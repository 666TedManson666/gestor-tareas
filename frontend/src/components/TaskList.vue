<template>
  <div>
    <!-- Estado vacío -->
    <div
      v-if="tasks.length === 0 && !loading"
      class="flex flex-col items-center justify-center py-20 text-gray-400"
    >
      <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
      </svg>
      <p class="text-lg font-medium">No hay tareas</p>
      <p class="text-sm mt-1">Crea tu primera tarea con el botón de arriba.</p>
    </div>

    <!-- Skeleton cargando -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
      <div
        v-for="n in 3"
        :key="n"
        class="bg-white rounded-2xl border border-gray-100 p-5 animate-pulse"
      >
        <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
        <div class="h-3 bg-gray-100 rounded w-full mb-2"></div>
        <div class="h-3 bg-gray-100 rounded w-2/3"></div>
      </div>
    </div>

    <!-- Grid de tareas -->
    <TransitionGroup
      v-if="!loading && tasks.length"
      name="task-list"
      tag="div"
      class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"
    >
      <TaskCard
        v-for="task in tasks"
        :key="task.id"
        :task="task"
        :adding-item="addingItemId === task.id"
        :loading-status="loadingStatusId === task.id"
        @edit="$emit('edit', $event)"
        @delete="$emit('delete', $event)"
        @toggle-item="(task, item) => $emit('toggle-item', task, item)"
        @delete-item="(task, item) => $emit('delete-item', task, item)"
        @add-item="(task, payload) => $emit('add-item', task, payload)"
        @mark-done="$emit('mark-done', $event)"
      />
    </TransitionGroup>
  </div>
</template>

<script setup>
import TaskCard from './TaskCard.vue'

defineProps({
  tasks:          { type: Array,   default: () => [] },
  loading:        { type: Boolean, default: false },
  addingItemId:   { type: Number,  default: null },
  loadingStatusId:{ type: Number,  default: null },
})

defineEmits(['edit', 'delete', 'toggle-item', 'delete-item', 'add-item', 'mark-done'])
</script>

<style scoped>
.task-list-enter-active,
.task-list-leave-active {
  transition: all 0.3s ease;
}
.task-list-enter-from,
.task-list-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
