import request from '@/utils/request'
// 验证码
export function verifyCodeList(data) {
    return request({
        url: '/verifyCode/list',
        method: 'post',
        data
    })
}

export function verifyCodeDelete(data) {
    return request({
        url: '/verifyCode/delete',
        method: 'delete',
        data
    })
}

export function verifyCodeDeleteBatch(data) {
    return request({
        url: '/verifyCode/deleteBatch',
        method: 'delete',
        data
    })
}

export function verifyCodeOne(data) {
    return request({
        url: '/verifyCode/find',
        method: 'post',
        data
    })
}



export function verifyCodeAdd(data) {
    return request({
        url: '/verifyCode/add',
        method: 'post',
        data
    })
}

export function verifyCodeUpdate(data) {
    return request({
        url: '/verifyCode/update',
        method: 'put',
        data
    })
}






