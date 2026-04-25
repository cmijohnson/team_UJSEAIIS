<script setup lang="ts">
import { onMounted, ref } from 'vue'
import request from '@/utils/request'
import { createCardPlaceholder, resolveMediaUrl } from '@/utils/media'

interface Competition {
  id: number
  name: string
  competition_date?: string
  date?: string
  result: string
  description: string
  images?: string[]
}

const competitions = ref<Competition[]>([
  {
    id: 1,
    name: '全国大学生信息安全竞赛',
    competition_date: '2023-11-15',
    result: '特等奖',
    description: '团队在漏洞分析与修复能力赛道中取得突出成绩。',
    images: []
  },
  {
    id: 2,
    name: '强网杯全国网络安全挑战赛',
    competition_date: '2023-06-20',
    result: '一等奖',
    description: '在 Web 安全和智能合约方向均取得优异排名。',
    images: []
  }
])

const fetchCompetitions = async () => {
  try {
    const res: any = await request.get('/competitions')
    const list = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : []
    if (list.length > 0) {
      competitions.value = list
    }
  } catch (error) {
    console.error('Failed to fetch competitions, using mock data', error)
  }
}

const getCompetitionDate = (competition: Competition) =>
  competition.competition_date || competition.date || '待更新'

const getCompetitionImage = (competition: Competition) =>
  resolveMediaUrl(
    competition.images?.[0],
    createCardPlaceholder(`COMP ${competition.id}`, '#ea580c', '#ffedd5')
  )

onMounted(() => {
  fetchCompetitions()
})
</script>

<template>
  <div class="competitions-page py-12">
    <div class="container mx-auto px-4">
      <div class="mb-16 text-center">
        <h1 class="mb-4 text-4xl font-bold text-gray-800">比赛成果</h1>
        <p class="mx-auto max-w-2xl text-gray-500">
          以赛代练，在真实攻防场景中沉淀能力，并通过公开竞赛验证团队水平。
        </p>
      </div>

      <div class="mx-auto max-w-5xl">
        <div
          v-for="(competition, index) in competitions"
          :key="competition.id"
          class="mb-10 flex flex-col overflow-hidden rounded-xl bg-white shadow-lg transition-shadow hover:shadow-xl md:flex-row"
          :class="{ 'md:flex-row-reverse': index % 2 !== 0 }"
        >
          <div class="relative h-64 md:h-auto md:w-1/2">
            <img
              :src="getCompetitionImage(competition)"
              :alt="competition.name"
              class="h-full w-full object-cover"
            >
            <div
              class="absolute right-4 top-4 rounded-full bg-orange-500 px-4 py-1 font-bold text-white shadow-md"
            >
              {{ competition.result }}
            </div>
          </div>
          <div class="flex flex-col justify-center p-8 md:w-1/2">
            <div class="mb-2 text-sm font-semibold text-blue-600">
              {{ getCompetitionDate(competition) }}
            </div>
            <h2 class="mb-4 text-2xl font-bold text-gray-800">{{ competition.name }}</h2>
            <p class="leading-relaxed text-gray-600">{{ competition.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
