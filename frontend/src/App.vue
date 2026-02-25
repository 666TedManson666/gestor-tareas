<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-brand-orange flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
          </div>
          <span class="text-lg font-bold text-gray-800">Gestor de Tareas</span>
        </div>

        <button
          @click="openCreateModal"
          class="flex items-center gap-2 bg-brand-orange text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Nueva tarea
        </button>
      </div>
    </header>

    <!-- Main -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Filtros + estadísticas -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <!-- Stats -->
        <div class="flex gap-4">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="bg-white rounded-xl border border-gray-100 px-4 py-2 text-center shadow-sm"
          >
            <p class="text-xl font-bold" :class="stat.color">{{ stat.count }}</p>
            <p class="text-xs text-gray-500">{{ stat.label }}</p>
          </div>
        </div>

        <!-- Filtros -->
        <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
          <button
            v-for="filter in filters"
            :key="filter.value"
            @click="activeFilter = filter.value"
            class="px-3 py-1.5 rounded-lg text-sm font-medium transition"
            :class="activeFilter === filter.value
              ? 'bg-brand-orange text-white shadow-sm'
              : 'text-gray-600 hover:bg-gray-100'"
          >
            {{ filter.label }}
          </button>
        </div>
      </div>

      <!-- Lista de tareas -->
      <TaskList
        :tasks="filteredTasks"
        :loading="loading"
        :adding-item-id="addingItemId"
        :loading-status-id="loadingStatusId"
        @edit="openEditModal"
        @delete="confirmDelete"
        @toggle-item="handleToggleItem"
        @delete-item="handleDeleteItem"
        @add-item="handleAddItem"
        @mark-done="handleMarkDone"
      />
    </main>

    <!-- Modal crear/editar -->
    <TaskForm
      v-if="showModal"
      :task="editingTask"
      :loading="savingTask"
      @submit="handleSaveTask"
      @cancel="closeModal"
    />

    <!-- Toast notifications -->
    <ToastNotification />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TaskList         from './components/TaskList.vue'
import TaskForm         from './components/TaskForm.vue'
import ToastNotification from './components/ToastNotification.vue'
import { tasksApi, itemsApi } from './api/index.js'
import { useToast }     from './composables/useToast.js'

const { success, error } = useToast()

// ─── State ───────────────────────────────────────────────────────────────────

const tasks          = ref([])
const loading        = ref(false)
const showModal      = ref(false)
const editingTask    = ref(null)
const savingTask     = ref(false)
const addingItemId   = ref(null)
const loadingStatusId = ref(null)
const activeFilter   = ref('all')

// ─── Filtros ─────────────────────────────────────────────────────────────────

const filters = [
  { value: 'all',        label: 'Todas'      },
  { value: 'pending',    label: 'Pendientes' },
  { value: 'inProgress', label: 'En progreso'},
  { value: 'done',       label: 'Terminadas' },
]

const filteredTasks = computed(() => {
  if (activeFilter.value === 'all') return tasks.value
  return tasks.value.filter((t) => t.status === activeFilter.value)
})

// ─── Stats ───────────────────────────────────────────────────────────────────

const stats = computed(() => [
  {
    label: 'Total',
    count: tasks.value.length,
    color: 'text-gray-700',
  },
  {
    label: 'Pendientes',
    count: tasks.value.filter((t) => t.status === 'pending').length,
    color: 'text-gray-500',
  },
  {
    label: 'En progreso',
    count: tasks.value.filter((t) => t.status === 'inProgress').length,
    color: 'text-orange-600',
  },
  {
    label: 'Terminadas',
    count: tasks.value.filter((t) => t.status === 'done').length,
    color: 'text-green-600',
  },
])

// ─── API Calls ───────────────────────────────────────────────────────────────

async function fetchTasks() {
  loading.value = true
  try {
    const { data } = await tasksApi.getAll()
    tasks.value = data.data
  } catch {
    error('No se pudo cargar las tareas. Verifica que el servidor esté activo.')
  } finally {
    loading.value = false
  }
}

async function handleSaveTask(payload) {
  savingTask.value = true
  try {
    if (editingTask.value) {
      const { data } = await tasksApi.update(editingTask.value.id, payload)
      updateTaskInList(data.data)
      success('Tarea actualizada correctamente.')
    } else {
      const { data } = await tasksApi.create(payload)
      tasks.value.unshift(data.data)
      success('Tarea creada correctamente.')
    }
    closeModal()
  } catch (err) {
    const msg = err.response?.data?.message ?? 'Error al guardar la tarea.'
    error(msg)
  } finally {
    savingTask.value = false
  }
}

async function confirmDelete(task) {
  if (!confirm(`¿Eliminar la tarea "${task.title}"?`)) return
  try {
    await tasksApi.remove(task.id)
    tasks.value = tasks.value.filter((t) => t.id !== task.id)
    success('Tarea eliminada.')
  } catch {
    error('No se pudo eliminar la tarea.')
  }
}

async function handleMarkDone(task) {
  loadingStatusId.value = task.id
  try {
    const { data } = await tasksApi.update(task.id, { status: 'done' })
    updateTaskInList(data.data)
    success('¡Tarea marcada como terminada!')
  } catch (err) {
    const msg = err.response?.data?.message
      ?? 'No se puede marcar la tarea como terminada.'
    error(msg)
  } finally {
    loadingStatusId.value = null
  }
}

async function handleAddItem(task, payload) {
  addingItemId.value = task.id
  try {
    const { data } = await itemsApi.create(task.id, payload)
    const t = tasks.value.find((t) => t.id === task.id)
    if (t) t.items.push(data.data)
    success('Ítem agregado.')
  } catch (err) {
    const msg = err.response?.data?.message ?? 'No se pudo agregar el ítem.'
    error(msg)
  } finally {
    addingItemId.value = null
  }
}

async function handleToggleItem(task, item) {
  try {
    const { data } = await itemsApi.update(task.id, item.id, {
      is_completed: !item.is_completed,
    })
    const t = tasks.value.find((t) => t.id === task.id)
    if (t) {
      const idx = t.items.findIndex((i) => i.id === item.id)
      if (idx !== -1) t.items[idx] = data.data
    }
  } catch {
    error('No se pudo actualizar el ítem.')
  }
}

async function handleDeleteItem(task, item) {
  try {
    await itemsApi.remove(task.id, item.id)
    const t = tasks.value.find((t) => t.id === task.id)
    if (t) t.items = t.items.filter((i) => i.id !== item.id)
    success('Ítem eliminado.')
  } catch {
    error('No se pudo eliminar el ítem.')
  }
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function updateTaskInList(updatedTask) {
  const idx = tasks.value.findIndex((t) => t.id === updatedTask.id)
  if (idx !== -1) tasks.value[idx] = updatedTask
}

function openCreateModal() {
  editingTask.value = null
  showModal.value   = true
}

function openEditModal(task) {
  editingTask.value = { ...task }
  showModal.value   = true
}

function closeModal() {
  showModal.value   = false
  editingTask.value = null
}

// ─── Lifecycle ───────────────────────────────────────────────────────────────

onMounted(fetchTasks)
</script>
