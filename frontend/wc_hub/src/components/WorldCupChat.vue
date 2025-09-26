<template>
  <!-- FAB / Toggle -->
  <div class="fixed z-50 bottom-4 right-4 w-[22rem] max-w-[92vw]">
    <button
      class="w-full rounded-2xl shadow-lg px-4 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-semibold hover:brightness-105 active:scale-[0.99] transition"
      @click="open = !open"
      :aria-expanded="open.toString()"
    >
      {{ open ? 'Close World Cup Chat' : 'Ask about the World Cup' }}
    </button>

    <!-- Panel -->
    <div
      v-if="open"
      class="mt-3 bg-white rounded-2xl shadow-2xl overflow-hidden border border-emerald-100"
    >
      <!-- Header -->
      <div class="relative">
        <div class="h-14 bg-gradient-to-r from-emerald-600 to-emerald-500"></div>
        <div class="absolute inset-0 flex items-center gap-3 px-4">
          <div class="h-9 w-9 rounded-full bg-white/90 flex items-center justify-center shadow">
            <!-- tiny ball icon -->
            <span aria-hidden="true">⚽️</span>
          </div>
          <div class="text-white">
            <h3 class="text-sm font-semibold leading-4">World Cup Q&A</h3>
            <p class="text-xs/5 opacity-90">Powered by OpenAI (on-topic answers)</p>
          </div>
        </div>
      </div>

      <!-- Messages -->
      <div ref="scrollBox" class="max-h-[460px] min-h-[260px] p-3 overflow-y-auto bg-gradient-to-b from-emerald-50/40 to-white">
        <ul class="space-y-3">
          <li v-for="(m, i) in messages" :key="i" class="flex gap-2"
              :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
            <!-- avatar -->
            <div v-if="m.role !== 'user'" class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
              ⚽
            </div>
            <div v-else class="h-8 w-8 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center shrink-0">
              😊
            </div>

            <!-- bubble -->
            <div
              class="max-w-[75%] px-3 py-2 rounded-2xl text-sm shadow-sm"
              :class="m.role === 'user'
                ? 'bg-emerald-600 text-white rounded-br-sm'
                : 'bg-white text-gray-900 border border-gray-100 rounded-bl-sm'"
            >
              <p class="whitespace-pre-wrap break-words">{{ m.content }}</p>
              <div class="mt-1 text-[10px] opacity-60"
                   :class="m.role === 'user' ? 'text-emerald-50' : 'text-gray-500'">
                {{ m.time }}
              </div>
            </div>
          </li>

          <!-- typing indicator -->
          <li v-if="loading" class="flex items-end gap-2">
            <div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center">⚽</div>
            <div class="px-3 py-2 rounded-2xl bg-white border border-gray-100 shadow-sm">
              <span class="typing">
                <span class="dot"></span><span class="dot"></span><span class="dot"></span>
              </span>
            </div>
          </li>
        </ul>
      </div>

      <!-- Input -->
      <form class="p-3 border-t bg-white" @submit.prevent="send">
        <div class="flex items-center gap-2">
          <input
            v-model.trim="input"
            type="text"
            inputmode="search"
            autocomplete="off"
            placeholder="e.g., Who won the 2014 final?"
            class="flex-1 rounded-xl border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
            :disabled="loading"
            @keydown.enter.exact.prevent="send"
          />
          <button
            class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:brightness-105 active:scale-[0.99] transition"
            :disabled="!input || loading"
          >
            Send
          </button>
        </div>
        <div class="mt-2 text-[11px] text-gray-500 flex items-center gap-2">
          <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
          On-topic only (FIFA World Cup).
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue'

// open/close
const open = ref(true)

// messages with friendly opener
const messages = ref([
  { role: 'assistant', content: 'Hi! Ask me anything about the FIFA World Cup—history, fixtures, records, rules.', time: now() }
])

const input = ref('')
const loading = ref(false)
const scrollBox = ref(null)

function now() {
  const d = new Date()
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

async function scrollToBottom() {
  await nextTick()
  if (scrollBox.value) scrollBox.value.scrollTop = scrollBox.value.scrollHeight
}
watch([messages, loading], scrollToBottom, { deep: true })

const API_BASE = import.meta.env.VITE_API_BASE || ''

async function send() {
  if (!input.value) return
  const userMsg = { role: 'user', content: input.value, time: now() }
  messages.value.push(userMsg)
  input.value = ''
  loading.value = true

  try {
    const res = await fetch(`${API_BASE}/api/chat`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        // send only role/content to backend; time is just for UI
        messages: messages.value.map(({ role, content }) => ({ role, content }))
      })
    })
    const data = await res.json()

    if (!res.ok || !data?.message?.content) {
      messages.value.push({
        role: 'assistant',
        content: 'Sorry, I could not answer that.',
        time: now()
      })
    } else {
      messages.value.push({
        role: 'assistant',
        content: data.message.content,
        time: now()
      })
    }
  } catch (e) {
    messages.value.push({ role: 'assistant', content: 'Network error. Please try again.', time: now() })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* subtle typing animation */
.typing .dot {
  display: inline-block;
  width: 6px;
  height: 6px;
  margin-right: 4px;
  border-radius: 9999px;
}
.typing .dot:nth-child(1),
.typing .dot:nth-child(2),
.typing .dot:nth-child(3) { background: #10b981; opacity: .6; animation: bounce 1.2s infinite; }
.typing .dot:nth-child(2) { animation-delay: .15s; }
.typing .dot:nth-child(3) { animation-delay: .3s; }

@keyframes bounce {
  0%, 80%, 100% { transform: translateY(0); opacity:.6; }
  40% { transform: translateY(-4px); opacity:1; }
}
</style>
