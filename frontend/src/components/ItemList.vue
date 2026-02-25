<template>
  <ul class="space-y-1.5">
    <li
      v-for="item in items"
      :key="item.id"
      class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-50 group transition"
    >
      <!-- Checkbox completado -->
      <input
        type="checkbox"
        :checked="item.is_completed"
        @change="$emit('toggle-item', item)"
        class="w-4 h-4 rounded accent-brand-orange cursor-pointer flex-shrink-0"
      />

      <!-- Título -->
      <span
        class="flex-1 text-sm text-gray-700 transition"
        :class="{ 'line-through text-gray-400': item.is_completed }"
      >
        {{ item.title }}
      </span>

      <!-- Badge prioridad -->
      <span
        class="text-xs px-1.5 py-0.5 rounded-full font-medium flex-shrink-0"
        :class="priorityClass(item.priority)"
      >
        {{ priorityLabel(item.priority) }}
      </span>

      <!-- Eliminar ítem -->
      <button
        @click="$emit('delete-item', item)"
        class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-all text-lg leading-none ml-1"
        title="Eliminar ítem"
      >
        ×
      </button>
    </li>

    <li v-if="items.length === 0" class="text-xs text-gray-400 italic px-2 py-1">
      Sin sub-ítems todavía.
    </li>
  </ul>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    default: () => [],
  },
})

defineEmits(['toggle-item', 'delete-item'])

function priorityClass(priority) {
  return {
    low:    'bg-gray-100 text-gray-500',
    medium: 'bg-yellow-100 text-yellow-700',
    high:   'bg-red-100 text-red-600',
  }[priority] ?? 'bg-gray-100 text-gray-500'
}

function priorityLabel(priority) {
  return { low: 'Baja', medium: 'Media', high: 'Alta' }[priority] ?? priority
}
</script>
