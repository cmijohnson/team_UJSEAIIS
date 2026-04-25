import axios from 'axios'
import type { AxiosError } from 'axios'
import { ElMessage } from 'element-plus'

const request = axios.create({
  baseURL: '/api',
  timeout: 10000
})

request.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('admin_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

request.interceptors.response.use(
  (response) => response.data,
  (error) => {
    const axiosError = error as AxiosError<{ error?: string; message?: string }>
    const message =
      axiosError.response?.data?.message ||
      axiosError.response?.data?.error ||
      axiosError.message ||
      '请求失败'

    ElMessage.error(message)

    if (
      axiosError.response?.status === 401 &&
      !window.location.pathname.startsWith('/admin/login')
    ) {
      localStorage.removeItem('admin_token')
      window.location.href = '/admin/login'
    }

    return Promise.reject(axiosError)
  }
)

export default request
