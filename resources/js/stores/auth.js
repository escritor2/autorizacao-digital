import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)

  const apiInstance = axios.create({
    baseURL: import.meta.env.VITE_API_URL || '/api',
    withCredentials: true,
  })

  // Interceptor para adicionar token
  apiInstance.interceptors.request.use((config) => {
    if (token.value) {
      config.headers.Authorization = `Bearer ${token.value}`
    }
    return config
  })

  const login = async (email, password) => {
    loading.value = true
    try {
      const response = await apiInstance.post('/auth/login', { email, password })
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value)
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Erro ao fazer login')
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await apiInstance.post('/auth/logout')
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
    }
  }

  const fetchUser = async () => {
    if (!token.value) return
    try {
      const response = await apiInstance.get('/auth/me')
      user.value = response.data
    } catch (error) {
      logout()
    }
  }

  const api = computed(() => apiInstance)

  return {
    user,
    token,
    loading,
    isAuthenticated,
    api,
    login,
    logout,
    fetchUser,
  }
})
