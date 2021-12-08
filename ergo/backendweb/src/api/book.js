import request from '@/utils/request'
// 图书管理
export function bookList(data) {
    return request({
        url: '/book/list',
        method: 'post',
        data
    })
}

export function bookDelete(data) {
    return request({
        url: '/book/delete',
        method: 'delete',
        data
    })
}

export function bookDeleteBatch(data) {
    return request({
        url: '/book/deleteBatch',
        method: 'delete',
        data
    })
}

export function bookOne(data) {
    return request({
        url: '/book/find',
        method: 'post',
        data
    })
}



export function bookAdd(data) {
    return request({
        url: '/book/add',
        method: 'post',
        data
    })
}

export function bookUpdate(data) {
    return request({
        url: '/book/update',
        method: 'put',
        data
    })
}
export function counts(data) {
    return request({
        url: '/book/counts',
        method: 'post',
        data
    })
}






