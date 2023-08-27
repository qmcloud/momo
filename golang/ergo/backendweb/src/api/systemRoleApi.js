import request from '@/utils/request'
// 角色Api关系
export function systemRoleApiList(data) {
    return request({
        url: '/systemRoleApi/list',
        method: 'post',
        data
    })
}

export function systemRoleApisByRoleId(data) {
    return request({
        url: '/systemRoleApi/byRoleId',
        method: 'post',
        data
    })
}

export function systemRoleApiDelete(data) {
    return request({
        url: '/systemRoleApi/delete',
        method: 'delete',
        data
    })
}

export function systemRoleApiDeleteBatch(data) {
    return request({
        url: '/systemRoleApi/deleteBatch',
        method: 'delete',
        data
    })
}

export function systemRoleApiOne(data) {
    return request({
        url: '/systemRoleApi/find',
        method: 'post',
        data
    })
}



export function systemRoleApiAdd(data) {
    return request({
        url: '/systemRoleApi/add',
        method: 'post',
        data
    })
}

export function systemRoleApiUpdate(data) {
    return request({
        url: '/systemRoleApi/update',
        method: 'put',
        data
    })
}






