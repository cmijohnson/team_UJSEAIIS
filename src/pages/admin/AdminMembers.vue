<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Delete, Edit, Plus } from '@element-plus/icons-vue'
import { createMember, deleteMember, getMembers, updateMember } from '@/api/member'

interface MemberForm {
  id: number | null
  name: string
  position: string
  avatar: string
  bio: string
  sort_order: number
}

const loading = ref(false)
const dialogVisible = ref(false)
const isEdit = ref(false)
const members = ref<any[]>([])

const createDefaultForm = (): MemberForm => ({
  id: null,
  name: '',
  position: '',
  avatar: '',
  bio: '',
  sort_order: 0
})

const form = ref<MemberForm>(createDefaultForm())

const fetchMembers = async () => {
  loading.value = true
  try {
    const res: any = await getMembers()
    members.value = Array.isArray(res) ? res : []
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
    position: row.position || '',
    avatar: row.avatar || '',
    bio: row.bio || '',
    sort_order: Number(row.sort_order || 0)
  }
  isEdit.value = true
  dialogVisible.value = true
}

const handleDelete = (row: any) => {
  ElMessageBox.confirm(`确认删除成员“${row.name}”吗？`, '提示', {
    confirmButtonText: '删除',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(async () => {
    await deleteMember(row.id)
    ElMessage.success('成员已删除')
    fetchMembers()
  })
}

const handleSave = async () => {
  if (!form.value.name.trim() || !form.value.position.trim()) {
    ElMessage.warning('请填写成员姓名和职位')
    return
  }

  const payload = {
    name: form.value.name.trim(),
    position: form.value.position.trim(),
    avatar: form.value.avatar.trim(),
    bio: form.value.bio.trim(),
    sort_order: Number(form.value.sort_order || 0)
  }

  if (isEdit.value && form.value.id) {
    await updateMember(form.value.id, payload)
    ElMessage.success('成员信息已更新')
  } else {
    await createMember(payload)
    ElMessage.success('成员已创建')
  }

  dialogVisible.value = false
  fetchMembers()
}

onMounted(() => {
  fetchMembers()
})
</script>

<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-2xl font-bold text-gray-800">成员管理</h2>
      <el-button type="primary" :icon="Plus" @click="handleAdd">新增成员</el-button>
    </div>

    <el-card shadow="sm" v-loading="loading">
      <el-table :data="members" style="width: 100%">
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column label="头像" width="90">
          <template #default="scope">
            <el-image
              v-if="scope.row.avatar"
              :src="scope.row.avatar"
              fit="cover"
              preview-teleported
              class="h-12 w-12 rounded-full"
            />
            <div
              v-else
              class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-xs text-gray-400"
            >
              无图
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="name" label="姓名" width="140" />
        <el-table-column prop="position" label="职位" width="160" />
        <el-table-column prop="sort_order" label="排序" width="100" />
        <el-table-column prop="bio" label="简介" show-overflow-tooltip />
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

    <el-dialog v-model="dialogVisible" :title="isEdit ? '编辑成员' : '新增成员'" width="620px">
      <el-form :model="form" label-width="90px">
        <el-form-item label="姓名" required>
          <el-input v-model="form.name" placeholder="请输入成员姓名" />
        </el-form-item>
        <el-form-item label="职位" required>
          <el-input v-model="form.position" placeholder="例如：队长 / 核心成员 / 指导老师" />
        </el-form-item>
        <el-form-item label="头像链接">
          <el-input v-model="form.avatar" placeholder="请输入头像 URL" />
        </el-form-item>
        <el-form-item label="排序">
          <el-input-number v-model="form.sort_order" :min="0" :max="999" class="w-full" />
        </el-form-item>
        <el-form-item label="成员简介">
          <el-input
            v-model="form.bio"
            type="textarea"
            :rows="4"
            placeholder="请输入成员介绍"
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
