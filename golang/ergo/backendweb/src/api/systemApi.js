import request from '@/utils/request'
// Api管理
export function systemApiList(data) {
  return request({
    url: '/systemApi/list',
    method: 'post',
    data
  })
}

export function systemApiDelete(data) {
  return request({
    url: '/systemApi/delete',
    method: 'delete',
    data
  })
}

export function systemApiDeleteBatch(data) {
  return request({
    url: '/systemApi/deleteBatch',
    method: 'delete',
    data
  })
}

export function systemApiOne(data) {
  return request({
    url: '/systemApi/find',
    method: 'post',
    data
  })
}

export function systemApiAdd(data) {
  return request({
    url: '/systemApi/add',
    method: 'post',
    data
  })
}

export function systemApiUpdate(data) {
  return request({
    url: '/systemApi/update',
    method: 'put',
    data
  })
}

