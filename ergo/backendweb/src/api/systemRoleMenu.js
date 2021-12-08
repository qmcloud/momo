import request from '@/utils/request'
// 角色菜单关系
export function systemRoleMenuList(data) {
    return request({
        url: '/systemRoleMenu/list',
        method: 'post',
        data
    })
}

export function systemRoleMenuDelete(data) {
    return request({
        url: '/systemRoleMenu/delete',
        method: 'delete',
        data
    })
}

export function systemRoleMenuDeleteBatch(data) {
    return request({
        url: '/systemRoleMenu/deleteBatch',
        method: 'delete',
        data
    })
}
export function systemRoleMenusByRoleId(data) {
    return request({
        url: '/systemRoleMenu/byRoleId',
        method: 'post',
        data
    })
}


export function systemRoleMenuOne(data) {
    return request({
        url: '/systemRoleMenu/find',
        method: 'post',
        data
    })
}



export function systemRoleMenuAdd(data) {
    return request({
        url: '/systemRoleMenu/add',
        method: 'post',
        data
    })
}

export function systemRoleMenuUpdate(data) {
    return request({
        url: '/systemRoleMenu/update',
        method: 'put',
        data
    })
}






