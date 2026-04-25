<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import request from '@/utils/request'
import {
  createBannerPlaceholder,
  createCardPlaceholder,
  resolveMediaUrl
} from '@/utils/media'

interface BannerItem {
  id: number | string
  url: string
}

interface NewsItem {
  id: number
  title: string
  date?: string
  created_at?: string
  cover_image?: string
}

const router = useRouter()

const bannerList = ref<BannerItem[]>([
  {
    id: 1,
    url: createBannerPlaceholder('智链细米安全盾队展示', '探索网络安全前沿，构建面向实战的技术能力')
  },
  {
    id: 2,
    url: createBannerPlaceholder('安全研究与竞赛并进', '从基础训练到工程交付，持续积累可验证的成果')
  }
])

const introduction = ref(
  '智链细米安全盾队专注于网络安全、区块链安全与攻防实践，持续参与竞赛、研究与开源安全建设。'
)

const latestNews = ref<NewsItem[]>([
  { id: 1, title: '团队在全国大学生信息安全竞赛中获得一等奖', created_at: '2023-10-15' },
  { id: 2, title: '新学期招新启动，欢迎对安全感兴趣的同学加入', created_at: '2023-09-01' },
  { id: 3, title: '成员发现开源项目高危漏洞并提交修复建议', created_at: '2023-08-20' }
])

const loading = ref(true)

const normalizeBanners = (banners: any[]) =>
  banners
    .map((item, index) => ({
      id: item.id ?? index + 1,
      url: item.url || item.image
    }))
    .filter((item) => item.url)

const normalizeIntroduction = (value: unknown) => {
  if (typeof value === 'string') {
    return value
  }

  if (value && typeof value === 'object' && 'content' in value) {
    return String((value as { content?: string }).content || introduction.value)
  }

  return introduction.value
}

const fetchHomeData = async () => {
  try {
    const res: any = await request.get('/home/content')
    const banners = Array.isArray(res?.banner) ? normalizeBanners(res.banner) : []
    const news = Array.isArray(res?.latest_news) ? res.latest_news : []

    if (banners.length > 0) {
      bannerList.value = banners
    }

    introduction.value = normalizeIntroduction(res?.introduction)

    if (news.length > 0) {
      latestNews.value = news
    }
  } catch (error) {
    console.error('Failed to fetch home data, using mock data', error)
  } finally {
    loading.value = false
  }
}

const getNewsDate = (news: NewsItem) => {
  const rawDate = news.date || news.created_at || ''
  return rawDate.split(' ')[0]
}

const getNewsImage = (news: NewsItem) =>
  resolveMediaUrl(
    news.cover_image,
    createCardPlaceholder(`NEWS ${news.id}`, '#2563eb', '#dbeafe')
  )

const getBannerImage = (item: BannerItem, index: number) =>
  resolveMediaUrl(
    item.url,
    createBannerPlaceholder(
      index === 0 ? '智链细米安全盾队展示' : '安全研究与竞赛并进',
      index === 0 ? '探索网络安全前沿，构建面向实战的技术能力' : '从基础训练到工程交付，持续积累可验证的成果'
    )
  )

onMounted(() => {
  fetchHomeData()
})
</script>

<template>
  <div class="home-page">
    <el-carousel height="500px" arrow="always">
      <el-carousel-item v-for="(item, index) in bannerList" :key="item.id">
        <div
          class="relative flex h-full w-full items-center justify-center bg-cover bg-center"
          :style="{ backgroundImage: `url(${getBannerImage(item, index)})` }"
        >
          <div class="absolute inset-0 bg-black/45"></div>
          <div class="relative z-10 px-4 text-center text-white">
            <h1 class="mb-4 text-5xl font-bold tracking-wider">智链细米安全盾队展示</h1>
            <p class="text-xl tracking-wide">探索网络安全前沿，构建面向实战的技术能力</p>
          </div>
        </div>
      </el-carousel-item>
    </el-carousel>

    <div class="container mx-auto px-4 py-16">
      <div
        class="relative z-20 mx-auto mb-16 max-w-4xl -translate-y-20 rounded-xl bg-white p-8 shadow-lg md:p-12"
      >
        <h2 class="relative mb-6 pb-4 text-center text-3xl font-bold text-gray-800">
          团队介绍
          <span
            class="absolute bottom-0 left-1/2 h-1 w-16 -translate-x-1/2 rounded-full bg-orange-500"
          ></span>
        </h2>
        <p class="text-center text-lg leading-relaxed text-gray-600">
          {{ introduction }}
        </p>
      </div>

      <el-skeleton :loading="loading" animated>
        <template #template>
          <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <div v-for="index in 3" :key="index" class="rounded-lg bg-white p-6 shadow-md">
              <el-skeleton-item variant="image" style="width: 100%; height: 12rem" />
              <el-skeleton-item variant="text" style="margin-top: 1rem; width: 50%" />
              <el-skeleton-item variant="h3" style="margin-top: 0.75rem; width: 100%" />
            </div>
          </div>
        </template>

        <div class="mb-16">
          <div class="mb-8 flex items-center justify-between">
            <h2 class="border-l-4 border-orange-500 pl-4 text-3xl font-bold text-gray-800">
              最新动态
            </h2>
            <el-button type="primary" plain @click="router.push('/articles')">查看更多</el-button>
          </div>

          <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <article
              v-for="news in latestNews"
              :key="news.id"
              class="cursor-pointer overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl"
              @click="router.push(`/article/${news.id}`)"
            >
              <div class="relative h-48 bg-gray-200">
                <img
                  :src="getNewsImage(news)"
                  :alt="news.title"
                  class="h-full w-full object-cover"
                >
              </div>
              <div class="p-6">
                <p class="mb-2 text-sm text-gray-500">{{ getNewsDate(news) }}</p>
                <h3 class="line-clamp-2 text-xl font-semibold text-gray-800 transition-colors hover:text-blue-600">
                  {{ news.title }}
                </h3>
              </div>
            </article>
          </div>
        </div>
      </el-skeleton>

      <div class="grid grid-cols-1 gap-6 text-center md:grid-cols-4">
        <div
          class="cursor-pointer rounded-xl bg-blue-50 p-8 transition-colors hover:bg-blue-100"
          @click="router.push('/team')"
        >
          <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-white">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-800">队伍风采</h3>
        </div>

        <div
          class="cursor-pointer rounded-xl bg-orange-50 p-8 transition-colors hover:bg-orange-100"
          @click="router.push('/competitions')"
        >
          <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-orange-500 text-white">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
              />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-800">比赛成果</h3>
        </div>

        <div
          class="cursor-pointer rounded-xl bg-green-50 p-8 transition-colors hover:bg-green-100"
          @click="router.push('/research')"
        >
          <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-500 text-white">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
              />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-800">科研项目</h3>
        </div>

        <div
          class="cursor-pointer rounded-xl bg-purple-50 p-8 transition-colors hover:bg-purple-100"
          @click="router.push('/advisors')"
        >
          <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-purple-600 text-white">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 14v6m-4-3.5V11.5l4-2.2 4 2.2v5"
              />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-800">指导老师</h3>
        </div>
      </div>
    </div>
  </div>
</template>
