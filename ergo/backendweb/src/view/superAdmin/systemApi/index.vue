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

      <el-table-column label="分组" prop="api_group" />
      <el-table-column label="名称" prop="description" />
      <el-table-column label="方法" prop="method" />
      <el-table-column label="路径" prop="path" />
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
            size="mini"
            type="danger"
            icon="el-icon-delete"
            @click="remove(scope.row)"
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
        ref="form"
        :model="formData"
        label-position="right"
        label-width="100px"
        :rules="rules"
      >
        <el-form-item label="api名称:" prop="description">
          <el-input
            v-model="formData.description"
            clearable
            placeholder="请输入api名称"
          />
        </el-form-item>

        <el-form-item label="api分组:" prop="api_group">
          <el-input
            v-model="formData.api_group"
            clearable
            placeholder="请输入api分组"
          />
        </el-form-item>

        <el-form-item label="api路径:" prop="path">
          <el-input
            v-model="formData.path"
            clearable
            placeholder="请输入api路径"
          />
        </el-form-item>

        <el-form-item label="请求方法:" prop="method">
          <el-radio-group v-model="formData.method">
            <el-radio :label="item.name" name="method" v-for="(item,index) in methods" :key="index"></el-radio>
          </el-radio-group>
        </el-form-item>
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
  systemApiList,
  systemApiDelete,
  systemApiDeleteBatch,
  systemApiOne,
  systemApiAdd,
  systemApiUpdate,
} from "@/api/systemApi"; //  此处请自行替换地址
import { formatTimeToStr } from "@/utils/date";
import infoList from "@/mixins/infoList";
const defaultForm = {
  api_group: "",
  description: "",
  id: 0,
  method: "POST",
  path: "",
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
      methods:[
        {
          name:"POST"
        },
        {
          name:"PUT"
        },
        {
          name:"DELETE"
        },
        {
          name:"GET"
        }
      ],
      listApi: systemApiList,
      dialogFormVisible: false,
      visible: false,
      type: "",
      deleteVisible: false,
      multipleSelection: [],
      formData: Object.assign({}, defaultForm),
      rules: {
        api_group: [
          { required: true, message: "请输入api组", trigger: "blur" },
        ],
        description: [
          { required: true, message: "请输入api中文描述", trigger: "blur" },
        ],
        method: [
          { required: true, message: "请输入请求方法", trigger: "blur" },
        ],
        path: [{ required: true, message: "请输入api路径", trigger: "blur" }],
      },
    };
  },
  async created() {
    await this.getTableData();
  },
  methods: {
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

      const res = await systemApiDeleteBatch({ ids: ids.join(",") });
      if (res.code == 200) {
        this.$message({
          type: "success",
          message: "删除成功",
        });
        // if (this.tableData.length == ids.length) {
        //  this.page--;
        // }
        this.deleteVisible = false;
        this.getTableData();
      }
    },
    async edit(row) {
      const res = await systemApiOne({ id: row.id });
      this.type = "update";
      if (res.code == 200) {
        this.formData = res.data.item;
        this.dialogFormVisible = true;
      }
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
          const res = await systemApiDelete({ id: row.id });
          if (res.code == 200) {
            this.$message({
              type: "success",
              message: "删除成功!",
            });
            // if (this.tableData.length == 1) {
            //  this.page--;
            // }
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
          this.formData.dept_id = parseInt(this.formData.dept_id);
          switch (this.type) {
            case "create":
              this.formData.id = 0;
              res = await systemApiAdd(this.formData);
              break;
            case "update":
              res = await systemApiUpdate(this.formData);
              break;
            default:
              res = await systemApiAdd(this.formData);
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
    openDialog() {
      this.type = "create";
      this.dialogFormVisible = true;
    },
  },
};
</script>

<style>
</style>
