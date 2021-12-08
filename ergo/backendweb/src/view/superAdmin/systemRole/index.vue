<template>
  <div>
    <div class="search-term">
      <el-form :inline="true" :model="searchInfo" class="demo-form-inline">
        <el-form-item label="搜索关键词">
          <el-input
            v-model="searchInfo.keyword"
            placeholder="输入搜索关键词"
            size="mini"
          />
        </el-form-item>
        <el-form-item>
          <el-button
            type="primary"
            size="mini"
            icon="el-icon-search"
            @click="search"
            >查询</el-button
          >
        </el-form-item>
        <el-form-item>
          <el-button
            type="primary"
            size="mini"
            icon="el-icon-plus"
            @click="openDialog"
            >新增</el-button
          >
        </el-form-item>
        <el-form-item>
          <el-popover v-model="deleteVisible" placement="top" width="160">
            <p>确定要删除吗？</p>
            <div style="text-align: right; margin: 0">
              <el-button size="mini" type="text" @click="deleteVisible = false"
                >取消</el-button
              >
              <el-button size="mini" type="primary" @click="deleteBatch"
                >确定</el-button
              >
            </div>
            <el-button
              slot="reference"
              icon="el-icon-delete"
              size="mini"
              type="danger"
              >批量删除</el-button
            >
          </el-popover>
        </el-form-item>
      </el-form>
    </div>
    <el-table
      ref="multipleTable"
      :data="tableData"
      border
      stripe
      style="width: 100%"
      tooltip-effect="dark"
      @selection-change="handleSelectionChange"
    >
      <el-table-column type="selection" width="55" fixed="left" />
      <el-table-column label="ID" prop="id" width="70" />

      <el-table-column label="角色名" prop="name" />
      <el-table-column label="父级ID" prop="parent_id" width="70" />
      <el-table-column label="排序" prop="sort" width="70" />

      <!--
      <el-table-column label="创建时间" width="160">
        <template slot-scope="scope">
          {{ scope.row.created_at | formatDate }}
        </template>
      </el-table-column>

      <el-table-column label="更新时间" width="160">
        <template slot-scope="scope">
          {{ scope.row.updated_at | formatDate }}
        </template>
      </el-table-column>
      -->

      <el-table-column label="操作" fixed="right" width="290" align="center">
        <template slot-scope="scope">
          <el-button
            class="table-button"
            size="mini"
            type="primary"
            icon="el-icon-setting"
            @click="assignPermissions(scope.row)"
            >分配权限</el-button
          >
          <el-button
            class="table-button"
            size="mini"
            type="primary"
            icon="el-icon-edit"
            @click="edit(scope.row)"
            >变更</el-button
          >
          <el-button
            @click="remove(scope.row)"
            size="mini"
            type="danger"
            icon="el-icon-delete"
            >删除</el-button
          >
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      :current-page="page"
      :page-size="pageSize"
      :page-sizes="[10, 30, 50, 100]"
      :style="{ float: 'right', padding: '20px' }"
      :total="total"
      layout="total, sizes, prev, pager, next, jumper"
      @current-change="handleCurrentChange"
      @size-change="handleSizeChange"
    />

    <el-dialog
      :before-close="closeDialog"
      :visible.sync="dialogFormVisible"
      :title="type == 'create' ? '新增记录' : '编辑记录'"
    >
      <el-form
        :model="formData"
        label-position="right"
        label-width="80px"
        ref="form"
        :rules="rules"
      >
        <!--
        <el-form-item label=":" prop="id">
          <el-input v-model="formData.id" clearable placeholder="请输入" />
        </el-form-item>
        -->
        <el-form-item label="父级:" prop="parent_id">
          <el-select v-model="formData.parent_id" placeholder="请选择父级">
            <el-option label="顶级" :value="0"></el-option>
            <el-option
              :label="item.name"
              :value="item.id"
              v-for="(item, index) in parents"
              :key="index"
            ></el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="角色名:" prop="name">
          <el-input
            v-model="formData.name"
            clearable
            placeholder="请输入角色名"
          />
        </el-form-item>

        <el-form-item label="排序:" prop="sort">
          <el-input
            type="number"
            v-model="formData.sort"
            clearable
            placeholder="请输入排序"
          />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="closeDialog">取 消</el-button>
        <el-button type="primary" @click="enterDialog">确 定</el-button>
      </div>
    </el-dialog>

    <el-drawer :visible.sync="drawer" :with-header="false" size="50%" title="角色配置" v-if="drawer">
      <el-tabs :before-leave="autoEnter" class="role-box" type="border-card">
        <el-tab-pane label="角色菜单">
          <Menus :row="activeRow" ref="menus" @hideDrawerOnSuccess="hideDrawer"/>
        </el-tab-pane>
        <el-tab-pane label="角色Api">
           <apis :row="activeRow" ref="apis" @hideDrawerOnSuccess="hideDrawer"/>
        </el-tab-pane>
      </el-tabs>
    </el-drawer>
  </div>
</template>

