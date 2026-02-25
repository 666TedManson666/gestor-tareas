import axios from 'axios'

const http = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// ─── Tasks ───────────────────────────────────────────────────────────────────

export const tasksApi = {
  /** GET /api/tasks */
  getAll: () => http.get('/tasks'),

  /** POST /api/tasks */
  create: (payload) => http.post('/tasks', payload),

  /** PUT /api/tasks/:id */
  update: (id, payload) => http.put(`/tasks/${id}`, payload),

  /** DELETE /api/tasks/:id */
  remove: (id) => http.delete(`/tasks/${id}`),
}

// ─── Items ───────────────────────────────────────────────────────────────────

export const itemsApi = {
  /** POST /api/tasks/:taskId/items */
  create: (taskId, payload) => http.post(`/tasks/${taskId}/items`, payload),

  /** PUT /api/tasks/:taskId/items/:itemId */
  update: (taskId, itemId, payload) =>
    http.put(`/tasks/${taskId}/items/${itemId}`, payload),

  /** DELETE /api/tasks/:taskId/items/:itemId */
  remove: (taskId, itemId) => http.delete(`/tasks/${taskId}/items/${itemId}`),
}
