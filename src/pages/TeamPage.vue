<script setup lang="ts">
import { onMounted, ref } from 'vue'
import request from '@/utils/request'
import { createAvatarPlaceholder, resolveMediaUrl } from '@/utils/media'

interface Member {
  id: number
  name: string
  position: string
  bio: string
  avatar?: string
}

interface HistoryItem {
  id: number
  year: number
  title: string
  description: string
  event_date?: string
}

const members = ref<Member[]>([
  {
    id: 1,
    name: '张三',
    position: '队长',
    bio: '负责 Web 安全方向，擅长漏洞分析与竞赛组织。',
    avatar: ''
  },
  {
    id: 2,
    name: '李四',
    position: '核心成员',
    bio: '专注逆向工程与二进制分析，参与多个攻防项目。',
    avatar: ''
  },
  {
    id: 3,
    name: '王五',
    position: '开发工程师',
    bio: '负责平台开发与基础设施建设，支持团队研发交付。',
    avatar: ''
  },
  {
    id: 4,
    name: '赵六',
    position: '安全研究员',
    bio: '研究区块链与智能合约安全，参与审计与工具建设。',
    avatar: ''
  }
])

const history = ref<HistoryItem[]>([
  {
    id: 1,
    year: 2023,
    event_date: '2023-11-01',
    title: '获得全国总决赛特等奖',
    description: '团队在全国大学生信息安全竞赛中取得突破性成绩。'
  },
  {
    id: 2,
    year: 2022,
    event_date: '2022-09-15',
    title: '团队正式成立',
    description: '由多位安全方向学生联合发起，开始系统化开展竞赛与研究。'
  }
])

const fetchTeamInfo = async () => {
  try {
    const res: any = await request.get('/team/info')
    if (Array.isArray(res?.members) && res.members.length > 0) {
      members.value = res.members
    }
    if (Array.isArray(res?.history) && res.history.length > 0) {
      history.value = res.history
    }
  } catch (error) {
    console.error('Failed to fetch team info, using mock data', error)
  }
}

onMounted(() => {
  fetchTeamInfo()
})

const getMemberAvatar = (member: Member) =>
  resolveMediaUrl(
    member.avatar,
    createAvatarPlaceholder(member.name, '#2563eb', '#60a5fa')
  )
</script>

<template>
  <div class="team-page py-12">
    <div class="container mx-auto px-4">
      <div class="mb-16 text-center">
        <h1 class="mb-4 text-4xl font-bold text-gray-800">队伍风采</h1>
        <p class="mx-auto max-w-2xl text-gray-500">
          一群持续投入安全技术、攻防实战和工程建设的同学，因为共同目标聚在一起。
        </p>
      </div>

      <section class="mb-20">
        <h2 class="relative mb-10 pb-4 text-center text-3xl font-bold text-gray-800">
          核心成员
          <span
            class="absolute bottom-0 left-1/2 h-1 w-12 -translate-x-1/2 rounded-full bg-blue-600"
          ></span>
        </h2>
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="member in members"
            :key="member.id"
            class="rounded-xl bg-white p-6 text-center shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
          >
            <div class="mx-auto mb-4 h-32 w-32 overflow-hidden rounded-full border-4 border-blue-100">
              <img
                :src="getMemberAvatar(member)"
                :alt="member.name"
                class="h-full w-full object-cover"
              >
            </div>
            <h3 class="mb-1 text-xl font-bold text-gray-800">{{ member.name }}</h3>
            <p class="mb-3 font-medium text-orange-500">{{ member.position }}</p>
            <p class="text-sm text-gray-500">{{ member.bio }}</p>
          </div>
        </div>
      </section>

      <section>
        <h2 class="relative mb-10 pb-4 text-center text-3xl font-bold text-gray-800">
          发展历程
          <span
            class="absolute bottom-0 left-1/2 h-1 w-12 -translate-x-1/2 rounded-full bg-orange-500"
          ></span>
        </h2>

        <div class="mx-auto max-w-4xl">
          <el-timeline>
            <el-timeline-item
              v-for="item in history"
              :key="item.id"
              :timestamp="item.event_date || String(item.year)"
              placement="top"
              size="large"
              type="primary"
              hollow
            >
              <el-card class="transition-shadow hover:shadow-md">
                <h4 class="mb-2 text-xl font-bold text-gray-800">{{ item.title }}</h4>
                <p class="text-gray-600">{{ item.description }}</p>
              </el-card>
            </el-timeline-item>
          </el-timeline>
        </div>
      </section>
    </div>
  </div>
</template>
