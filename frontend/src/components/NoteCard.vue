<template>
  <article class="card">
    <div class="card-top">
      <div class="meta">
        <span class="mood-dot" :style="{ background: moodColor }" />
        <span class="mood-label">{{ moodEmoji }} {{ moodName }}</span>
      </div>
      <time class="date">{{ formattedDate }}</time>
    </div>

    <router-link :to="`/notes/${note.id}`" class="title">{{ note.title }}</router-link>

    <p class="content">{{ note.content }}</p>

    <div class="card-bottom">
      <button class="like-btn" :class="{ liked }" @click.prevent="handleLike" :disabled="loading">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path v-if="liked" fill="currentColor" stroke="none" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
          <path v-else d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
        </svg>
        {{ note.likes_count }}
      </button>
      <router-link :to="`/notes/${note.id}`" class="read-link">Читати</router-link>
    </div>
  </article>
</template>

<script setup>
import { ref, computed } from 'vue'
import { likeNote } from '../api/notes.js'

const props = defineProps({ note: { type: Object, required: true } })
const emit = defineEmits(['liked'])

const MOODS = {
  happy:   { emoji: '😊', label: 'Радість',       color: 'var(--happy)' },
  sad:     { emoji: '😢', label: 'Сум',            color: 'var(--sad)' },
  neutral: { emoji: '😐', label: 'Нейтрально',    color: 'var(--neutral)' },
  angry:   { emoji: '😠', label: 'Злість',         color: 'var(--angry)' },
  confused:{ emoji: '😕', label: 'Розгубленість', color: 'var(--confused)' },
}

const mood = computed(() => MOODS[props.note.mood] ?? { emoji: '💭', label: props.note.mood, color: 'var(--neutral)' })
const moodEmoji  = computed(() => mood.value.emoji)
const moodName   = computed(() => mood.value.label)
const moodColor  = computed(() => mood.value.color)

const formattedDate = computed(() =>
  new Date(props.note.created_at).toLocaleDateString('uk-UA', {
    day: 'numeric', month: 'short', year: 'numeric',
  })
)

const liked = ref(false)
const loading = ref(false)

async function handleLike() {
  if (loading.value || liked.value) return
  loading.value = true
  try {
    const res = await likeNote(props.note.id)
    liked.value = true
    emit('liked', res.data)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 20px 22px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: box-shadow .15s, border-color .15s;
}

.card:hover {
  box-shadow: var(--shadow-md);
  border-color: var(--border);
}

.card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.meta {
  display: flex;
  align-items: center;
  gap: 6px;
}

.mood-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  flex-shrink: 0;
}

.mood-label {
  font-size: 0.78rem;
  color: var(--text-muted);
}

.date {
  font-size: 0.75rem;
  color: var(--text-placeholder);
}

.title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text);
  line-height: 1.35;
  letter-spacing: -.01em;
  transition: color .12s;
}

.title:hover {
  color: var(--accent);
}

.content {
  font-size: 0.875rem;
  color: var(--text-muted);
  line-height: 1.65;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 4px;
}

.like-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 0.8rem;
  color: var(--text-muted);
  padding: 4px 10px 4px 8px;
  border-radius: 6px;
  transition: all .12s;
}

.like-btn:hover:not(:disabled) {
  background: var(--danger-subtle);
  color: var(--danger);
}

.like-btn.liked {
  color: var(--danger);
}

.like-btn:disabled { opacity: .5; }

.read-link {
  font-size: 0.78rem;
  color: var(--text-muted);
  padding: 4px 8px;
  border-radius: 6px;
  transition: all .12s;
}

.read-link:hover {
  background: var(--surface-secondary);
  color: var(--text);
}
</style>
