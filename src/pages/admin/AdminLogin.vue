<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Lock, User } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'
import { login } from '@/api/auth'

const router = useRouter()
const loginForm = ref({
  username: '',
  password: ''
})
const loading = ref(false)

const handleLogin = async () => {
  if (!loginForm.value.username || !loginForm.value.password) {
    ElMessage.warning('请输入用户名和密码')
    return
  }

  loading.value = true
  try {
    const res: any = await login(loginForm.value)
    if (res.token) {
      localStorage.setItem('admin_token', res.token)
      ElMessage.success('登录成功')
      router.push('/admin/dashboard')
    }
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="relative flex min-h-screen items-center justify-center bg-gray-100">
    <div
      class="absolute inset-0 bg-blue-900"
      style="clip-path: polygon(0 0, 100% 0, 100% 40%, 0 60%)"
    ></div>

    <div class="relative z-10 w-full max-w-md overflow-hidden rounded-xl bg-white shadow-2xl">
      <div class="bg-blue-800 py-8 text-center text-white">
        <h1 class="text-3xl font-bold tracking-wider">团队后台管理</h1>
        <p class="mt-2 text-blue-200">使用管理员账号登录后台</p>
      </div>

      <div class="p-8">
        <el-form class="space-y-6" @submit.prevent="handleLogin">
          <el-input
            v-model="loginForm.username"
            placeholder="用户名：admin"
            size="large"
            :prefix-icon="User"
          />

          <el-input
            v-model="loginForm.password"
            type="password"
            placeholder="密码：password"
            size="large"
            show-password
            :prefix-icon="Lock"
            @keyup.enter="handleLogin"
          />

          <el-button
            type="primary"
            size="large"
            class="w-full"
            :loading="loading"
            @click="handleLogin"
          >
            登录
          </el-button>
        </el-form>
      </div>
    </div>
  </div>
</template>
