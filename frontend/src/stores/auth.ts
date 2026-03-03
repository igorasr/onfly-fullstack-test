import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types'
import { register as apiRegister, login as apiLogin, logout as apiLogout } from '@/services/api'

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
      const data = await apiRegister(name, email, password, password_confirmation)
      
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
      const data = await apiLogin({ email, password })
      token.value = data.authorization.token
      user.value = data.user
      localStorage.setItem('auth_token', data.authorization.token)
      localStorage.setItem('auth_user', JSON.stringify(data.user))
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error.value = axiosError.response?.data?.message || 'Credenciais invalidas. Tente novamente.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await apiLogout()
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
