<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Delete, Edit, Plus } from '@element-plus/icons-vue'
import { createProject, deleteProject, getProjects, updateProject } from '@/api/project'

interface ProjectForm {
  id: number | null
  title: string
  status: string
  start_date: string
  end_date: string
  description: string
  achievements: string
  image_url: string
}

const statusOptions = [
  { label: '进行中', value: 'ongoing', type: 'success' },
  { label: '已完成', value: 'completed', type: 'info' },
  { label: '规划中', value: 'planned', type: 'warning' }
]

const loading = ref(false)
const dialogVisible = ref(false)
const isEdit = ref(false)
const projects = ref<any[]>([])

const createDefaultForm = (): ProjectForm => ({
  id: null,
  title: '',
  status: 'ongoing',
  start_date: '',
  end_date: '',
  description: '',
  achievements: '',
  image_url: ''
})

const form = ref<ProjectForm>(createDefaultForm())

const getStatusConfig = (status: string) =>
  statusOptions.find((item) => item.value === status) || { label: status || '未知', type: 'info' }

const fetchProjects = async () => {
  loading.value = true
  try {
    const res: any = await getProjects()
    projects.value = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : []
  } finally {
    loading.value = false
  }
}

const handleAdd = () => {
  form.value = createDefaultForm()
  isEdit.value = false
  dialogVisible.value = true
}

const handleEdit = (row: any) => {
  form.value = {
    id: row.id,
    title: row.title || '',
    status: row.status || 'ongoing',
    start_date: row.start_date || '',
    end_date: row.end_date || '',
    description: row.description || '',
    achievements: row.achievements || '',
    image_url: Array.isArray(row.images) ? row.images[0] || '' : ''
  }
  isEdit.value = true
  dialogVisible.value = true
}

const handleDelete = (row: any) => {
  ElMessageBox.confirm(`确认删除项目“${row.title}”吗？`, '提示', {
    confirmButtonText: '删除',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(async () => {
    await deleteProject(row.id)
    ElMessage.success('项目已删除')
    fetchProjects()
  })
}

const handleSave = async () => {
  if (!form.value.title.trim() || !form.value.status) {
    ElMessage.warning('请填写项目名称和状态')
    return
  }

  const payload = {
    title: form.value.title.trim(),
    status: form.value.status,
    start_date: form.value.start_date || null,
    end_date: form.value.end_date || null,
    description: form.value.description.trim(),
    achievements: form.value.achievements.trim(),
    images: form.value.image_url.trim() ? [form.value.image_url.trim()] : []
  }

  if (isEdit.value && form.value.id) {
    await updateProject(form.value.id, payload)
    ElMessage.success('项目已更新')
  } else {
    await createProject(payload)
    ElMessage.success('项目已创建')
  }

  dialogVisible.value = false
  fetchProjects()
}

onMounted(() => {
  fetchProjects()
})
</script>

<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-2xl font-bold text-gray-800">科研项目管理</h2>
      <el-button type="primary" :icon="Plus" @click="handleAdd">新增项目</el-button>
    </div>

    <el-card shadow="sm" v-loading="loading">
      <el-table :data="projects" style="width: 100%">
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="title" label="项目名称" min-width="220" />
        <el-table-column label="状态" width="120">
          <template #default="scope">
            <el-tag :type="getStatusConfig(scope.row.status).type">
              {{ getStatusConfig(scope.row.status).label }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="start_date" label="开始时间" width="140" />
        <el-table-column prop="end_date" label="结束时间" width="140" />
        <el-table-column prop="achievements" label="项目成果" show-overflow-tooltip />
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
    </el-card>

    <el-dialog v-model="dialogVisible" :title="isEdit ? '编辑项目' : '新增项目'" width="720px">
      <el-form :model="form" label-width="90px">
        <el-form-item label="项目名称" required>
          <el-input v-model="form.title" placeholder="请输入项目名称" />
        </el-form-item>
        <el-form-item label="项目状态" required>
          <el-select v-model="form.status" class="w-full">
            <el-option
              v-for="item in statusOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
        </el-form-item>
        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="开始时间">
              <el-date-picker
                v-model="form.start_date"
                type="date"
                value-format="YYYY-MM-DD"
                placeholder="请选择开始时间"
                class="w-full"
              />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="结束时间">
              <el-date-picker
                v-model="form.end_date"
                type="date"
                value-format="YYYY-MM-DD"
                placeholder="请选择结束时间"
                class="w-full"
              />
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="展示图片">
          <el-input v-model="form.image_url" placeholder="请输入展示图 URL" />
        </el-form-item>
        <el-form-item label="项目简介">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="4"
            placeholder="请输入项目简介"
          />
        </el-form-item>
        <el-form-item label="项目成果">
          <el-input
            v-model="form.achievements"
            type="textarea"
            :rows="4"
            placeholder="请输入项目成果或阶段性产出"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleSave">保存</el-button>
      </template>
    </el-dialog>
  </div>
</template>
