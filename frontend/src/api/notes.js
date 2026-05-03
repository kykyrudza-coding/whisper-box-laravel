const BASE = '/api/notes'

function normalizeNote(raw) {
  return {
    id: raw.id,
    ...raw.attributes,
    likes_count: raw.attributes?.likes_count ?? 0,
  }
}

function normalizeResponse(json) {
  // JsonApiResource wraps single items in an extra `data` key
  const payload = json.data?.data ?? json.data

  if (Array.isArray(payload)) {
    return { ...json, data: payload.map(normalizeNote) }
  }
  if (payload?.attributes) {
    return { ...json, data: normalizeNote(payload) }
  }
  return json
}

async function request(url, options = {}) {
  const res = await fetch(url, {
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    ...options,
  })
  const json = await res.json()
  if (!res.ok) throw json
  return normalizeResponse(json)
}

export const getNotes = (sort = 'latest') =>
  request(`${BASE}?sort=${sort}`)

export const getNote = (id) =>
  request(`${BASE}/${id}`)

export const createNote = (body) =>
  request(BASE, { method: 'POST', body: JSON.stringify(body) })

export const likeNote = (id) =>
  request(`${BASE}/${id}/like`, { method: 'POST' })
