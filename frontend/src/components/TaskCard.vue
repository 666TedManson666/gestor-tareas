<template>
  <article
    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition hover:shadow-md"
    :class="{ 'opacity-75': task.status === 'done' }"
  >
    <!-- Color bar superior según estado -->
    <div class="h-1 w-full" :class="statusBarClass"></div>

    <div class="p-5">
      <!-- Cabecera -->
      <div class="flex items-start justify-between gap-2 mb-3">
        <div class="flex-1 min-w-0">
          <h3
            class="font-semibold text-gray-800 leading-snug"
            :class="{ 'line-through text-gray-500': task.status === 'done' }"
          >
            {{ task.title }}
          </h3>
          <p v-if="task.description" class="text-xs text-gray-500 mt-0.5 line-clamp-2">
            {{ task.description }}
          </p>
        </div>

        <!-- Acciones -->
        <div class="flex gap-1 flex-shrink-0">
          <button
            @click="$emit('edit', task)"
            class="p-1.5 rounded-lg text-gray-400 hover:text-brand-blue hover:bg-blue-50 transition"
            title="Editar tarea"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </button>
          <button
            @click="$emit('delete', task)"
            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
            title="Eliminar tarea"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Estado badge + progreso -->
      <div class="flex items-center gap-3 mb-4">
        <span
          class="text-xs font-medium px-2.5 py-1 rounded-full"
          :class="statusBadgeClass"
        >
          {{ statusLabel }}
        </span>

        <div v-if="task.items.length" class="flex-1 flex items-center gap-2">
          <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-500"
              :class="progressBarColor"
              :style="{ width: `${completionPercent}%` }"
            />
          </div>
          <span class="text-xs text-gray-500 whitespace-nowrap">
            {{ completedCount }}/{{ task.items.length }}
          </span>
        </div>
      </div>

      <!-- Lista de ítems -->
      <ItemList
        :items="task.items"
        @toggle-item="(item) => $emit('toggle-item', task, item)"
        @delete-item="(item) => $emit('delete-item', task, item)"
      />

      <!-- Formulario nuevo ítem -->
      <ItemForm
        :loading="addingItem"
        @submit="(payload) => $emit('add-item', task, payload)"
        class="mt-3"
      />

      <!-- Botón marcar como terminada -->
      <div class="mt-4 pt-3 border-t border-gray-100">
        <div class="relative group/done inline-block w-full">
          <button
            @click="handleMarkDone"
            :disabled="!canMarkDone || task.status === 'done' || loadingStatus"
            class="w-full rounded-lg py-2 text-sm font-medium transition flex items-center justify-center gap-2"
            :class="doneButtonClass"
          >
            <span
              v-if="loadingStatus"
              class="inline-block w-4 h-4 border-2 border-current/30 border-t-current rounded-full animate-spin"
            />
            <template v-else>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </template>
            {{ task.status === 'done' ? 'Tarea terminada' : 'Marcar como terminada' }}
          </button>

          <!-- Tooltip cuando está deshabilitado -->
          <div
            v-if="!canMarkDone && task.status !== 'done'"
            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 hidden group-hover/done:block"
          >
            <div class="bg-gray-800 text-white text-xs rounded-lg px-3 py-2 text-center leading-snug shadow-lg">
              Completa todos los ítems antes de marcar la tarea como terminada.
              <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed, ref } from 'vue'
import ItemList from './ItemList.vue'
import ItemForm from './ItemForm.vue'

const props = defineProps({
  task:        { type: Object, required: true },
  addingItem:  { type: Boolean, default: false },
  loadingStatus: { type: Boolean, default: false },
})

const emit = defineEmits(['edit', 'delete', 'toggle-item', 'delete-item', 'add-item', 'mark-done'])

// ─── Computed ────────────────────────────────────────────────────────────────

const completedCount = computed(() =>
  props.task.items.filter((i) => i.is_completed).length
)

const completionPercent = computed(() =>
  props.task.items.length
    ? Math.round((completedCount.value / props.task.items.length) * 100)
    : 0
)

const canMarkDone = computed(() => {
  const items = props.task.items
  return items.length === 0 || items.every((i) => i.is_completed)
})

const statusLabel = computed(() => ({
  pending:    'Pendiente',
  inProgress: 'En progreso',
  done:       'Terminada',
}[props.task.status] ?? props.task.status))

const statusBarClass = computed(() => ({
  pending:    'bg-gray-300',
  inProgress: 'bg-brand-orange',
  done:       'bg-green-500',
}[props.task.status]))

const statusBadgeClass = computed(() => ({
  pending:    'bg-gray-100 text-gray-600',
  inProgress: 'bg-orange-100 text-orange-700',
  done:       'bg-green-100 text-green-700',
}[props.task.status]))

const progressBarColor = computed(() =>
  completionPercent.value === 100 ? 'bg-green-500' : 'bg-brand-orange'
)

const doneButtonClass = computed(() => {
  if (props.task.status === 'done') {
    return 'bg-green-100 text-green-700 cursor-default'
  }
  if (!canMarkDone.value) {
    return 'bg-gray-100 text-gray-400 cursor-not-allowed'
  }
  return 'bg-green-600 text-white hover:bg-green-700'
})

// ─── Actions ─────────────────────────────────────────────────────────────────

function handleMarkDone() {
  if (!canMarkDone.value || props.task.status === 'done') return
  emit('mark-done', props.task)
}
</script>
