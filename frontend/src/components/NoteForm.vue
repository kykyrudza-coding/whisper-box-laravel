<template>
  <div class="overlay" @click.self="$emit('close')">
    <div class="modal" role="dialog" aria-modal="true">
      <div class="modal-header">
        <h2 class="modal-title">Новий шепіт</h2>
        <button class="close-btn" @click="$emit('close')" aria-label="Закрити">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>

      <form @submit.prevent="submit" class="form" novalidate>
        <div class="field">
          <label for="title">Заголовок</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            placeholder="Про що хочеш розповісти?"
            :class="{ invalid: errors.title }"
            autofocus
          />
          <p v-if="errors.title" class="field-error">{{ errors.title[0] }}</p>
        </div>

        <div class="field">
          <label for="content">Думка</label>
          <textarea
            id="content"
            v-model="form.content"
            placeholder="Говори відверто — ніхто не знає хто ти..."
            rows="5"
            :class="{ invalid: errors.content }"
          />
          <p v-if="errors.content" class="field-error">{{ errors.content[0] }}</p>
        </div>

        <div class="field">
          <label>Настрій</label>
          <div class="moods">
            <button
              v-for="m in MOODS" :key="m.value"
              type="button"
              class="mood-chip"
              :class="{ active: form.mood === m.value }"
              @click="form.mood = m.value"
            >
              {{ m.emoji }} {{ m.label }}
            </button>
          </div>
          <p v-if="errors.mood" class="field-error">{{ errors.mood[0] }}</p>
        </div>

        <p v-if="serverError" class="server-error">{{ serverError }}</p>

        <div class="form-actions">
          <button type="button" class="btn-secondary" @click="$emit('close')">Скасувати</button>
          <button type="submit" class="btn-primary" :disabled="loading">
            {{ loading ? 'Надсилання…' : 'Надіслати' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { createNote } from '../api/notes.js'

const emit = defineEmits(['close', 'created'])

const MOODS = [
  { value: 'happy',    emoji: '😊', label: 'Радість' },
  { value: 'sad',      emoji: '😢', label: 'Сум' },
  { value: 'neutral',  emoji: '😐', label: 'Нейтрально' },
  { value: 'angry',    emoji: '😠', label: 'Злість' },
  { value: 'confused', emoji: '😕', label: 'Розгубленість' },
]

const form = reactive({ title: '', content: '', mood: 'neutral' })
const errors = ref({})
const serverError = ref('')
const loading = ref(false)

async function submit() {
  errors.value = {}
  serverError.value = ''
  loading.value = true
  try {
    const res = await createNote(form)
    emit('created', res.data)
    emit('close')
  } catch (err) {
    if (err?.errors) errors.value = err.errors
    else serverError.value = 'Щось пішло не так. Спробуй ще раз.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.4);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
  padding: 16px;
}

.modal {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  width: 100%;
  max-width: 500px;
  box-shadow: var(--shadow-md);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px 0;
}

.modal-title {
  font-size: 1.05rem;
  font-weight: 600;
  letter-spacing: -.01em;
}

.close-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  color: var(--text-muted);
  transition: background .12s, color .12s;
}

.close-btn:hover {
  background: var(--surface-secondary);
  color: var(--text);
}

.form {
  padding: 20px 24px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

label {
  font-size: 0.8rem;
  font-weight: 500;
  color: var(--text-muted);
}

input, textarea {
  background: var(--surface-secondary);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text);
  font: inherit;
  font-size: 0.9rem;
  padding: 9px 12px;
  resize: vertical;
  outline: none;
  transition: border-color .12s, box-shadow .12s;
}

input::placeholder, textarea::placeholder {
  color: var(--text-placeholder);
}

input:focus, textarea:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 12%, transparent);
  background: var(--surface);
}

input.invalid, textarea.invalid {
  border-color: var(--danger);
}

.field-error {
  font-size: 0.78rem;
  color: var(--danger);
}

.moods {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.mood-chip {
  padding: 5px 12px;
  border-radius: 20px;
  border: 1px solid var(--border);
  background: var(--surface-secondary);
  font-size: 0.8rem;
  color: var(--text-muted);
  transition: all .12s;
}

.mood-chip:hover {
  border-color: var(--accent);
  color: var(--text);
}

.mood-chip.active {
  border-color: var(--accent);
  background: var(--accent-subtle);
  color: var(--accent);
  font-weight: 500;
}

.server-error {
  padding: 10px 12px;
  background: var(--danger-subtle);
  border: 1px solid color-mix(in srgb, var(--danger) 25%, transparent);
  border-radius: var(--radius-sm);
  color: var(--danger);
  font-size: 0.82rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 4px;
}

.btn-secondary {
  padding: 8px 16px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
  font-size: 0.875rem;
  color: var(--text-muted);
  transition: all .12s;
}

.btn-secondary:hover {
  background: var(--surface-secondary);
  color: var(--text);
}

.btn-primary {
  padding: 8px 20px;
  border-radius: var(--radius-sm);
  background: var(--accent);
  color: #fff;
  font-size: 0.875rem;
  font-weight: 500;
  transition: background .12s, opacity .12s;
}

.btn-primary:hover:not(:disabled) {
  background: var(--accent-hover);
}

.btn-primary:disabled { opacity: .55; }
</style>
