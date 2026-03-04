export interface User {
  id: number
  name: string
  email: string
  is_admin: boolean
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface AuthResponse {
  authorization: {
    token: string
  }
  user: User
}

export type TravelRequest = {
  id: number
  requester: User
  destination: string
  departure_date: string
  return_date: string
  status: TravelRequestStatus
}

export enum TravelRequestStatus {
  REQUESTED = 'requested',
  APPROVED = 'approved',
  CANCELLED = 'cancelled',
}


export interface CreateOrderPayload {
  destination: string
  departure_date: string
  return_date: string
}

export interface UpdateStatusPayload {
  status: TravelRequestStatus
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}

