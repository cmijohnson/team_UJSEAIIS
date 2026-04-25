<script setup lang="ts">
import { onMounted, ref } from 'vue'
import request from '@/utils/request'
import { createAvatarPlaceholder, resolveMediaUrl } from '@/utils/media'

interface Advisor {
  id: number
  name: string
  title: string
  avatar?: string
  profile: string
  research_fields: string
  contact?: string
}

const advisors = ref<Advisor[]>([
  {
    id: 1,
    name: '王教授',
    title: '计算机学院教授 / 博士生导师',
    avatar: '',
    profile: '长期从事网络空间安全、区块链安全与密码学研究，承担多项科研课题。',
    research_fields: '网络与信息安全、区块链安全、密码学',
    contact: 'wang@example.edu.cn'
  },
  {
    id: 2,
    name: '李副教授',
    title: '网络空间安全系副主任',
    avatar: '',
    profile: '聚焦软件安全与漏洞挖掘，指导学生参加多项高水平安全竞赛。',
    research_fields: '软件分析与测试、二进制安全、AI 安全',
    contact: 'li@example.edu.cn'
  }
])

const fetchAdvisors = async () => {
  try {
    const res: any = await request.get('/advisors')
    const list = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : []
    if (list.length > 0) {
      advisors.value = list
    }
  } catch (error) {
    console.error('Failed to fetch advisors, using mock data', error)
  }
}

onMounted(() => {
  fetchAdvisors()
})

const getAdvisorAvatar = (advisor: Advisor) =>
  resolveMediaUrl(
    advisor.avatar,
    createAvatarPlaceholder(advisor.name, '#7c3aed', '#c4b5fd')
  )
</script>

<template>
  <div class="advisors-page py-12">
    <div class="container mx-auto px-4">
      <div class="mb-16 text-center">
        <h1 class="mb-4 text-4xl font-bold text-gray-800">指导老师</h1>
        <p class="mx-auto max-w-2xl text-gray-500">
          学术研究与实战经验并重，为团队方向、训练方法和项目落地提供支持。
        </p>
      </div>

      <div class="mx-auto max-w-4xl space-y-12">
        <div
          v-for="advisor in advisors"
          :key="advisor.id"
          class="flex flex-col items-start gap-8 rounded-xl bg-white p-8 shadow-md transition-shadow hover:shadow-lg md:flex-row"
        >
          <div class="mx-auto h-32 w-32 flex-shrink-0 md:mx-0 md:h-48 md:w-48">
            <img
              :src="getAdvisorAvatar(advisor)"
              :alt="advisor.name"
              class="h-full w-full rounded-xl border border-gray-100 object-cover shadow-inner"
            >
          </div>

          <div class="flex-grow">
            <div class="mb-4 border-b border-gray-100 pb-4">
              <h2 class="mb-1 text-2xl font-bold text-gray-800">{{ advisor.name }}</h2>
              <p class="font-medium text-blue-600">{{ advisor.title }}</p>
            </div>

            <div class="space-y-4 text-gray-600">
              <p class="leading-relaxed">{{ advisor.profile }}</p>

              <div>
                <span class="mr-2 font-semibold text-gray-700">研究方向:</span>
                <span>{{ advisor.research_fields }}</span>
              </div>

              <div v-if="advisor.contact" class="flex items-center text-gray-500">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                  />
                </svg>
                <a
                  :href="`mailto:${advisor.contact}`"
                  class="transition-colors hover:text-blue-600"
                >
                  {{ advisor.contact }}
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
