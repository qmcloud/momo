import request from '@/utils/request'


//deployment
export function listDeployment(data) {
    return request({
        url: '/k8s/listDeployment',
        method: 'post',
        data
    })
}

export function createDeployment(data) {
    return request({
        url: '/k8s/createDeployment',
        method: 'post',
        data
    })
}
export function deleteDeployment(data) {
    return request({
        url: '/k8s/deleteDeployment',
        method: 'delete',
        data
    })
}

export function updateDeployment(data) {
    return request({
        url: '/k8s/updateDeployment',
        method: 'put',
        data
    })
}
export function getDeployment(data) {
    return request({
        url: '/k8s/getDeployment',
        method: 'post',
        data
    })
}


// pods

// namespace
export function createNameSpace(data) {
    return request({
        url: '/k8s/createNameSpace',
        method: 'post',
        data
    })
}
export function deleteNameSpace(data) {
    return request({
        url: '/k8s/deleteNameSpace',
        method: 'delete',
        data
    })
}
export function listNameSpace(data) {
    return request({
        url: '/k8s/listNameSpace',
        method: 'get',
        data
    })
}


// service
export function createService(data) {
    return request({
        url: '/k8s/createService',
        method: 'post',
        data
    })
}
export function deleteService(data) {
    return request({
        url: '/k8s/deleteService',
        method: 'delete',
        data
    })
}
export function updateService(data) {
    return request({
        url: '/k8s/updateService',
        method: 'put',
        data
    })
}
export function getService(data) {
    return request({
        url: '/k8s/getService',
        method: 'post',
        data
    })
}
export function listService(data) {
    return request({
        url: '/k8s/listService',
        method: 'post',
        data
    })
}

// ingress
export function createIngress(data) {
    return request({
        url: '/k8s/createIngress',
        method: 'post',
        data
    })
}
export function deleteIngress(data) {
    return request({
        url: '/k8s/deleteIngress',
        method: 'delete',
        data
    })
}
export function updateIngress(data) {
    return request({
        url: '/k8s/updateIngress',
        method: 'put',
        data
    })
}
export function getIngress(data) {
    return request({
        url: '/k8s/getIngress',
        method: 'post',
        data
    })
}
export function listIngress(data) {
    return request({
        url: '/k8s/listIngress',
        method: 'post',
        data
    })
}


// pods
export function listPods(data) {
    return request({
        url: '/k8s/listPods',
        method: 'post',
        data
    })
}