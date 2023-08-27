import request from '@/utils/request'
// 菜单管理
export function systemMenuList(data) {
    return request({
        url: '/systemMenu/list',
        method: 'post',
        data
    })
}

export function systemMenuParent(data) {
    return request({
        url: '/systemMenu/parentList',
        method: 'post',
        data
    })
}

export function treeList(data) {
    return request({
        url: '/systemMenu/treeList',
        method: 'post',
        data
    })
}


export function systemMenuDelete(data) {
    return request({
        url: '/systemMenu/delete',
        method: 'delete',
        data
    })
}

export function systemMenuDeleteBatch(data) {
    return request({
        url: '/systemMenu/deleteBatch',
        method: 'delete',
        data
    })
}

export function systemMenuOne(data) {
    return request({
        url: '/systemMenu/find',
        method: 'post',
        data
    })
}



export function systemMenuAdd(data) {
    return request({
        url: '/systemMenu/add',
        method: 'post',
        data
    })
}

export function systemMenuUpdate(data) {
    return request({
        url: '/systemMenu/update',
        method: 'put',
        data
    })
}






