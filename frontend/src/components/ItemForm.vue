<template>
  <form @submit.prevent="handleSubmit" class="flex gap-2 items-start mt-3">
    <div class="flex-1">
      <input
        v-model="title"
        type="text"
        placeholder="Nuevo sub-ítem..."
        class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange/40 focus:border-brand-orange transition"
        :class="{ 'border-red-400': error }"
      />
      <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
    </div>

    <!-- Prioridad -->
    <select
      v-model="priority"
      class="rounded-lg border border-gray-200 px-2 py-1.5 text-xs text-gray-600 focus:outline-none focus:ring-2 focus:ring-brand-orange/40 focus:border-brand-orange transition"
    >
      <option value="low">Baja</option>
      <option value="medium">Media</option>
      <option value="high">Alta</option>
    </select>

    <button
      type="submit"
      :disabled="loading"
      class="rounded-lg bg-brand-orange px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-600 transition disabled:opacity-50"
    >
      <span v-if="loading" class="inline-block w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
      <span v-else>+</span>
    </button>
  </form>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['submit'])

const title    = ref('')
const priority = ref('medium')
const error    = ref('')

function handleSubmit() {
  error.value = ''

  if (!title.value.trim()) {
    error.value = 'El título del ítem es obligatorio.'
    return
  }

  emit('submit', { title: title.value.trim(), priority: priority.value })
  title.value = ''
  priority.value = 'medium'
}
</script>
