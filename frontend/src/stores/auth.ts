import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types'
import client from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const user = ref<User | null>(
    (() => {
      const stored = localStorage.getItem('auth_user')
      return stored ? JSON.parse(stored) : null
    })()
  )
  const loading = ref(false)
  const error = ref<string | null>(null)

  const isAuthenticated = computed(() => !!token.value)

  async function register(name: string, email: string, password: string, password_confirmation: string) {
    
    loading.value = true
    error.value = null

    try {
      const data = await client.post('/register', { name, email, password, password_confirmation })
      
      token.value = data.authorization.token
      user.value = data.user
      localStorage.setItem('auth_token', data.authorization.token)
      localStorage.setItem('auth_user', JSON.stringify(data.user))
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error.value = axiosError.response?.data?.message || 'Erro ao registrar. Tente novamente.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function login(email: string, password: string) {
    loading.value = true
    error.value = null

    try {
      const data = await client.post('/login', { email, password })
      token.value = data.authorization.token
      user.value = data.user
      localStorage.setItem('auth_token', data.authorization.token)
      localStorage.setItem('auth_user', JSON.stringify(data.user))
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error.value = axiosError.response?.data?.message || 'Verifique suas credenciais e tente novamente.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await client.post('/logout')
    } catch {
      // Proceed even if the API call fails
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
    }
  }

  function clearError() {
    error.value = null
  }

  return {
    token,
    user,
    loading,
    error,
    isAuthenticated,
    login,
    register,
    logout,
    clearError,
  }
})
