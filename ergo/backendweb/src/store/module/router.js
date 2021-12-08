import { asyncRouterHandle } from '@/utils/asyncRouter';

import { getInfo } from '@/api/systemUser'

const routerList = []
const formatRouter = (routes) => {
    routes && routes.map(item => {
        if ((!item.children || item.children.every(ch => ch.hidden)) && item.name != '404') {
            routerList.push({ label: item.meta.title, value: item.name })
        }
        if (item.children && item.children.length > 0) {
            formatRouter(item.children)
        }
    })
}

export const router = {
    namespaced: true,
    state: {
        asyncRouters: [],
        routerList: routerList,
    },
    mutations: {
        setRouterList(state, routerList) {
            state.routerList = routerList
        },
        // 设置动态路由
        setAsyncRouter(state, asyncRouters) {
            state.asyncRouters = asyncRouters
        },
    },
    actions: {
        // 从后台获取动态路由
        async SetAsyncRouter({ commit }) {
            const baseRouter = [{
                path: '/layout',
                name: 'layout',
                component: "view/layout/index.vue",
                meta: {
                    title: "底层layout"
                },
                children: []
            }]
            const asyncRouterRes = await getInfo()
            if (asyncRouterRes.code != 200) {
                return
            }
            const asyncRouter = asyncRouterRes.data && asyncRouterRes.data.menus
            asyncRouter.push(
                {
                    path: "404",
                    name: "404",
                    hidden: true,
                    meta: {
                        title: "迷路了*。*",
                    },
                    component: 'view/error/index.vue'
                },
                {
                    path: "profile",
                    name: "profile",
                    hidden: true,
                    meta: {
                        title: "个人信息",
                    },
                    component: 'view/superAdmin/systemUser/profile.vue'
                }
            )
            formatRouter(asyncRouter)
            baseRouter[0].children = asyncRouter
            baseRouter.push({
                path: '*',
                redirect: '/layout/404'

            })
            asyncRouterHandle(baseRouter)
            console.log('-------------baseRouter----------')
            console.log(baseRouter)
            commit('setAsyncRouter', baseRouter)
            commit('setRouterList', routerList)
            return true
        }
    },
    getters: {
        // 获取动态路由
        asyncRouters(state) {
            return state.asyncRouters
        },
        routerList(state) {
            return state.routerList
        },
        defaultRouter(state) {
            return state.defaultRouter
        }
    }
}