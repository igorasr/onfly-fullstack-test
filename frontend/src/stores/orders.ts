import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { TravelRequest, TravelRequestStatus } from '@/types'
import client from '@/services/api'

export const useOrdersStore = defineStore('orders', () => {
  const orders = ref<TravelRequest[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const statusFilter = ref<TravelRequestStatus | ''>('')

  watch(statusFilter, (status) => {
    fetchOrders(status)
  })

  async function fetchOrders(status: TravelRequestStatus | '' = statusFilter.value) {
    loading.value = true
    error.value = null

    try {
      const params: Record<string, string> = {}

      if (status) {
        params.status = status
      }

      const result = await client.get('/travel-requests', params)
      orders.value = result.data

    } catch (err: any) {
      error.value = err.message || 'Erro ao buscar pedidos.'
    } finally {
      loading.value = false
    }
  }

  async function createOrder(payload: {
    requesterName: string
    destination: string
    departure_date: string
    return_date: string
  }): Promise<{ success: boolean; error?: string }> {
    try {
      await client.post('/travel-requests', payload)
      return { success: true }
    } catch (err: any) {
      return { success: false, error: err.message || 'Erro de conexao.' }
    }
  }

  async function updateTravelRequestStatus(
    id: number,
    status: TravelRequestStatus
  ): Promise<{ success: boolean; error?: string }> {
    try {
      const response = await client.patch(`/travel-requests/${id}/status`, { status })
      const data = response.data

      const idx = orders.value.findIndex((o) => o.id === id)
      if (idx !== -1) {
        orders.value[idx] = data
      }
      return { success: true }
    } catch (err: any) {
      return { success: false, error: err.message || 'Erro de conexao.' }
    }
  }

  return { orders, loading, error, statusFilter, fetchOrders, createOrder, updateTravelRequestStatus }
})
