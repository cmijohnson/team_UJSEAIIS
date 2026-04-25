<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import {
  Collection,
  DataLine,
  Document,
  Menu as IconMenu,
  SwitchButton,
  Trophy,
  User
} from '@element-plus/icons-vue'

const router = useRouter()
const route = useRoute()

const logout = () => {
  localStorage.removeItem('admin_token')
  router.push('/admin/login')
}
</script>

<template>
  <el-container class="min-h-screen">
    <el-aside width="250px" class="flex flex-col bg-blue-900 text-white">
      <div class="flex h-16 items-center justify-center border-b border-blue-800">
        <span class="text-xl font-bold tracking-wider">后台管理</span>
      </div>

      <el-menu
        :default-active="route.path"
        active-text-color="#f97316"
        background-color="#1e3a8a"
        class="flex-grow border-r-0"
        text-color="#fff"
        router
      >
        <el-menu-item index="/admin/dashboard">
          <el-icon><IconMenu /></el-icon>
          <span>控制台首页</span>
        </el-menu-item>

        <el-menu-item index="/admin/articles">
          <el-icon><Document /></el-icon>
          <span>文章管理</span>
        </el-menu-item>

        <el-menu-item index="/admin/members">
          <el-icon><User /></el-icon>
          <span>成员管理</span>
        </el-menu-item>

        <el-menu-item index="/admin/competitions">
          <el-icon><Trophy /></el-icon>
          <span>比赛奖项</span>
        </el-menu-item>

        <el-menu-item index="/admin/projects">
          <el-icon><Collection /></el-icon>
          <span>科研项目</span>
        </el-menu-item>

        <el-menu-item index="/admin/timeline">
          <el-icon><DataLine /></el-icon>
          <span>时间轴管理</span>
        </el-menu-item>
      </el-menu>

      <div class="border-t border-blue-800 p-4">
        <el-button type="danger" plain class="w-full" @click="logout">
          <el-icon class="mr-2"><SwitchButton /></el-icon>
          退出登录
        </el-button>
      </div>
    </el-aside>

    <el-container>
      <el-header class="flex items-center justify-between bg-white px-6 shadow-sm">
        <div class="font-medium text-gray-600">欢迎回来，管理员</div>
        <el-button text @click="router.push('/')">返回前台</el-button>
      </el-header>

      <el-main class="bg-gray-100 p-6">
        <router-view v-slot="{ Component }">
          <transition name="el-fade-in-linear" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </el-main>
    </el-container>
  </el-container>
</template>

<style scoped>
.el-menu {
  border-right: none;
}
</style>
