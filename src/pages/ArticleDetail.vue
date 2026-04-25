<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ArrowLeft, Calendar, Loading, View } from '@element-plus/icons-vue'
import { MdPreview } from 'md-editor-v3'
import 'md-editor-v3/lib/preview.css'
import { getArticle } from '@/api/article'
import {
  createCardPlaceholder,
  mdEditorSafeProps,
  resolveMediaUrl
} from '@/utils/media'

interface Article {
  id: number
  title: string
  content: string
  category: string
  view_count: number
  created_at: string
  cover_image: string
}

const route = useRoute()
const router = useRouter()

const article = ref<Article>({
  id: 0,
  title: '加载中...',
  content: '',
  category: '',
  view_count: 0,
  created_at: '',
  cover_image: ''
})

const loading = ref(true)
const articleId = computed(() => Number(route.params.id))
const previewId = computed(() => `article-preview-${article.value.id || articleId.value || 'loading'}`)

const fetchArticleData = async () => {
  loading.value = true
  try {
    const res: any = await getArticle(articleId.value)
    if (res) {
      article.value = res
    }
  } catch (error) {
    console.error('Failed to fetch article', error)
    article.value = {
      id: articleId.value,
      title: '文章加载失败',
      content: '# 暂时无法获取文章内容\n\n请稍后再试，或检查后端服务是否已启动。',
      category: '系统提示',
      view_count: 0,
      created_at: new Date().toISOString(),
      cover_image: ''
    }
  } finally {
    loading.value = false
  }
}

watch(
  () => route.params.id,
  () => {
    fetchArticleData()
  },
  { immediate: true }
)

const formattedDate = computed(() => article.value.created_at?.split(' ')[0] || '')
const coverImage = computed(() =>
  resolveMediaUrl(
    article.value.cover_image,
    createCardPlaceholder(
      `ARTICLE ${article.value.id || articleId.value || 0}`,
      '#1d4ed8',
      '#dbeafe'
    )
  )
)
</script>

<template>
  <div class="article-page py-12">
    <div class="container mx-auto max-w-4xl px-4">
      <el-button :icon="ArrowLeft" class="mb-6" @click="router.back()">返回</el-button>

      <div v-if="loading" class="py-20 text-center">
        <el-icon class="is-loading text-4xl text-blue-500"><Loading /></el-icon>
      </div>

      <div v-else class="overflow-hidden rounded-xl bg-white shadow-md">
        <div class="h-64 w-full">
          <img :src="coverImage" alt="Cover" class="h-full w-full object-cover">
        </div>

        <div class="p-8 md:p-12">
          <h1 class="mb-6 text-3xl font-bold text-gray-900">{{ article.title }}</h1>

          <div class="mb-10 flex items-center space-x-6 border-b border-gray-100 pb-6 text-sm text-gray-500">
            <span class="flex items-center">
              <el-icon class="mr-1"><Calendar /></el-icon>
              {{ formattedDate }}
            </span>
            <span class="flex items-center rounded bg-blue-50 px-2 py-1 text-blue-600">
              {{ article.category }}
            </span>
            <span class="flex items-center">
              <el-icon class="mr-1"><View /></el-icon>
              阅读: {{ article.view_count }}
            </span>
          </div>

          <div class="markdown-body">
            <MdPreview
              :editorId="previewId"
              :modelValue="article.content"
              v-bind="mdEditorSafeProps"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.markdown-body {
  font-size: 16px;
  line-height: 1.8;
}
</style>
