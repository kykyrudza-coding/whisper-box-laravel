<template>
  <div>
    <router-link to="/" class="back-link">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
      Назад
    </router-link>

    <div v-if="loading" class="state">
      <div class="spinner" />
    </div>

    <div v-else-if="error" class="state">
      <p class="state-text error-text">{{ error }}</p>
      <router-link to="/" class="btn-ghost">На головну</router-link>
    </div>

    <article v-else-if="note" class="note">
      <div class="note-meta">
        <div class="meta-left">
          <span class="mood-dot" :style="{ background: moodColor }" />
          <span class="mood-label">{{ moodEmoji }} {{ moodName }}</span>
        </div>
        <time class="date">{{ formattedDate }}</time>
      </div>

      <h1 class="note-title">{{ note.title }}</h1>
      <p class="note-content">{{ note.content }}</p>

      <div class="note-footer">
        <button class="like-btn" :class="{ liked }" @click="handleLike" :disabled="likeLoading">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path v-if="liked" fill="currentColor" stroke="none" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            <path v-else d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
          </svg>
          {{ note.likes_count }} {{ likesWord }}
        </button>
      </div>
    </article>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getNote, likeNote } from '../api/notes.js'

const route = useRoute()
const note = ref(null)
const loading = ref(false)
const error = ref('')
const liked = ref(false)
const likeLoading = ref(false)

const MOODS = {
  happy:    { emoji: '😊', label: 'Радість',      color: 'var(--happy)' },
  sad:      { emoji: '😢', label: 'Сум',           color: 'var(--sad)' },
  neutral:  { emoji: '😐', label: 'Нейтрально',   color: 'var(--neutral)' },
  angry:    { emoji: '😠', label: 'Злість',        color: 'var(--angry)' },
  confused: { emoji: '😕', label: 'Розгубленість', color: 'var(--confused)' },
}

const mood = computed(() => MOODS[note.value?.mood] ?? { emoji: '💭', label: note.value?.mood, color: 'var(--neutral)' })
const moodEmoji = computed(() => mood.value.emoji)
const moodName  = computed(() => mood.value.label)
const moodColor = computed(() => mood.value.color)

const formattedDate = computed(() =>
  note.value
    ? new Date(note.value.created_at).toLocaleDateString('uk-UA', { day: 'numeric', month: 'long', year: 'numeric' })
    : ''
)

const likesWord = computed(() => {
  const n = note.value?.likes_count ?? 0
  if (n % 10 === 1 && n % 100 !== 11) return 'лайк'
  if ([2,3,4].includes(n % 10) && ![12,13,14].includes(n % 100)) return 'лайки'
  return 'лайків'
})

async function load() {
  loading.value = true
  error.value = ''
  try {
    const res = await getNote(route.params.id)
    note.value = res.data
  } catch {
    error.value = 'Нотатку не знайдено або стався збій.'
  } finally {
    loading.value = false
  }
}

async function handleLike() {
  if (likeLoading.value || liked.value) return
  likeLoading.value = true
  try {
    const res = await likeNote(note.value.id)
    note.value = res.data
    liked.value = true
  } finally {
    likeLoading.value = false
  }
}

onMounted(load)
</script>

<style scoped>
.back-link {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.83rem;
  color: var(--text-muted);
  margin-bottom: 28px;
  padding: 4px 8px;
  border-radius: 6px;
  margin-left: -8px;
  transition: all .12s;
}

.back-link:hover {
  background: var(--surface-secondary);
  color: var(--text);
}

.note {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 28px 32px 24px;
  box-shadow: var(--shadow-sm);
}

.note-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.meta-left {
  display: flex;
  align-items: center;
  gap: 6px;
}

.mood-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.mood-label {
  font-size: 0.8rem;
  color: var(--text-muted);
}

.date {
  font-size: 0.78rem;
  color: var(--text-placeholder);
}

.note-title {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: -.02em;
  line-height: 1.25;
  margin-bottom: 16px;
}

.note-content {
  font-size: 0.95rem;
  line-height: 1.8;
  color: var(--text-muted);
  white-space: pre-wrap;
}

.note-footer {
  margin-top: 28px;
  padding-top: 20px;
  border-top: 1px solid var(--border-subtle);
}

.like-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 16px;
  border-radius: 8px;
  border: 1px solid var(--border);
  font-size: 0.85rem;
  color: var(--text-muted);
  transition: all .12s;
}

.like-btn:hover:not(:disabled):not(.liked) {
  border-color: var(--danger);
  background: var(--danger-subtle);
  color: var(--danger);
}

.like-btn.liked {
  border-color: var(--danger);
  color: var(--danger);
  background: var(--danger-subtle);
}

.like-btn:disabled { opacity: .5; }

.state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 80px 0;
  text-align: center;
}

.state-text { font-size: 0.95rem; color: var(--text-muted); }
.error-text { color: var(--danger); }

.btn-ghost {
  padding: 7px 16px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
  font-size: 0.82rem;
  color: var(--text-muted);
  transition: all .12s;
}

.btn-ghost:hover {
  background: var(--surface-secondary);
  color: var(--text);
}

.spinner {
  width: 28px;
  height: 28px;
  border: 2px solid var(--border);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: spin .65s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>
