import service from '@/utils/request'

// @Tags SystemUser
// @Summary 创建SystemUser
// @Security ApiKeyAuth
// @accept application/json
// @Produce application/json
// @Param data body model.SystemUser true "创建SystemUser"
// @Success 200 {string} string "{"success":true,"data":{},"msg":"获取成功"}"
// @Router /systemUser/createSystemUser [post]
export const createSystemUser = (data) => {
  return service({
    url: '/admin/adminAdd',
    method: 'post',
    data
  })
}

// @Tags SystemUser
// @Summary 删除SystemUser
// @Security ApiKeyAuth
// @accept application/json
// @Produce application/json
// @Param data body model.SystemUser true "删除SystemUser"
// @Success 200 {string} string "{"success":true,"data":{},"msg":"删除成功"}"
// @Router /systemUser/deleteSystemUser [delete]
export const deleteSystemUser = (data) => {
  return service({
    url: '/admin/delete',
    method: 'delete',
    data
  })
}

// @Tags SystemUser
// @Summary 删除SystemUser
// @Security ApiKeyAuth
// @accept application/json
// @Produce application/json
// @Param data body request.IdsReq true "批量删除SystemUser"
// @Success 200 {string} string "{"success":true,"data":{},"msg":"删除成功"}"
// @Router /systemUser/deleteSystemUser [delete]
export const deleteSystemUserByIds = (data) => {
  return service({
    url: '/admin/deleteBatch',
    method: 'delete',
    data
  })
}

// @Tags SystemUser
// @Summary 更新SystemUser
// @Security ApiKeyAuth
// @accept application/json
// @Produce application/json
// @Param data body model.SystemUser true "更新SystemUser"
// @Success 200 {string} string "{"success":true,"data":{},"msg":"更新成功"}"
// @Router /systemUser/updateSystemUser [put]
export const updateSystemUser = (data) => {
  return service({
    url: '/admin/adminUpdate',
    method: 'put',
    data
  })
}

// @Tags SystemUser
// @Summary 用id查询SystemUser
// @Security ApiKeyAuth
// @accept application/json
// @Produce application/json
// @Param data body model.SystemUser true "用id查询SystemUser"
// @Success 200 {string} string "{"success":true,"data":{},"msg":"查询成功"}"
// @Router /systemUser/findSystemUser [get]
export const findSystemUser = (data) => {
  return service({
    url: '/admin/find',
    method: 'post',
    data
  })
}

// @Tags SystemUser
// @Summary 分页获取SystemUser列表
// @Security ApiKeyAuth
// @accept application/json
// @Produce application/json
// @Param data body request.PageInfo true "分页获取SystemUser列表"
// @Success 200 {string} string "{"success":true,"data":{},"msg":"获取成功"}"
// @Router /systemUser/getSystemUserList [get]
export const getSystemUserList = (data) => {
  return service({
    url: '/admin/list',
    method: 'post',
    data
  })
}
export function updatePassword(data) {
  return service({
      url: '/admin/updateadminpassword',
      method: 'put',
      data
  })
}
// @Summary 用户登录
// @Produce  application/json
// @Param data body {username:"string",password:"string"}
// @Router /base/login [post]
export const login = (data) => {
  return service({
      url: "/admin/adminLogin",
      method: 'post',
      data: data
  })
}
export function getInfo() {
  return service({
      url: '/admin/info',
      method: 'get',
      params: { t: Math.random() }
  })
}
export function logout() {
  return service({
      url: '/admin/logout',
      method: 'post'
  })
}