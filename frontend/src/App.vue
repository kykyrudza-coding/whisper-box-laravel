<template>
  <div class="app">
    <header class="header">
      <router-link to="/" class="logo">
        <span class="logo-icon">🤫</span>
        <span class="logo-text">WhisperBox</span>
      </router-link>
      <button class="theme-toggle" @click="toggleTheme" :aria-label="isDark ? 'Світла тема' : 'Темна тема'">
        <span v-if="isDark">☀️</span>
        <span v-else>🌙</span>
      </button>
    </header>
    <main class="main">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

const isDark = ref(false)

function applyTheme(dark) {
  document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light')
}

function toggleTheme() {
  isDark.value = !isDark.value
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

onMounted(() => {
  const saved = localStorage.getItem('theme')
  if (saved) {
    isDark.value = saved === 'dark'
  } else {
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
  }
})

watch(isDark, applyTheme, { immediate: true })
</script>

<style>
*, *::before, *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

:root,
[data-theme="light"] {
  --bg: #faf9f7;
  --surface: #ffffff;
  --surface-secondary: #f5f4f1;
  --border: #e8e4de;
  --border-subtle: #f0ede8;
  --text: #1a1814;
  --text-muted: #78716c;
  --text-placeholder: #b5afa8;
  --accent: #c96c2a;
  --accent-hover: #b05d22;
  --accent-subtle: #fdf4ec;
  --danger: #c0392b;
  --danger-subtle: #fef2f2;
  --happy: #b45309;
  --sad: #1d6fa4;
  --neutral: #6b7280;
  --angry: #b91c1c;
  --confused: #7c3aed;
  --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
  --shadow-md: 0 4px 12px rgba(0,0,0,.08), 0 2px 4px rgba(0,0,0,.04);
  --radius-sm: 8px;
  --radius: 12px;
  --radius-lg: 16px;
}

[data-theme="dark"] {
  --bg: #1c1b19;
  --surface: #252320;
  --surface-secondary: #2e2c29;
  --border: #3a3733;
  --border-subtle: #302e2b;
  --text: #edeae4;
  --text-muted: #9c9288;
  --text-placeholder: #5c5550;
  --accent: #d4700e;
  --accent-hover: #e07d1a;
  --accent-subtle: #2a1e0e;
  --danger: #e05252;
  --danger-subtle: #2a1212;
  --happy: #d4870e;
  --sad: #4a9fd4;
  --neutral: #9ca3af;
  --angry: #e05252;
  --confused: #a78bfa;
  --shadow-sm: 0 1px 3px rgba(0,0,0,.3);
  --shadow-md: 0 4px 16px rgba(0,0,0,.4);
}

html {
  background: var(--bg);
  transition: background .2s;
}

body {
  background: var(--bg);
  color: var(--text);
  font-family: 'Söhne', 'ui-sans-serif', system-ui, -apple-system, 'Segoe UI', sans-serif;
  font-size: 15px;
  line-height: 1.6;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
  transition: background .2s, color .2s;
}

a {
  color: inherit;
  text-decoration: none;
}

button {
  cursor: pointer;
  border: none;
  background: none;
  font: inherit;
  color: inherit;
}
</style>

<style scoped>
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.header {
  position: sticky;
  top: 0;
  z-index: 50;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  padding: 0 24px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  backdrop-filter: blur(8px);
}

.logo {
  display: flex;
  align-items: center;
  gap: 8px;
}

.logo-icon {
  font-size: 1.25rem;
  line-height: 1;
}

.logo-text {
  font-size: 1.05rem;
  font-weight: 600;
  color: var(--text);
  letter-spacing: -.01em;
}

.theme-toggle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  color: var(--text-muted);
  transition: background .15s;
}

.theme-toggle:hover {
  background: var(--surface-secondary);
}

.main {
  flex: 1;
  width: 100%;
  max-width: 680px;
  margin: 0 auto;
  padding: 40px 20px 80px;
}
</style>
