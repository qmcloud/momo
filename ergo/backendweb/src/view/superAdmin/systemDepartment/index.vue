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
      <el-table-column label="部门名称" prop="name" />

      <el-table-column label="负责人" prop="leader" />
      <el-table-column label="邮箱" prop="email" />
      <el-table-column label="联系电话" prop="phone" />

      <el-table-column label="父级ID" prop="parent_id" width="70" />
      <el-table-column label="祖级列表" prop="ancestors" />
      <el-table-column label="部门状态" width="80">
        <template slot-scope="scope">
          {{ scope.row.status | formatBoolean }}
        </template>
      </el-table-column>
      <el-table-column label="排序" prop="sort" width="70" />
      <!--
  <el-table-column label="创建者" prop="create_by"/>
  <el-table-column label="更新者" prop="update_by"/>
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

      <el-table-column label="操作" fixed="right" width="180" align="center">
        <template slot-scope="scope">
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
        label-width="100px"
        ref="form"
        :rules="rules"
      >
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

        <el-form-item label="部门名称:" prop="name">
          <el-input
            v-model="formData.name"
            clearable
            placeholder="请输入部门名称"
          />
        </el-form-item>

        <el-form-item label="负责人:" prop="leader">
          <el-input
            v-model="formData.leader"
            clearable
            placeholder="请输入负责人"
          />
        </el-form-item>

        <el-form-item label="联系电话:" prop="phone">
          <el-input
            v-model="formData.phone"
            clearable
            placeholder="请输入联系电话"
          />
        </el-form-item>

        <el-form-item label="邮箱:" prop="email">
          <el-input
            v-model="formData.email"
            clearable
            placeholder="请输入邮箱"
          />
        </el-form-item>

        <el-form-item label="部门状态:" prop="status">
          <el-radio-group v-model="formData.status">
            <el-radio :label="0" name="status">正常</el-radio>
            <el-radio :label="1" name="status">停用</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item label="排序:" prop="sort">
          <el-input
            type="number"
            v-model="formData.sort"
            clearable
            placeholder="请输入排序"
          />
        </el-form-item>

        <!--

           <el-form-item label="祖级列表:" prop="ancestors">
          <el-input
            v-model="formData.ancestors"
            clearable
            placeholder="请输入祖级列表"
          />
        </el-form-item>

        <el-form-item label="创建者:" prop="create_by">
          <el-input
            v-model="formData.create_by"
            clearable
            placeholder="请输入创建者"
          />
        </el-form-item>

        <el-form-item label="更新者:" prop="update_by">
          <el-input
            v-model="formData.update_by"
            clearable
            placeholder="请输入更新者"
          />
        </el-form-item>
       
        <el-form-item label=":" prop="id">
          <el-input
            v-model="formData.id"
            clearable
            placeholder="请输入"
          /> </el-form-item
        >
        -->
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="closeDialog">取 消</el-button>
        <el-button type="primary" @click="enterDialog">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  systemDepartmentList,
  systemDepartmentParent,
  systemDepartmentDelete,
  systemDepartmentDeleteBatch,
  systemDepartmentOne,
  systemDepartmentAdd,
  systemDepartmentUpdate,
} from "@/api/systemDepartment"; //  此处请自行替换地址
import { formatTimeToStr } from "@/utils/date";
import infoList from "@/mixins/infoList";
let defaultForm = {
  ancestors: "",
  create_by: "",
  email: "",
  id: 0,
  leader: "",
  name: "",
  parent_id: 0,
  phone: "",
  sort: 0,
  status: 0,
  update_by: "",
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
    formatBoolean: function (v) {
      return v == 0 ? "正常" : "停用";
    },
  },
  mixins: [infoList],
  data() {
    return {
      parents: [],

      listApi: systemDepartmentList,
      dialogFormVisible: false,
      visible: false,
      type: "",
      deleteVisible: false,
      multipleSelection: [],
      formData: Object.assign({}, defaultForm),
      rules: {
        ancestors: [
          { required: true, message: "请输入祖级列表", trigger: "blur" },
        ],
        create_by: [
          { required: true, message: "请输入创建者", trigger: "blur" },
        ],
        email: [{ required: true, message: "请输入邮箱", trigger: "blur" }],
        id: [{ required: true, message: "请输入", trigger: "blur" }],
        leader: [{ required: true, message: "请输入负责人", trigger: "blur" }],
        name: [{ required: true, message: "请输入部门名称", trigger: "blur" }],
        parent_id: [
          { required: true, message: "请输入父级ID", trigger: "blur" },
        ],
        phone: [{ required: true, message: "请输入联系电话", trigger: "blur" }],
        sort: [{ required: true, message: "请输入排序", trigger: "blur" }],
        status: [
          {
            required: true,
            message: "请输入部门状态（0正常 1停用）",
            trigger: "blur",
          },
        ],
        update_by: [
          { required: true, message: "请输入更新者", trigger: "blur" },
        ],
      },
    };
  },
  async created() {
    await this.getTableData();
  },
  methods: {
    //获取父级
    async getParent(page, pageSize) {
      const res = await systemDepartmentParent({ page, pageSize });
      console.log(res);
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

      const res = await systemDepartmentDeleteBatch({ ids: ids.join(",") });
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
      const res = await systemDepartmentOne({ id: row.id });
      this.type = "update";
      if (res.code == 200) {
        this.formData = res.data.item;
        this.dialogFormVisible = true;
      }
      //加载父级
      await this.getParent(1, 200);
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
          const res = await systemDepartmentDelete({ id: row.id });
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
          this.formData.status = parseInt(this.formData.status);

          switch (this.type) {
            case "create":
              this.formData.id = 0;
              res = await systemDepartmentAdd(this.formData);
              break;
            case "update":
              res = await systemDepartmentUpdate(this.formData);
              break;
            default:
              res = await systemDepartmentAdd(this.formData);
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
