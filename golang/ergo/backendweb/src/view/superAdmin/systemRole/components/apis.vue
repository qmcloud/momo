<template>
  <div>
    <div class="clearflex">
      <el-button
        class="fl-right"
        size="small"
        type="primary"
        @click="authApiEnter"
      >确 定</el-button>
    </div>
    <el-tree
      ref="apiTree"
      :data="apiTreeData"
      :default-checked-keys="apiTreeIds"
      :props="apiDefaultProps"
      default-expand-all
      highlight-current
      node-key="onlyId"
      show-checkbox
      @check="nodeChange"
    />
  </div>
</template>
<script>
import { systemApiList } from '@/api/systemApi'
import { systemRoleApiAdd, systemRoleApisByRoleId } from '@/api/systemRoleApi'
export default {
  name: 'Apis',
  props: {
    row: {
      default: function() {
        return {}
      },
      type: Object
    }
  },
  data() {
    return {
      role_id: 0,
      apiTreeData: [],
      apiTreeIds: [],
      needConfirm: false,
      apiDefaultProps: {
        children: 'children',
        label: 'description'
      }
    }
  },
  async created() {
    // 获取api并整理成树结构
    const res2 = await systemApiList({ page: 1, pageSize: 200 })
    const apis = res2.data.list

    this.apiTreeData = this.buildApiTree(apis)
    // console.log('----------->')
    // console.log(this.apiTreeData)
    const res = await systemRoleApisByRoleId({
      id: this.row.id
    })
    // console.log('----------->systemRoleApisByRoleId')
    // console.log(res)
    this.role_id = this.row.id
    this.apiTreeIds = []
    res.data.list &&
      res.data.list.map((item) => {
        this.apiTreeIds.push('p:' + item.v1 + 'm:' + item.v2)
      })
  },
  methods: {
    nodeChange() {
      this.needConfirm = true
    },
    // 暴露给外层使用的切换拦截统一方法
    enterAndNext() {
      this.authApiEnter()
    },
    // 创建api树方法
    buildApiTree(apis) {
      const apiObj = new Object()
      apis &&
        apis.map((item) => {
          item.onlyId = 'p:' + item.path + 'm:' + item.method
          if (Object.prototype.hasOwnProperty.call(apiObj, item.api_group)) {
            apiObj[item.api_group].push(item)
          } else {
            Object.assign(apiObj, { [item.api_group]: [item] })
          }
        })
      const apiTree = []
      for (const key in apiObj) {
        const treeNode = {
          ID: key,
          description: key + '组',
          children: apiObj[key]
        }
        apiTree.push(treeNode)
      }
      return apiTree
    },
    // 关联关系确定
    async authApiEnter() {
      const checkArr = this.$refs.apiTree.getCheckedNodes(true)
      var casbinInfos = []
      checkArr &&
        checkArr.map((item) => {
          var casbinInfo = {
            // id: 0,
            p_type: 'p',
            v0: this.role_id + '',
            v1: item.path,
            v2: item.method,
            v3: '',
            v4: '',
            v5: ''
          }
          casbinInfos.push(casbinInfo)
        })
      const res = await systemRoleApiAdd({
        role_apis: casbinInfos,
        role_id: this.role_id
      })
      if (res.code == 200) {
        this.$message({
          type: 'success',
          message: 'api设置成功',
          duration: 800
        })
        // this.$emit('hideDrawerOnSuccess',{role_id:this.role_id})
      }
    }
  }
}
</script>
<style lang="scss">
</style>
