<template lang="">
<div class="min-h-screen bg-background">
      <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <DashboardHeader />

        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-foreground">Filtrar por status:</label>
            <!-- <StatusFilter v-model="statusFilter" /> -->
          </div>
          <button
            @click="refresh"
            class="h-9 rounded-md border border-input bg-background px-3 text-sm font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors inline-flex items-center gap-2"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Atualizar
          </button>
        </div>

        <div class="mt-4">
          <OrdersTable :orders="ordersStore.orders" :loading="ordersStore.loading"/>
        </div>

        <!-- <div
          v-if="ordersStore.error"
          class="mt-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800"
        >
          {{ ordersStore.error }}
        </div>

        <CreateOrderModal :open="showModal" @close="closeModal" /> -->
      </div>
    </div>
</template>
<script setup lang="ts">
  import DashboardHeader from '@/components/DashboardHeader.vue'
  import OrdersTable from  '@/components/OrdersTable.vue' 
  import { onMounted } from 'vue';
  import { useOrdersStore } from '@/stores/orders';

  const ordersStore = useOrdersStore()

  onMounted(() => {
      ordersStore.fetchOrders()
  })

  function refresh() {
    ordersStore.fetchOrders()
  }
  
</script>
<style lang="">
  
</style>