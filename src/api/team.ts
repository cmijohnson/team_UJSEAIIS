import request from '@/utils/request'

export const getTeamInfo = () => {
  return request({
    url: '/team/info',
    method: 'get'
  })
}
