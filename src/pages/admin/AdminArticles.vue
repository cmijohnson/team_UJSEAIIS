<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Delete, Edit, Plus } from '@element-plus/icons-vue'
import { MdEditor } from 'md-editor-v3'
import 'md-editor-v3/lib/style.css'
import {
  createArticle,
  deleteArticle,
  getArticle,
  getArticles,
  updateArticle
} from '@/api/article'
import { mdEditorSafeProps } from '@/utils/media'

const isEditing = ref(false)
const loading = ref(false)
const articles = ref<any[]>([])
const articleForm = ref({
  id: null as number | null,
  title: '',
  category: '比赛动态',
  content: '',
  cover_image: ''
})

const pagination = ref({
  current_page: 1,
  per_page: 10,
  total: 0
})

const categories = ['比赛动态', '技术分享', '团队通知']

const fetchArticles = async () => {
  loading.value = true
  try {
    const res: any = await getArticles({
      page: pagination.value.current_page,
      limit: pagination.value.per_page
    })
    articles.value = Array.isArray(res?.data) ? res.data : []
    pagination.value.total = res?.meta?.total ?? articles.value.length
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  articleForm.value = {
    id: null,
    title: '',
    category: '比赛动态',
    content: '',
    cover_image: ''
  }
}

const handleAdd = () => {
  resetForm()
  isEditing.value = true
}

const handleEdit = async (row: any) => {
  loading.value = true
  try {
    const res: any = await getArticle(row.id)
    articleForm.value = {
      id: res.id,
      title: res.title || '',
      category: res.category || '比赛动态',
      content: res.content || '',
      cover_image: res.cover_image || ''
    }
    isEditing.value = true
  } catch (error) {
    console.error(error)
    ElMessage.error('获取文章详情失败')
  } finally {
    loading.value = false
  }
}

const handleDelete = (row: any) => {
  ElMessageBox.confirm('确定要删除这篇文章吗？', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(async () => {
    try {
      await deleteArticle(row.id)
      ElMessage.success('删除成功')
      fetchArticles()
    } catch (error) {
      console.error(error)
      ElMessage.error('删除失败')
    }
  })
}

const saveArticle = async () => {
  if (!articleForm.value.title || !articleForm.value.content) {
    ElMessage.warning('请填写标题和内容')
    return
  }

  loading.value = true
  try {
    if (articleForm.value.id) {
      await updateArticle(articleForm.value.id, articleForm.value)
      ElMessage.success('更新成功')
    } else {
      await createArticle(articleForm.value)
      ElMessage.success('发布成功')
    }
    isEditing.value = false
    fetchArticles()
  } catch (error) {
    console.error(error)
    ElMessage.error('保存失败')
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page: number) => {
  pagination.value.current_page = page
  fetchArticles()
}

onMounted(() => {
  fetchArticles()
})
</script>

<template>
  <div class="admin-articles">
    <div v-if="!isEditing">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">文章管理</h2>
        <el-button type="primary" :icon="Plus" @click="handleAdd">发布新文章</el-button>
      </div>

      <el-card shadow="sm" v-loading="loading">
        <el-table :data="articles" style="width: 100%">
          <el-table-column prop="id" label="ID" width="80" />
          <el-table-column prop="title" label="标题" />
          <el-table-column prop="category" label="分类" width="120">
            <template #default="scope">
              <el-tag>{{ scope.row.category }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="view_count" label="浏览量" width="100" />
          <el-table-column prop="created_at" label="发布时间" width="180" />
          <el-table-column label="操作" width="180" fixed="right">
            <template #default="scope">
              <el-button size="small" :icon="Edit" @click="handleEdit(scope.row)">编辑</el-button>
              <el-button
                size="small"
                type="danger"
                :icon="Delete"
                @click="handleDelete(scope.row)"
              >
                删除
              </el-button>
            </template>
          </el-table-column>
        </el-table>

        <div class="mt-4 flex justify-end">
          <el-pagination
            background
            layout="prev, pager, next"
            :total="pagination.total"
            :page-size="pagination.per_page"
            :current-page="pagination.current_page"
            @current-change="handlePageChange"
          />
        </div>
      </el-card>
    </div>

    <div v-else v-loading="loading">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
          {{ articleForm.id ? '编辑文章' : '发布新文章' }}
        </h2>
        <div class="space-x-3">
          <el-button @click="isEditing = false">取消</el-button>
          <el-button type="primary" @click="saveArticle">保存</el-button>
        </div>
      </div>

      <el-card shadow="sm" class="mb-6">
        <el-form :model="articleForm" label-width="80px">
          <el-row :gutter="20">
            <el-col :span="16">
              <el-form-item label="文章标题" required>
                <el-input v-model="articleForm.title" placeholder="请输入文章标题" />
              </el-form-item>
            </el-col>
            <el-col :span="8">
              <el-form-item label="文章分类" required>
                <el-select v-model="articleForm.category" class="w-full">
                  <el-option
                    v-for="category in categories"
                    :key="category"
                    :label="category"
                    :value="category"
                  />
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>

          <el-form-item label="封面图">
            <el-input v-model="articleForm.cover_image" placeholder="请输入封面图片地址" />
          </el-form-item>
        </el-form>
      </el-card>

      <div class="h-[600px] overflow-hidden rounded-lg border bg-white">
        <MdEditor
          v-model="articleForm.content"
          class="h-full"
          v-bind="mdEditorSafeProps"
          :no-prettier="true"
          :show-toolbar-name="true"
          :toolbars-exclude="['mermaid', 'katex', 'prettier', 'github']"
        />
      </div>
    </div>
  </div>
</template>
