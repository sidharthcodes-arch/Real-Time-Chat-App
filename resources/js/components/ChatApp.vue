<script setup>
import { ref, nextTick, onMounted } from 'vue';

const STORAGE_KEY = 'chat_name';

const name = ref('');
const joined = ref(false);
const draft = ref('');
const messages = ref([]);
const sending = ref(false);
const listEl = ref(null);
const typingUser = ref('');
let typingTimeout = null;

onMounted(async () => {
    const saved = window.localStorage.getItem(STORAGE_KEY);
    if (saved) {
        name.value = saved;
        joined.value = true;
    }

    await fetchMessages();

    // -------------------------------------------------------------------------
    // TODO (real-time): subscribe to the chat channel here and push any
    // incoming message into `messages`. Until this is wired up, a second
    // browser will only see new messages after a manual reload.
    //
    // Watch out for showing a message twice: this client already appends its
    // own sent messages optimistically in send(), so decide how the sender
    // should handle (or skip) its own broadcast.
    //
    // window.Echo.channel('chat').listen('.message.sent', (e) => {
    //     messages.value.push(e.message ?? e);
    //     scrollToBottom();
    // });
    // -------------------------------------------------------------------------
    window.Echo.channel('chat').listen('.message.sent', (e) => {
        messages.value.push(e.message ?? e);
        scrollToBottom();
    });

    window.Echo.channel('chat').listen('.user.typing', (e) => {
        if (e.name !== name.value) {
            typingUser.value = e.name;
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                typingUser.value = '';
            }, 1000);
        }
    });
});

async function fetchMessages() {
    try {
        const { data } = await window.axios.get('/messages');
        messages.value = data;
        scrollToBottom();
    } catch (e) {
        console.error('Failed to load messages', e);
    }
}
function onTyping() {
    window.axios.post('/typing', { name: name.value });
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
        typingUser.value = '';
    }, 1000);
}

function join() {
    const trimmed = name.value.trim();
    if (!trimmed) return;
    name.value = trimmed;
    window.localStorage.setItem(STORAGE_KEY, trimmed);
    joined.value = true;
}

async function send() {
    const body = draft.value.trim();
    if (!body || sending.value) return;

    sending.value = true;
    try {
        const { data } = await window.axios.post('/messages', {
            name: name.value,
            body,
        });
        // Optimistic local append so the sender sees their message immediately.
        // messages.value.push(data);
        draft.value = '';
        scrollToBottom();
    } catch (e) {
        console.error('Failed to send message', e);
    } finally {
        sending.value = false;
    }
}


function scrollToBottom() {
    nextTick(() => {
        const el = listEl.value;
        if (el) el.scrollTop = el.scrollHeight;
    });
}

function timeLabel(ts) {
    if (!ts) return '';
    return new Date(ts).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
</script>

<template>
  <div class="shell">
    <!-- Name gate -->
    <div v-if="!joined" class="gate">
      <div class="gate-card">
        <h1>Join the chat</h1>
        <p>Pick a display name to start.</p>
        <input
          v-model="name"
          class="gate-input"
          type="text"
          maxlength="50"
          placeholder="Your name"
          @keyup.enter="join"
        />
        <button class="btn block" :disabled="!name.trim()" @click="join">Join</button>
      </div>
    </div>

    <!-- Chat -->
    <div v-else class="chat">
      <header class="chat-header">
        <div class="room">
          <span class="dot" title="Live connection not wired up yet"></span>
          <strong>General</strong>
        </div>
        <div class="me">{{ name }}</div>
      </header>

      <div ref="listEl" class="messages">
        <p v-if="messages.length === 0" class="empty">No messages yet. Say hi.</p>
        <div
          v-for="m in messages"
          :key="m.id"
          class="row"
          :class="{ mine: m.name === name }"
        >
          <div class="bubble">
            <div class="meta">
              <span class="author">{{ m.name }}</span>
              <span class="time">{{ timeLabel(m.created_at) }}</span>
            </div>
            <div class="body">{{ m.body }}</div>
          </div>
        </div>
      </div>
      <div class="typing" v-if="typingUser">
        {{ typingUser }} is typing...
    </div>

      <footer class="composer">
        <input
          @input="onTyping"
          v-model="draft"
          class="composer-input"
          type="text"
          maxlength="2000"
          placeholder="Type a message"
          @keyup.enter="send"
        />
        <button class="btn" :disabled="!draft.trim() || sending" @click="send">
          Send
        </button>
      </footer>
    </div>
  </div>
</template>

<style scoped>
.shell {
  height: 100%;
  display: flex;
  align-items: stretch;
  justify-content: center;
  background: #f3f4f6;
}

/* Name gate */
.gate {
  height: 100%;
  width: 100%;
  display: grid;
  place-items: center;
}
.gate-card {
  background: #fff;
  padding: 28px;
  border-radius: 14px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  width: 320px;
  text-align: center;
}
.gate-card h1 { margin: 0 0 6px; font-size: 20px; }
.gate-card p { margin: 0 0 16px; color: #6b7280; font-size: 14px; }
.gate-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  margin-bottom: 12px;
}

/* Chat */
.chat {
  width: 100%;
  max-width: 640px;
  height: 100%;
  display: flex;
  flex-direction: column;
  background: #fff;
  border-left: 1px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
}
.chat-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  border-bottom: 1px solid #e5e7eb;
}
.room { display: flex; align-items: center; gap: 8px; }
.dot { width: 8px; height: 8px; border-radius: 50%; background: #9ca3af; }
.me { font-size: 13px; color: #6b7280; }

.messages {
  flex: 1;
  overflow-y: auto;
  padding: 18px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.empty { color: #9ca3af; text-align: center; margin-top: 40px; font-size: 14px; }

.row { display: flex; }
.row.mine { justify-content: flex-end; }
.bubble {
  max-width: 75%;
  background: #e5e7eb;
  border-radius: 12px;
  padding: 8px 12px;
  color: #111827;
}
.row.mine .bubble { background: #2563eb; color: #fff; }
.meta { display: flex; gap: 8px; align-items: baseline; margin-bottom: 2px; }
.author { font-size: 12px; font-weight: 600; color: #374151; }
.row.mine .author { color: #dbeafe; }
.time { font-size: 11px; color: #9ca3af; }
.row.mine .time { color: #bfdbfe; }
.body { font-size: 14px; white-space: pre-wrap; word-break: break-word; }

.composer {
  display: flex;
  gap: 8px;
  padding: 14px 18px;
  border-top: 1px solid #e5e7eb;
}
.composer-input {
  flex: 1;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
}
.btn {
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  background: #2563eb;
  color: #fff;
  font-size: 14px;
  cursor: pointer;
}
.btn.block { width: 100%; }
.btn:disabled { opacity: 0.5; cursor: not-allowed; }
.typing {
    padding: 4px 18px;
    font-size: 12px;
    color: #9ca3af;
    font-style: italic;
}
</style>
