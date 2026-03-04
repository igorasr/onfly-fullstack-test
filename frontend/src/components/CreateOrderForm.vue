
<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" @click="close"></div>

        <div class="relative z-50 w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-card-foreground">
              Novo Pedido de Viagem
            </h2>

            <button
              @click="close"
              class="rounded-md p-1 text-muted-foreground hover:text-foreground hover:bg-muted transition-colors"
              aria-label="Fechar"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="onSubmit" class="flex flex-col gap-4">
            <div class="flex flex-col gap-1.5">
              <BaseInput
                id="modal-dest"
                label="Destino"
                name="destination"
                type="text"
                v-model="destination"
                placeholder="Ex: Sao Paulo, SP"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div class="flex flex-col gap-1.5">
                <BaseInput
                  id="modal-dep"
                  label="Data de Ida"
                  name="departureDate"
                  type="date"
                  v-model="departureDate"
                />
              </div>

              <div class="flex flex-col gap-1.5">
                <BaseInput
                  id="modal-ret"
                  label="Data de Retorno"
                  name="returnDate"
                  type="date"
                  v-model="returnDate"
                />
              </div>
            </div>

            <div class="flex justify-end gap-2 mt-2">
              <button
                type="button"
                @click="close"
                class="h-9 rounded-md border border-input bg-background px-4 text-sm font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors"
              >
                Cancelar
              </button>

              <button
                type="submit"
                :disabled="submitting"
                class="h-9 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground shadow-sm hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2"
              >
                <svg v-if="submitting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>

                {{ submitting ? 'Criando...' : 'Criar Pedido' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import BaseInput from './BaseInput.vue'
import { ref, watch } from 'vue'
import { useOrdersStore } from '../stores/orders'
import { useAuthStore } from '../stores/auth'
import { useToast } from '@/composables/useToast'

interface Props {
  open: boolean
}

const { showToast } = useToast()

const props = withDefaults(defineProps<Props>(), {
  open: false,
})

const emit = defineEmits<{
  (e: 'close'): void
}>()

const ordersStore = useOrdersStore()
const authStore = useAuthStore()

const submitting = ref(false)
const destination = ref('')
const departureDate = ref('')
const returnDate = ref('')

watch(
  () => props.open,
  (val) => {
    if (val) {
      destination.value = ''
      departureDate.value = ''
      returnDate.value = ''
    }
  }
)

async function onSubmit() {
  debugger
  if (!destination.value || !departureDate.value || !returnDate.value) {
    showToast('Preencha todos os campos.', 'error')
    return
  }

  if (new Date(returnDate.value) <= new Date(departureDate.value)) {
    showToast('A data de retorno deve ser posterior a data de ida.', 'error')
    return
  }

  submitting.value = true

  const result = await ordersStore.createOrder({
    requesterName: authStore.user?.name || 'Desconhecido',
    destination: destination.value,
    departure_date: departureDate.value,
    return_date: returnDate.value,
  })

  submitting.value = false

  if (result.success) {
    showToast('Pedido criado com sucesso!', 'success')
    emit('close')
  } else {
    showToast(result.error || 'Erro ao criar pedido.', 'error')
  }
}

function close() {
  emit('close')
}
</script>