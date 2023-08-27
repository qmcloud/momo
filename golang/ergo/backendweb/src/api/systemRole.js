import request from '@/utils/request'
// 角色管理
export function systemRoleList(data) {
    return request({
        url: '/systemRole/list',
        method: 'post',
        data
    })
}

export function systemRoleParent(data) {
    return request({
        url: '/systemRole/parentList',
        method: 'post',
        data
    })
}

export function systemRoleDelete(data) {
    return request({
        url: '/systemRole/delete',
        method: 'delete',
        data
    })
}

export function systemRoleDeleteBatch(data) {
    return request({
        url: '/systemRole/deleteBatch',
        method: 'delete',
        data
    })
}

export function systemRoleOne(data) {
    return request({
        url: '/systemRole/find',
        method: 'post',
        data
    })
}



export function systemRoleAdd(data) {
    return request({
        url: '/systemRole/add',
        method: 'post',
        data
    })
}

export function systemRoleUpdate(data) {
    return request({
        url: '/systemRole/update',
        method: 'put',
        data
    })
}






