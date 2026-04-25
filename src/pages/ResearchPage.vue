<script setup lang="ts">
import { onMounted, ref } from 'vue'
import request from '@/utils/request'
import { createCardPlaceholder, resolveMediaUrl } from '@/utils/media'

interface Project {
  id: number
  title: string
  status: 'ongoing' | 'completed' | 'planned' | string
  start_date?: string
  description: string
  achievements: string
  images?: string[]
}

const projects = ref<Project[]>([
  {
    id: 1,
    title: '基于大模型的智能合约漏洞检测系统',
    status: 'ongoing',
    start_date: '2023-09-01',
    description: '通过语义分析与自动化测试识别高危合约缺陷。',
    achievements: '已完成原型系统并在团队内部投入使用。',
    images: []
  },
  {
    id: 2,
    title: '物联网固件自动化分析平台',
    status: 'completed',
    start_date: '2022-05-10',
    description: '支持批量解包、组件识别与已知漏洞匹配。',
    achievements: '发现多个真实设备漏洞，并形成可复用分析流程。',
    images: []
  }
])

const fetchProjects = async () => {
  try {
    const res: any = await request.get('/projects')
    const list = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : []
    if (list.length > 0) {
      projects.value = list
    }
  } catch (error) {
    console.error('Failed to fetch projects, using mock data', error)
  }
}

const getStatusType = (status: string) => {
  switch (status) {
    case 'ongoing':
      return 'success'
    case 'completed':
      return 'info'
    case 'planned':
      return 'warning'
    default:
      return 'info'
  }
}

const getStatusText = (status: string) => {
  switch (status) {
    case 'ongoing':
      return '进行中'
    case 'completed':
      return '已完成'
    case 'planned':
      return '规划中'
    default:
      return '未知'
  }
}

const getProjectImage = (project: Project) =>
  resolveMediaUrl(
    project.images?.[0],
    createCardPlaceholder(`LAB ${project.id}`, '#16a34a', '#dcfce7')
  )

onMounted(() => {
  fetchProjects()
})
</script>

<template>
  <div class="research-page py-12">
    <div class="container mx-auto px-4">
      <div class="mb-16 text-center">
        <h1 class="mb-4 text-4xl font-bold text-gray-800">科研项目</h1>
        <p class="mx-auto max-w-2xl text-gray-500">
          立足前沿问题，围绕攻防实践、自动化分析和平台建设持续推进研究。
        </p>
      </div>

      <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 md:grid-cols-2">
        <div
          v-for="project in projects"
          :key="project.id"
          class="group overflow-hidden rounded-xl bg-white shadow-md transition-all duration-300 hover:shadow-xl"
        >
          <div class="relative h-48 overflow-hidden">
            <img
              :src="getProjectImage(project)"
              :alt="project.title"
              class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
            >
            <div class="absolute right-4 top-4">
              <el-tag :type="getStatusType(project.status)" effect="dark">
                {{ getStatusText(project.status) }}
              </el-tag>
            </div>
          </div>
          <div class="p-6">
            <h3 class="mb-3 text-xl font-bold text-gray-800">{{ project.title }}</h3>
            <p class="mb-4 text-sm text-gray-500">启动时间：{{ project.start_date || '待更新' }}</p>
            <div class="mb-4">
              <h4 class="mb-1 font-semibold text-gray-700">项目简介：</h4>
              <p class="text-sm leading-relaxed text-gray-600">{{ project.description }}</p>
            </div>
            <div>
              <h4 class="mb-1 font-semibold text-gray-700">主要成果：</h4>
              <p class="rounded-md bg-blue-50 p-3 text-sm text-blue-600">
                {{ project.achievements }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
