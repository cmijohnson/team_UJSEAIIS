<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { getArticles } from '@/api/article'
import { createCardPlaceholder, resolveMediaUrl } from '@/utils/media'

interface ArticleSummary {
  id: number
  title: string
  category: string
  cover_image?: string
  view_count?: number
  created_at?: string
  author_name?: string
}

const router = useRouter()
const loading = ref(false)
const articles = ref<ArticleSummary[]>([])
const pagination = ref({
  current_page: 1,
  per_page: 9,
  total: 0
})

const fetchArticles = async () => {
  loading.value = true
  try {
    const res: any = await getArticles({
      page: pagination.value.current_page,
      limit: pagination.value.per_page
    })

    const list = Array.isArray(res?.data) ? res.data : []
    articles.value = list
    pagination.value.total = res?.meta?.total ?? list.length
  } catch (error) {
    console.error('Failed to fetch articles', error)
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page: number) => {
  pagination.value.current_page = page
  fetchArticles()
}

const getCoverImage = (article: ArticleSummary) =>
  resolveMediaUrl(
    article.cover_image,
    createCardPlaceholder(`ARTICLE ${article.id}`, '#1d4ed8', '#dbeafe')
  )

onMounted(() => {
  fetchArticles()
})
</script>

<template>
  <div class="articles-page py-12">
    <div class="container mx-auto px-4">
      <div class="mb-12 text-center">
        <h1 class="mb-4 text-4xl font-bold text-gray-800">文章动态</h1>
        <p class="mx-auto max-w-2xl text-gray-500">
          汇总团队的比赛复盘、技术分享、研究进展与日常通知。
        </p>
      </div>

      <el-card shadow="never" class="border-0 bg-transparent" v-loading="loading">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3">
          <article
            v-for="article in articles"
            :key="article.id"
            class="cursor-pointer overflow-hidden rounded-xl bg-white shadow-md transition-shadow hover:shadow-xl"
            @click="router.push(`/article/${article.id}`)"
          >
            <img :src="getCoverImage(article)" :alt="article.title" class="h-52 w-full object-cover">
            <div class="p-6">
              <div class="mb-3 flex items-center justify-between text-sm text-gray-500">
                <span>{{ article.category || '未分类' }}</span>
                <span>{{ article.created_at?.split(' ')[0] || '' }}</span>
              </div>
              <h2 class="mb-3 line-clamp-2 text-xl font-semibold text-gray-800">
                {{ article.title }}
              </h2>
              <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ article.author_name || '团队管理员' }}</span>
                <span>阅读 {{ article.view_count || 0 }}</span>
              </div>
            </div>
          </article>
        </div>

        <el-empty v-if="!loading && articles.length === 0" description="暂无文章内容" />

        <div v-if="pagination.total > pagination.per_page" class="mt-8 flex justify-end">
          <el-pagination
            background
            layout="prev, pager, next"
            :current-page="pagination.current_page"
            :page-size="pagination.per_page"
            :total="pagination.total"
            @current-change="handlePageChange"
          />
        </div>
      </el-card>
    </div>
  </div>
</template>
