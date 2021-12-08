import request from '@/utils/request'
// 部门管理
export function systemDepartmentList(data) {
    return request({
        url: '/systemDepartment/list',
        method: 'post',
        data
    })
}

export function systemDepartmentParent(data) {
    return request({
        url: '/systemDepartment/parentList',
        method: 'post',
        data
    })
}

export function systemDepartmentDelete(data) {
    return request({
        url: '/systemDepartment/delete',
        method: 'delete',
        data
    })
}

export function systemDepartmentDeleteBatch(data) {
    return request({
        url: '/systemDepartment/deleteBatch',
        method: 'delete',
        data
    })
}

export function systemDepartmentOne(data) {
    return request({
        url: '/systemDepartment/find',
        method: 'post',
        data
    })
}



export function systemDepartmentAdd(data) {
    return request({
        url: '/systemDepartment/add',
        method: 'post',
        data
    })
}

export function systemDepartmentUpdate(data) {
    return request({
        url: '/systemDepartment/update',
        method: 'put',
        data
    })
}






