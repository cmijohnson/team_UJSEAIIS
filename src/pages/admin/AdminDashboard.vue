<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { Collection, Document, Trophy, User } from '@element-plus/icons-vue'
import { getArticles } from '@/api/article'
import { getCompetitions } from '@/api/competition'
import { getMembers } from '@/api/member'
import { getProjects } from '@/api/project'

const router = useRouter()
const loading = ref(false)
const counts = ref({
  articles: 0,
  members: 0,
  competitions: 0,
  projects: 0
})

const stats = computed(() => [
  {
    title: '文章总数',
    value: counts.value.articles,
    icon: Document,
    color: 'text-blue-500',
    bg: 'bg-blue-100'
  },
  {
    title: '团队成员',
    value: counts.value.members,
    icon: User,
    color: 'text-green-500',
    bg: 'bg-green-100'
  },
  {
    title: '比赛奖项',
    value: counts.value.competitions,
    icon: Trophy,
    color: 'text-orange-500',
    bg: 'bg-orange-100'
  },
  {
    title: '科研项目',
    value: counts.value.projects,
    icon: Collection,
    color: 'text-indigo-500',
    bg: 'bg-indigo-100'
  }
])

const quickActions = [
  { title: '发布文章', path: '/admin/articles', button: '去管理文章' },
  { title: '维护成员', path: '/admin/members', button: '去管理成员' },
  { title: '录入奖项', path: '/admin/competitions', button: '去管理奖项' },
  { title: '更新项目', path: '/admin/projects', button: '去管理项目' }
]

const fetchStats = async () => {
  loading.value = true
  try {
    const [articleRes, memberRes, competitionRes, projectRes]: any = await Promise.all([
      getArticles({ page: 1, limit: 1 }),
      getMembers(),
      getCompetitions(),
      getProjects()
    ])

    counts.value = {
      articles: Number(articleRes?.meta?.total || 0),
      members: Array.isArray(memberRes) ? memberRes.length : 0,
      competitions: Array.isArray(competitionRes?.data)
        ? competitionRes.data.length
        : Array.isArray(competitionRes)
          ? competitionRes.length
          : 0,
      projects: Array.isArray(projectRes?.data)
        ? projectRes.data.length
        : Array.isArray(projectRes)
          ? projectRes.length
          : 0
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchStats()
})
</script>

<template>
  <div v-loading="loading">
    <h2 class="mb-6 text-2xl font-bold text-gray-800">控制台概览</h2>

    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
      <div
        v-for="(stat, index) in stats"
        :key="index"
        class="flex items-center rounded-lg bg-white p-6 shadow-sm"
      >
        <div :class="`mr-4 flex h-14 w-14 items-center justify-center rounded-full ${stat.bg} ${stat.color}`">
          <el-icon class="text-2xl"><component :is="stat.icon" /></el-icon>
        </div>
        <div>
          <p class="mb-1 text-sm text-gray-500">{{ stat.title }}</p>
          <p class="text-2xl font-bold text-gray-800">{{ stat.value }}</p>
        </div>
      </div>
    </div>

    <div class="mb-8 rounded-lg bg-white p-6 shadow-sm">
      <h3 class="mb-4 text-lg font-bold text-gray-800">快捷操作</h3>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div
          v-for="item in quickActions"
          :key="item.path"
          class="rounded-lg border border-gray-100 bg-gray-50 p-4"
        >
          <div class="mb-3 text-base font-semibold text-gray-800">{{ item.title }}</div>
          <el-button type="primary" link @click="router.push(item.path)">{{ item.button }}</el-button>
        </div>
      </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm">
      <h3 class="mb-4 text-lg font-bold text-gray-800">当前后台能力</h3>
      <div class="prose max-w-none text-gray-600">
        <p>这里可以统一维护站点文章、团队成员、比赛奖项、科研项目与发展时间轴。</p>
        <p>当前后台已支持以下内容维护：</p>
        <ul class="mt-2 list-disc space-y-2 pl-5">
          <li>文章发布、编辑、删除与分页查看</li>
          <li>团队成员的新增、编辑、删除与排序</li>
          <li>比赛奖项的录入、修改、删除与展示图维护</li>
          <li>科研项目的状态、时间、成果与图片维护</li>
          <li>发展时间轴事件维护</li>
        </ul>
      </div>
    </div>
  </div>
</template>
