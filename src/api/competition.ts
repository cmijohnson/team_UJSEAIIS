import request from '@/utils/request'

export const getCompetitions = () => {
  return request({
    url: '/competitions',
    method: 'get'
  })
}

export const createCompetition = (data: any) => {
  return request({
    url: '/competitions',
    method: 'post',
    data
  })
}

export const updateCompetition = (id: number, data: any) => {
  return request({
    url: `/competitions/${id}`,
    method: 'put',
    data
  })
}

export const deleteCompetition = (id: number) => {
  return request({
    url: `/competitions/${id}`,
    method: 'delete'
  })
}
