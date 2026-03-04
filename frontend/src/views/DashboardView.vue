<template lang="">
<div class="min-h-screen bg-background">
      <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <DashboardHeader :onCreateClick="openModal"/>

        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-foreground">Filtrar por status:</label>
            <StatusFilter v-model="storeStatusFilter" />
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
          <OrdersTable :orders="ordersStore.orders" :loading="ordersStore.loading" @delete-order="handleDeleteOrder"/>
        </div>

        <CreateOrderForm :open="showModal" @close="closeModal" />
      </div>
    </div>
</template>
<script setup lang="ts">
import DashboardHeader from '@/components/DashboardHeader.vue'
import OrdersTable from '@/components/OrdersTable.vue'
import StatusFilter from '@/components/StatusFilter.vue'
import CreateOrderForm from '@/components/CreateOrderForm.vue'
import { onMounted, ref, watch } from 'vue';
import { useOrdersStore } from '@/stores/orders';
import { TravelRequestStatus } from '@/types';
import { storeToRefs } from 'pinia';
import { useToast } from '@/composables/useToast';

const { showToast } = useToast()

const ordersStore = useOrdersStore()

const { statusFilter: storeStatusFilter } = storeToRefs(ordersStore)

onMounted(() => {
  ordersStore.fetchOrders()
})

const showModal = ref(false)

function refresh() {
  ordersStore.fetchOrders()
}

function openModal() {
  showModal.value = true
}

function closeModal() {
  refresh();
  showModal.value = false
}

async function handleDeleteOrder(id: number) {
  const result = await ordersStore.deleteOrder(id)
  if (!result.success) {
    showToast(result.error || 'Erro ao deletar pedido.', 'error')
    return
  }

  showToast('Pedido deletado com sucesso.', 'success')
  refresh()
}

</script>
<style lang="">

</style>