import axios from 'axios'
import type {
  AuthResponse,
  LoginCredentials,
} from '@/types'

const client = axios.create({
  baseURL: 'http://localhost:8989/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

client.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

client.interceptors.response.use(
  (response) => response,
  (error) => {
    
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
    }
    return Promise.reject(error)
  }
)

async function request(method='GET', url: string, data?: any): Promise<any> {
  try {
    const response = await client.request({ method, url, data })
    return response.data
  } catch (error) {
    if (axios.isAxiosError(error)) {
      throw new Error(error.response?.data?.message || 'Erro na requisição')
    }
    throw error
  }
}

async function get(url: string, params?: Record<string, string>): Promise<any> {
  const query = params ? `?${new URLSearchParams(params).toString()}` : ''
  return request('GET', `${url}${query}`)
}

async function post(url: string, data?: any): Promise<any> {
  return request('POST', url, data)
}

async function patch(url: string, data?: any): Promise<any> {
  return request('PATCH', url, data)
}

export default {
  get,
  post,
  patch,
  request
}
