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
              <td class="h-12 px-4">
                <StatusUpdater :order-id="order.id" :current-status="order.status" />
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

function formatDate(date: string) {
  const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: '2-digit', day: '2-digit' }
  return new Date(date).toLocaleDateString(undefined, options)
}
</script>
<style lang="">
  
</style>