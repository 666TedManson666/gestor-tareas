<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-80">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="flex items-start gap-3 rounded-lg px-4 py-3 shadow-lg text-white text-sm font-medium"
          :class="{
            'bg-green-600': toast.type === 'success',
            'bg-red-600':   toast.type === 'error',
            'bg-blue-600':  toast.type === 'info',
          }"
        >
          <!-- Icono -->
          <span class="text-lg leading-none mt-0.5">
            <template v-if="toast.type === 'success'">✓</template>
            <template v-else-if="toast.type === 'error'">✕</template>
            <template v-else>ℹ</template>
          </span>

          <!-- Mensaje -->
          <p class="flex-1 leading-snug">{{ toast.message }}</p>

          <!-- Cerrar -->
          <button
            @click="dismiss(toast.id)"
            class="opacity-70 hover:opacity-100 transition-opacity leading-none mt-0.5"
            aria-label="Cerrar notificación"
          >
            ×
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '../composables/useToast.js'

const { toasts, dismiss } = useToast()
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
