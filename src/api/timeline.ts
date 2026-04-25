import request from '@/utils/request'

export const getTimeline = () => {
  return request({
    url: '/timeline',
    method: 'get'
  })
}

export const createTimelineEvent = (data: any) => {
  return request({
    url: '/timeline',
    method: 'post',
    data
  })
}

export const updateTimelineEvent = (id: number, data: any) => {
  return request({
    url: `/timeline/${id}`,
    method: 'put',
    data
  })
}

export const deleteTimelineEvent = (id: number) => {
  return request({
    url: `/timeline/${id}`,
    method: 'delete'
  })
}
