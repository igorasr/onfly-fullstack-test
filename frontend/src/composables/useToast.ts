// /composables/useToast.ts
import { ref } from 'vue'

export type ToastType = 'success' | 'error'

export interface ToastItem {
  id: number
  message: string
  type: ToastType
}

let toastId = 0
const toasts = ref<ToastItem[]>([])

export function useToast() {
  function showToast(message: string, type: ToastType = 'success') {
    
    const id = ++toastId

    toasts.value.push({ id, message, type })

    setTimeout(() => {
      toasts.value = toasts.value.filter((t) => t.id !== id)
    }, 4000)
  }

  return {
    toasts,
    showToast,
  }
}