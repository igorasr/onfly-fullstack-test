<template lang="">
<div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
      <div v-if="loading" class="flex items-center justify-center py-16">
        <LoadingSpinner />
      </div>

      <div v-else-if="!orders.length" class="flex flex-col items-center justify-center py-16 text-muted-foreground">
        <svg class="h-12 w-12 mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-sm font-medium">Nenhum pedido encontrado</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-border bg-muted/50">
              <th class="h-10 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">ID</th>
              <th class="h-10 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Solicitante</th>
              <th class="h-10 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Destino</th>
              <th class="h-10 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Ida</th>
              <th class="h-10 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Retorno</th>
              <th class="h-10 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
              <th class="h-10 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Acao</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="order in orders"
              :key="order.id"
              class="border-b border-border last:border-0 hover:bg-muted/30 transition-colors"
            >
              <td class="h-12 px-4 text-sm font-mono text-muted-foreground">#{{ order.id }}</td>
              <td class="h-12 px-4 text-sm font-medium text-card-foreground">{{ order.requester.name }}</td>
              <td class="h-12 px-4 text-sm text-card-foreground">{{ order.destination }}</td>
              <td class="h-12 px-4 text-sm text-muted-foreground">{{ formatDate(order.departure_date) }}</td>
              <td class="h-12 px-4 text-sm text-muted-foreground">{{ formatDate(order.return_date) }}</td>
              <td class="h-12 px-4">
                <StatusBadge :status="order.status" />
              </td>
              <td class="h-12 px-4 flex items-center">
                <StatusUpdater :order-id="order.id" :current-status="order.status" />
                <button 
                  @click="$emit('delete-order', order.id)"                  
                  class="ml-2 px-3 py-1 text-xs font-medium rounded hover:bg-red-600 hover:text-white transition-colors cursor-pointer"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</template>
<script setup lang="ts">
import StatusBadge from '@/components/StatusBadge.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import StatusUpdater from '@/components/StatusUpdater.vue'
import { formatDate } from '@/lib/date'
import { TravelRequestStatus } from '@/types'

withDefaults(defineProps<{
  orders: Array<{
    id: number
    requester: { name: string }
    destination: string
    departure_date: string
    return_date: string
    status: TravelRequestStatus
  }>
  loading: boolean
}>(), {
  orders: () => [],
  loading: false
})

defineEmits<{
  (e: 'delete-order', id: number): void
}>()

</script>
<style lang="">

</style>