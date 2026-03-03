import axios from 'axios'
import type {
  AuthResponse,
  LoginCredentials,
  TravelOrder,
  CreateOrderPayload,
  UpdateStatusPayload,
  PaginatedResponse,
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
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export async function login(credentials: LoginCredentials): Promise<AuthResponse> {
  const { data } = await client.post<AuthResponse>('/login', credentials)
  return data
}

export async function register(name: string, email: string, password: string, password_confirmation: string): Promise<AuthResponse> {
  const { data } = await client.post<AuthResponse>('/register', { name, email, password, password_confirmation })
  return data
}

export async function logout(): Promise<void> {
  await client.post('/logout')
}

export async function request(method='GET', url: string, data?: any): Promise<any> {
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

export async function get(url: string, params?: Record<string, string>): Promise<any> {
  const query = params ? `?${new URLSearchParams(params).toString()}` : ''
  return request('GET', `${url}${query}`)
}

export async function post(url: string, data?: any): Promise<any> {
  return request('POST', url, data)
}

export async function patch(url: string, data?: any): Promise<any> {
  return request('PATCH', url, data)
}

export default client
