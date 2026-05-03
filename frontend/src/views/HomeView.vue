<template>
  <div>
    <div class="page-head">
      <div>
        <h1 class="page-title">Шепоти</h1>
        <p class="page-sub">Анонімні думки від людей навколо</p>
      </div>
      <button class="btn-new" @click="showForm = true">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Написати
      </button>
    </div>

    <div class="sort-bar">
      <button class="sort-btn" :class="{ active: sort === 'latest' }" @click="setSort('latest')">Нові</button>
      <button class="sort-btn" :class="{ active: sort === 'popular' }" @click="setSort('popular')">Популярні</button>
    </div>

    <div v-if="loading" class="state">
      <div class="spinner" />
    </div>

    <div v-else-if="error" class="state">
      <p class="state-text error-text">{{ error }}</p>
      <button class="btn-ghost" @click="load">Спробувати знову</button>
    </div>

    <div v-else-if="notes.length === 0" class="state">
      <p class="state-emoji">🌙</p>
      <p class="state-text">Тут ще тихо</p>
      <p class="state-sub">Залиш перший шепіт</p>
      <button class="btn-new" @click="showForm = true">Написати</button>
    </div>

    <div v-else class="list">
      <NoteCard v-for="note in notes" :key="note.id" :note="note" @liked="onLiked" />
    </div>

    <Teleport to="body">
      <NoteForm v-if="showForm" @close="showForm = false" @created="onCreated" />
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import NoteCard from '../components/NoteCard.vue'
import NoteForm from '../components/NoteForm.vue'
import { getNotes } from '../api/notes.js'

const notes = ref([])
const sort = ref('latest')
const loading = ref(false)
const error = ref('')
const showForm = ref(false)

async function load() {
  loading.value = true
  error.value = ''
  try {
    const res = await getNotes(sort.value)
    notes.value = res.data
  } catch {
    error.value = 'Не вдалось завантажити. Перевір чи запущено бекенд.'
  } finally {
    loading.value = false
  }
}

function setSort(value) {
  sort.value = value
  load()
}

function onCreated(note) {
  if (sort.value === 'latest') notes.value.unshift(note)
  else notes.value.push(note)
}

function onLiked(updated) {
  const idx = notes.value.findIndex((n) => n.id === updated.id)
  if (idx !== -1) notes.value[idx] = updated
}

onMounted(load)
</script>

<style scoped>
.page-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 24px;
  gap: 16px;
}

.page-title {
  font-size: 1.4rem;
  font-weight: 700;
  letter-spacing: -.02em;
  color: var(--text);
}

.page-sub {
  margin-top: 3px;
  font-size: 0.85rem;
  color: var(--text-muted);
}

.btn-new {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: var(--radius-sm);
  background: var(--accent);
  color: #fff;
  font-size: 0.85rem;
  font-weight: 500;
  white-space: nowrap;
  flex-shrink: 0;
  transition: background .12s;
}

.btn-new:hover { background: var(--accent-hover); }

.sort-bar {
  display: flex;
  gap: 2px;
  background: var(--surface-secondary);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 3px;
  width: fit-content;
  margin-bottom: 20px;
}

.sort-btn {
  padding: 5px 14px;
  border-radius: 6px;
  font-size: 0.82rem;
  color: var(--text-muted);
  transition: all .12s;
}

.sort-btn.active {
  background: var(--surface);
  color: var(--text);
  font-weight: 500;
  box-shadow: var(--shadow-sm);
}

.list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 80px 0;
  text-align: center;
}

.state-emoji { font-size: 2.5rem; }

.state-text {
  font-size: 1rem;
  font-weight: 500;
  color: var(--text);
}

.state-sub {
  font-size: 0.85rem;
  color: var(--text-muted);
  margin-top: -4px;
}

.error-text { color: var(--danger); }

.btn-ghost {
  margin-top: 8px;
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
