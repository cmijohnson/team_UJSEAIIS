<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Delete, Edit, Plus } from '@element-plus/icons-vue'
import {
  createCompetition,
  deleteCompetition,
  getCompetitions,
  updateCompetition
} from '@/api/competition'

interface CompetitionForm {
  id: number | null
  name: string
  competition_date: string
  result: string
  description: string
  image_url: string
}

const loading = ref(false)
const dialogVisible = ref(false)
const isEdit = ref(false)
const competitions = ref<any[]>([])

const createDefaultForm = (): CompetitionForm => ({
  id: null,
  name: '',
  competition_date: '',
  result: '',
  description: '',
  image_url: ''
})

const form = ref<CompetitionForm>(createDefaultForm())

const fetchCompetitions = async () => {
  loading.value = true
  try {
    const res: any = await getCompetitions()
    competitions.value = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : []
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
    name: row.name || '',
    competition_date: row.competition_date || '',
    result: row.result || '',
    description: row.description || '',
    image_url: Array.isArray(row.images) ? row.images[0] || '' : ''
  }
  isEdit.value = true
  dialogVisible.value = true
}

const handleDelete = (row: any) => {
  ElMessageBox.confirm(`确认删除奖项“${row.name}”吗？`, '提示', {
    confirmButtonText: '删除',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(async () => {
    await deleteCompetition(row.id)
    ElMessage.success('奖项已删除')
    fetchCompetitions()
  })
}

const handleSave = async () => {
  if (!form.value.name.trim() || !form.value.result.trim()) {
    ElMessage.warning('请填写比赛名称和获奖结果')
    return
  }

  const payload = {
    name: form.value.name.trim(),
    competition_date: form.value.competition_date || null,
    result: form.value.result.trim(),
    description: form.value.description.trim(),
    images: form.value.image_url.trim() ? [form.value.image_url.trim()] : []
  }

  if (isEdit.value && form.value.id) {
    await updateCompetition(form.value.id, payload)
    ElMessage.success('奖项信息已更新')
  } else {
    await createCompetition(payload)
    ElMessage.success('奖项已创建')
  }

  dialogVisible.value = false
  fetchCompetitions()
}

onMounted(() => {
  fetchCompetitions()
})
</script>

<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-2xl font-bold text-gray-800">比赛奖项管理</h2>
      <el-button type="primary" :icon="Plus" @click="handleAdd">新增奖项</el-button>
    </div>

    <el-card shadow="sm" v-loading="loading">
      <el-table :data="competitions" style="width: 100%">
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="比赛名称" min-width="220" />
        <el-table-column prop="competition_date" label="比赛日期" width="140" />
        <el-table-column prop="result" label="获奖结果" width="160">
          <template #default="scope">
            <el-tag type="warning">{{ scope.row.result }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="description" label="说明" show-overflow-tooltip />
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

    <el-dialog v-model="dialogVisible" :title="isEdit ? '编辑奖项' : '新增奖项'" width="680px">
      <el-form :model="form" label-width="90px">
        <el-form-item label="比赛名称" required>
          <el-input v-model="form.name" placeholder="请输入比赛名称" />
        </el-form-item>
        <el-form-item label="比赛日期">
          <el-date-picker
            v-model="form.competition_date"
            type="date"
            value-format="YYYY-MM-DD"
            placeholder="请选择比赛日期"
            class="w-full"
          />
        </el-form-item>
        <el-form-item label="获奖结果" required>
          <el-input v-model="form.result" placeholder="例如：国赛一等奖 / 省赛金奖" />
        </el-form-item>
        <el-form-item label="展示图片">
          <el-input v-model="form.image_url" placeholder="请输入展示图 URL" />
        </el-form-item>
        <el-form-item label="奖项说明">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="4"
            placeholder="请输入比赛背景或成果说明"
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
