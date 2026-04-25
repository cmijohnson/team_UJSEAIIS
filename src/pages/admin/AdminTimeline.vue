<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Delete, Edit, Plus } from '@element-plus/icons-vue'
import {
  createTimelineEvent,
  deleteTimelineEvent,
  getTimeline,
  updateTimelineEvent
} from '@/api/timeline'

const dialogVisible = ref(false)
const isEdit = ref(false)
const loading = ref(false)

const form = ref({
  id: null as number | null,
  year: new Date().getFullYear(),
  title: '',
  description: '',
  event_date: ''
})

const timeline = ref<any[]>([])

const fetchTimeline = async () => {
  loading.value = true
  try {
    const res: any = await getTimeline()
    timeline.value = Array.isArray(res) ? res : []
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    year: new Date().getFullYear(),
    title: '',
    description: '',
    event_date: ''
  }
}

const handleAdd = () => {
  resetForm()
  isEdit.value = false
  dialogVisible.value = true
}

const handleEdit = (row: any) => {
  form.value = {
    id: row.id,
    year: row.year,
    title: row.title,
    description: row.description || '',
    event_date: row.event_date || ''
  }
  isEdit.value = true
  dialogVisible.value = true
}

const handleDelete = (row: any) => {
  ElMessageBox.confirm('确定要删除这个事件吗？', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(async () => {
    try {
      await deleteTimelineEvent(row.id)
      ElMessage.success('删除成功')
      fetchTimeline()
    } catch (error) {
      console.error(error)
      ElMessage.error('删除失败')
    }
  })
}

const handleSave = async () => {
  if (!form.value.year || !form.value.title) {
    ElMessage.warning('年份和标题不能为空')
    return
  }

  try {
    if (isEdit.value && form.value.id) {
      await updateTimelineEvent(form.value.id, form.value)
      ElMessage.success('更新成功')
    } else {
      await createTimelineEvent(form.value)
      ElMessage.success('保存成功')
    }
    dialogVisible.value = false
    fetchTimeline()
  } catch (error) {
    console.error(error)
    ElMessage.error('保存失败')
  }
}

onMounted(() => {
  fetchTimeline()
})
</script>

<template>
  <div class="admin-timeline">
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-2xl font-bold text-gray-800">时间轴管理</h2>
      <el-button type="primary" :icon="Plus" @click="handleAdd">新增事件</el-button>
    </div>

    <el-card shadow="sm" v-loading="loading">
      <el-table :data="timeline" style="width: 100%">
        <el-table-column prop="year" label="年份" width="100" sortable />
        <el-table-column prop="event_date" label="具体日期" width="140" />
        <el-table-column prop="title" label="事件标题" width="220" />
        <el-table-column prop="description" label="事件描述" />
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

    <el-dialog v-model="dialogVisible" :title="isEdit ? '编辑事件' : '新增事件'" width="500px">
      <el-form :model="form" label-width="80px">
        <el-form-item label="年份" required>
          <el-input-number v-model="form.year" :min="2000" :max="2050" class="w-full" />
        </el-form-item>
        <el-form-item label="具体日期">
          <el-date-picker
            v-model="form.event_date"
            type="date"
            placeholder="选择日期"
            value-format="YYYY-MM-DD"
            class="w-full"
          />
        </el-form-item>
        <el-form-item label="标题" required>
          <el-input v-model="form.title" placeholder="请输入事件标题" />
        </el-form-item>
        <el-form-item label="描述">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="4"
            placeholder="请输入事件描述"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogVisible = false">取消</el-button>
          <el-button type="primary" @click="handleSave">确认</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>
