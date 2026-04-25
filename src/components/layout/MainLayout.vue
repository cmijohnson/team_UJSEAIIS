<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()
const isMenuOpen = ref(false)
const siteTitle = '智链细米安全盾队展示'

const navItems = [
  { name: '首页', path: '/' },
  { name: '团队风采', path: '/team' },
  { name: '比赛成果', path: '/competitions' },
  { name: '科研项目', path: '/research' },
  { name: '指导老师', path: '/advisors' },
  { name: '文章动态', path: '/articles' }
]

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const isActive = (path: string) => {
  if (path === '/') {
    return route.path === '/'
  }

  return route.path === path || route.path.startsWith(`${path}/`)
}

const getNavClass = (path: string) =>
  [
    'border-b-2 py-2 transition-colors duration-200',
    isActive(path)
      ? 'border-orange-500 text-white font-semibold'
      : 'border-transparent text-gray-200 hover:text-white'
  ]

const getMobileNavClass = (path: string) =>
  [
    'block border-l-4 px-4 py-3 transition-colors',
    isActive(path)
      ? 'border-orange-500 bg-blue-700 text-white font-semibold'
      : 'border-transparent text-gray-200 hover:bg-blue-700 hover:text-white'
  ]

watch(
  () => route.fullPath,
  () => {
    isMenuOpen.value = false
  }
)
</script>

<template>
  <div class="flex min-h-screen flex-col bg-gray-50">
    <header class="sticky top-0 z-50 bg-blue-900 text-white shadow-md">
      <div class="container mx-auto px-4">
        <div class="flex h-16 items-center justify-between">
          <div class="flex cursor-pointer items-center space-x-4" @click="router.push('/')">
            <div
              class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20 backdrop-blur"
            >
              <svg class="h-7 w-7" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32 8L49 14V28C49 40.6 41.5 50.6 32 56C22.5 50.6 15 40.6 15 28V14L32 8Z" fill="#F8FAFC"/>
                <path d="M32 16L42 19.6V27.4C42 35.8 37.2 42.8 32 46.2C26.8 42.8 22 35.8 22 27.4V19.6L32 16Z" fill="#1D4ED8"/>
                <path
                  d="M26 29L30 33L38 25"
                  stroke="#4ADE80"
                  stroke-width="4"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <circle cx="45.5" cy="18.5" r="4.5" fill="#F97316" />
              </svg>
            </div>
            <span class="text-xl font-bold tracking-wider">{{ siteTitle }}</span>
          </div>

          <nav class="hidden space-x-8 md:flex">
            <router-link
              v-for="item in navItems"
              :key="item.path"
              :to="item.path"
              :class="getNavClass(item.path)"
            >
              {{ item.name }}
            </router-link>
          </nav>

          <button
            type="button"
            class="p-2 md:hidden"
            aria-label="切换菜单"
            @click="toggleMenu"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                v-if="!isMenuOpen"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                v-else
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <div v-if="isMenuOpen" class="bg-blue-800 md:hidden">
        <router-link
          v-for="item in navItems"
          :key="item.path"
          :to="item.path"
          :class="getMobileNavClass(item.path)"
        >
          {{ item.name }}
        </router-link>
      </div>
    </header>

    <main class="flex-grow">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <footer class="bg-gray-900 py-8 text-gray-400">
      <div class="container mx-auto px-4 text-center">
        <p class="mb-4 text-lg font-semibold text-white">{{ siteTitle }}</p>
        <p class="mb-4">聚焦网络安全、区块链安全与攻防实践，持续输出团队成果。</p>
        <div class="mb-6 flex justify-center space-x-6">
          <router-link to="/team" class="transition-colors hover:text-white">关于我们</router-link>
          <a href="mailto:contact@example.com" class="transition-colors hover:text-white">联系方式</a>
          <router-link to="/admin/login" class="transition-colors hover:text-white">管理后台</router-link>
        </div>
        <p class="text-sm">© {{ new Date().getFullYear() }} {{ siteTitle }}. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
