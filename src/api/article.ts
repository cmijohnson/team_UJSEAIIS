import request from '@/utils/request'

export const getArticles = (params: any) => {
  return request({
    url: '/articles',
    method: 'get',
    params
  })
}

export const getArticle = (id: number) => {
  return request({
    url: `/articles/${id}`,
    method: 'get'
  })
}

export const createArticle = (data: any) => {
  return request({
    url: '/articles',
    method: 'post',
    data
  })
}

export const updateArticle = (id: number, data: any) => {
  return request({
    url: `/articles/${id}`,
    method: 'put',
    data
  })
}

export const deleteArticle = (id: number) => {
  return request({
    url: `/articles/${id}`,
    method: 'delete'
  })
}
