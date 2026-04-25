import request from '@/utils/request'

export const getProjects = () => {
  return request({
    url: '/projects',
    method: 'get'
  })
}

export const createProject = (data: any) => {
  return request({
    url: '/projects',
    method: 'post',
    data
  })
}

export const updateProject = (id: number, data: any) => {
  return request({
    url: `/projects/${id}`,
    method: 'put',
    data
  })
}

export const deleteProject = (id: number) => {
  return request({
    url: `/projects/${id}`,
    method: 'delete'
  })
}
