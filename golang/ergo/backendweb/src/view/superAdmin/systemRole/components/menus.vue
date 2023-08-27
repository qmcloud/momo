<template>
  <div>
    <div class="clearflex">
      <el-button @click="relation" class="fl-right" size="small" type="primary"
        >确 定</el-button
      >
    </div>
    <el-tree
      :data="menuTreeData"
      :default-checked-keys="menuTreeIds"
      :props="menuDefaultProps"
      @check="nodeChange"
      default-expand-all
      highlight-current
      node-key="id"
      ref="menuTree"
      show-checkbox
    ></el-tree>
  </div>
</template>
<script>
import { treeList } from "@/api/systemMenu";
import { systemRoleMenuAdd,systemRoleMenusByRoleId} from "@/api/systemRoleMenu";

export default {
  name: "Menus",
  props: {
    row: {
      default: function () {
        return {};
      },
      type: Object,
    },
  },
  data() {
    return {
      menuTreeData: [],
      menuTreeIds: [],
      needConfirm: false,
      menuDefaultProps: {
        children: "children",
        label: function (data) {
          return data.title;
        },
      },
    };
  },
  methods: {
    nodeChange() {
      this.needConfirm = true;
    },
    // 暴露给外层使用的切换拦截统一方法
    enterAndNext() {
      this.relation();
    },
    // 关联树 确认方法
    async relation() {
      const checkArr = this.$refs.menuTree.getCheckedNodes(false, true);
      // console.log(checkArr);
      let menu_ids = "";
      for (let i = 0; i < checkArr.length; i++) {
        if (menu_ids == "") {
          menu_ids = `${checkArr[i].id}`;
        } else {
          menu_ids += `,${checkArr[i].id}`;
        }
      }
      const res = await systemRoleMenuAdd({
        menu_ids,
        role_id: this.row.id,
      });
      if (res.code == 200) {
        this.$message({
          type: "success",
          message: "菜单设置成功!",
          duration: 800,
        });
        // this.$emit('hideDrawerOnSuccess',{role_id:this.row.id})
      }
    },
  },
  async created() {
    // 获取所有菜单树
    const res = await treeList({ ids: "" });
    this.menuTreeData = res.data.list;

    const res1 = await systemRoleMenusByRoleId({ role_id: this.row.id,menu_ids:'' })
    const roleMenus = res1.data.list
    const arr = []
    roleMenus.map(item => {
      // 防止直接选中父级造成全选
      if (!roleMenus.some(same => same.parent_id === item.id)) {
        // console.log(item.id)
        arr.push(Number(item.id))
      }
    })
    this.menuTreeIds = arr
  },
};
</script>
<style lang="scss">
</style>