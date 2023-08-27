import request from '@/utils/request'
// 读者
export function readerList(data) {
    return request({
        url: '/reader/list',
        method: 'post',
        data
    })
}

export function readerDelete(data) {
    return request({
        url: '/reader/delete',
        method: 'delete',
        data
    })
}

export function readerDeleteBatch(data) {
    return request({
        url: '/reader/deleteBatch',
        method: 'delete',
        data
    })
}

export function readerOne(data) {
    return request({
        url: '/reader/find',
        method: 'post',
        data
    })
}



export function readerAdd(data) {
    return request({
        url: '/reader/add',
        method: 'post',
        data
    })
}

export function readerUpdate(data) {
    return request({
        url: '/reader/update',
        method: 'put',
        data
    })
}






