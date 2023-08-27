import request from '@/utils/request'
// 图书订单
export function bookOrderList(data) {
    return request({
        url: '/bookOrder/list',
        method: 'post',
        data
    })
}

export function bookOrderDelete(data) {
    return request({
        url: '/bookOrder/delete',
        method: 'delete',
        data
    })
}

export function bookOrderDeleteBatch(data) {
    return request({
        url: '/bookOrder/deleteBatch',
        method: 'delete',
        data
    })
}

export function bookOrderOne(data) {
    return request({
        url: '/bookOrder/find',
        method: 'post',
        data
    })
}



export function bookOrderAdd(data) {
    return request({
        url: '/bookOrder/add',
        method: 'post',
        data
    })
}

export function bookOrderUpdate(data) {
    return request({
        url: '/bookOrder/update',
        method: 'put',
        data
    })
}