<script>
import {
  systemRoleList,
  systemRoleParent,
  systemRoleDelete,
  systemRoleDeleteBatch,
  systemRoleOne,
  systemRoleAdd,
  systemRoleUpdate,
} from "@/api/systemRole"; //  此处请自行替换地址
import { formatTimeToStr } from "@/utils/date";
import infoList from "@/mixins/infoList";
import Menus from "@/view/superAdmin/systemRole/components/menus";
import Apis from "@/view/superAdmin/systemRole/components/apis";
let defaultForm = {
  id: 0,
  name: "",
  parent_id: 0,
  sort: 0,
};
export default {
  name: "SystemUser",
  filters: {
    formatDate: function (time) {
      if (time != null && time != "") {
        var date = new Date(time);
        return formatTimeToStr(date, "yyyy-MM-dd hh:mm:ss");
      } else {
        return "";
      }
    },
    formatBoolean: function (bool) {
      if (bool != null) {
        return bool ? "是" : "否";
      } else {
        return "";
      }
    },
  },
  mixins: [infoList],
  data() {
    return {
      parents: [],

      drawer: false,
      activeRow: {},

      listApi: systemRoleList,
      dialogFormVisible: false,
      visible: false,
      type: "",
      deleteVisible: false,
      multipleSelection: [],
      formData: Object.assign({}, defaultForm),
      rules: {
        id: [{ required: true, message: "请输入", trigger: "blur" }],
        name: [{ required: true, message: "请输入角色名", trigger: "blur" }],
        parent_id: [
          { required: true, message: "请输入父级ID", trigger: "blur" },
        ],
        sort: [{ required: true, message: "请输入排序", trigger: "blur" }],
      },
    };
  },
  components: {
    Menus,
    Apis
  },
  async created() {
    await this.getTableData();
  },
  methods: {
    autoEnter(activeName, oldActiveName) {
      const paneArr = ["menus", "apis"];
      if (oldActiveName) {
        if (this.$refs[paneArr[oldActiveName]].needConfirm) {
          this.$refs[paneArr[oldActiveName]].enterAndNext();
          this.$refs[paneArr[oldActiveName]].needConfirm = false;
        }
      }
    },
    //获取父级
    async getParent(page, pageSize) {
      const res = await systemRoleParent({ page, pageSize });
      // console.log(res);
      if (res.code == 200) {
        this.parents = res.data.list;
      }
    },
    // 条件搜索前端看此方法
    search() {
      this.page = 1;
      this.pageSize = 10;
      this.getTableData();
    },
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    async deleteBatch() {
      const ids = [];
      if (this.multipleSelection.length == 0) {
        this.$message({
          type: "warning",
          message: "请选择要删除的数据",
        });
        return;
      }
      this.multipleSelection &&
        this.multipleSelection.map((item) => {
          ids.push(item.id);
        });

      const res = await systemRoleDeleteBatch({ ids: ids.join(",") });
      if (res.code == 200) {
        this.$message({
          type: "success",
          message: "删除成功",
        });
        //if (this.tableData.length == ids.length) {
        //  this.page--;
        //}
        this.deleteVisible = false;
        this.getTableData();
      }
    },
    async edit(row) {
      const res = await systemRoleOne({ id: row.id });
      this.type = "update";
      if (res.code == 200) {
        this.formData = res.data.item;
        this.dialogFormVisible = true;
      }
      //加载父级
      await this.getParent(1, 200);
    },
    // https://blog.csdn.net/sllailcp/article/details/78595077
    hideDrawer(data){
      console.log(data)
      this.drawer = false;
    },
    assignPermissions(row){
      // console.log(row)
      this.drawer = true;
      this.activeRow = row;
    },
    closeDialog() {
      this.$refs.form.resetFields();
      this.formData = Object.assign({}, defaultForm);
      this.dialogFormVisible = false;
    },
    async remove(row) {
      this.$confirm("此操作将永久删除所有角色下该api, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning",
      })
        .then(async () => {
          const res = await systemRoleDelete({ id: row.id });
          if (res.code == 200) {
            this.$message({
              type: "success",
              message: "删除成功!",
            });
            //if (this.tableData.length == 1) {
            //  this.page--;
            //}
            this.getTableData();
          }
        })
        .catch(() => {
          console.log("已取消删除");
          /*
          this.$message({
            type: "info",
            message: "已取消删除"
          });
          */
        });
    },
    async enterDialog() {
      this.$refs.form.validate(async (valid) => {
        if (valid) {
          let res;
          this.formData.parent_id = parseInt(this.formData.parent_id);
          this.formData.sort = parseInt(this.formData.sort);
          switch (this.type) {
            case "create":
              this.formData.id = 0;
              res = await systemRoleAdd(this.formData);
              break;
            case "update":
              res = await systemRoleUpdate(this.formData);
              break;
            default:
              res = await systemRoleAdd(this.formData);
              break;
          }
          if (res.code == 200) {
            this.$message({
              type: "success",
              // message: "创建/更改成功",
              message: res.message,
            });
            this.closeDialog();
            this.getTableData();
          }
        }
      });
    },
    async openDialog() {
      this.type = "create";
      this.dialogFormVisible = true;
      //加载父级
      await this.getParent(1, 200);
    },
  },
};
</script>

<style>
</style>
