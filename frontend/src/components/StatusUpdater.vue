<template lang="">
  <div class="relative inline-flex items-center gap-2">
    <select
      :value="currentStatus"
      @change="onStatusChange"
      :disabled="updating"
      class="h-8 rounded-md border border-input bg-background px-2 py-0.5 text-xs text-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-ring appearance-none pr-7 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
        {{ opt.label }}
      </option>
    </select>
    <svg
      v-if="!updating"
      class="absolute right-1.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground pointer-events-none"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      stroke-width="2"
    >
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
    </svg>
    <svg
      v-else
      class="absolute right-1.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground pointer-events-none animate-spin"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      />
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
      />
    </svg>
  </div>
</template>
<script setup lang="ts">
import { TravelRequestStatus } from '@/types';
import { useOrdersStore } from '@/stores/orders';
import { useToast } from '@/composables/useToast'
import { ref } from 'vue';

const { showToast } = useToast()

const orderStore = useOrdersStore()

const updating = ref(false)

const props = defineProps<{
  orderId: number
  currentStatus: TravelRequestStatus
}>()

const statusOptions: Record<TravelRequestStatus, { label: string; value: TravelRequestStatus }> = {
  [TravelRequestStatus.REQUESTED]: { label: 'Pending', value: TravelRequestStatus.REQUESTED },
  [TravelRequestStatus.APPROVED]: { label: 'Approved', value: TravelRequestStatus.APPROVED },
  [TravelRequestStatus.CANCELLED]: { label: 'Cancelled', value: TravelRequestStatus.CANCELLED }
}

async function onStatusChange(e: Event) {
  
  const newStatus = (e.target as HTMLSelectElement).value as TravelRequestStatus

  if (newStatus === props.currentStatus) return
  updating.value = true
  const result = await orderStore.updateTravelRequestStatus(props.orderId, newStatus)
  updating.value = false
  if (result.success) {
    showToast('Status atualizado com sucesso!', 'success')
  } else {
    showToast(result.error || 'Erro ao atualizar status.', 'error')
  }
}

</script>
<style lang=""></style>
