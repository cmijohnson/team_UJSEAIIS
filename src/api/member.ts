import request from '@/utils/request'

export const getMembers = () => {
  return request({
    url: '/members',
    method: 'get'
  })
}

export const createMember = (data: any) => {
  return request({
    url: '/members',
    method: 'post',
    data
  })
}

export const updateMember = (id: number, data: any) => {
  return request({
    url: `/members/${id}`,
    method: 'put',
    data
  })
}

export const deleteMember = (id: number) => {
  return request({
    url: `/members/${id}`,
    method: 'delete'
  })
}
