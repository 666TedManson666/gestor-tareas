<template>
  <!-- Backdrop -->
  <div
    class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 flex items-center justify-center p-4"
    @click.self="$emit('cancel')"
  >
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800">
          {{ isEditing ? 'Editar tarea' : 'Nueva tarea' }}
        </h2>
        <button
          @click="$emit('cancel')"
          class="text-gray-400 hover:text-gray-600 transition-colors text-xl leading-none"
        >
          ×
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="px-6 py-5 space-y-4">
        <!-- Título -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Título <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.title"
            type="text"
            placeholder="Nombre de la tarea..."
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange/50 focus:border-brand-orange transition"
            :class="{ 'border-red-400': errors.title }"
            autofocus
          />
          <p v-if="errors.title" class="mt-1 text-xs text-red-500">{{ errors.title }}</p>
        </div>

        <!-- Descripción -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
          <textarea
            v-model="form.description"
            rows="3"
            placeholder="Descripción opcional..."
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-brand-orange/50 focus:border-brand-orange transition"
          />
        </div>

        <!-- Estado (solo en edición) -->
        <div v-if="isEditing">
          <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
          <select
            v-model="form.status"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange/50 focus:border-brand-orange transition"
          >
            <option value="pending">Pendiente</option>
            <option value="inProgress">En progreso</option>
            <option value="done">Terminada</option>
          </select>
        </div>

        <!-- Acciones -->
        <div class="flex gap-3 pt-2">
          <button
            type="button"
            @click="$emit('cancel')"
            class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
          >
            Cancelar
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="flex-1 rounded-lg bg-brand-orange px-4 py-2 text-sm font-medium text-white hover:bg-orange-600 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <span v-if="loading" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ loading ? 'Guardando...' : (isEditing ? 'Guardar cambios' : 'Crear tarea') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'

const props = defineProps({
  task: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['submit', 'cancel'])

const isEditing = computed(() => !!props.task)

const form = reactive({
  title:       props.task?.title       ?? '',
  description: props.task?.description ?? '',
  status:      props.task?.status      ?? 'pending',
})

const errors = reactive({ title: '' })

function handleSubmit() {
  errors.title = ''

  if (!form.title.trim()) {
    errors.title = 'El título es obligatorio.'
    return
  }

  emit('submit', { ...form })
}
</script>
