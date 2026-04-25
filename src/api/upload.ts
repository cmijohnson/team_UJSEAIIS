import request from '@/utils/request'

export const uploadImage = (data: FormData) => {
  return request({
    url: '/upload/image',
    method: 'post',
    headers: {
      'Content-Type': 'multipart/form-data'
    },
    data
  })
}
