<template>
  <div>
    <div class="search-term">
      <el-form :inline="true" :model="searchInfo" class="demo-form-inline">
        <el-form-item>
          <el-button
            type="primary"
            size="mini"
            icon="el-icon-plus"
            @click="openDialog"
            >新增</el-button
          >
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
      <el-table-column label="名字" prop="name" />

      <el-table-column label="状态">
        <template slot-scope="scope">
          <el-tag type="success" v-if="scope.row.status=='Active'">{{scope.row.status}}</el-tag>
          <el-tag type="info" v-else>{{scope.row.status}}</el-tag>
        </template>
      </el-table-column>

      <el-table-column label="创建时间" prop="age" />

      <el-table-column label="操作" fixed="right" width="180" align="center">
        <template slot-scope="scope">
          <!--
          <el-button
            class="table-button"
            size="mini"
            type="primary"
            icon="el-icon-edit"
            @click="edit(scope.row)"
            >变更</el-button
          >
          -->
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

        <el-form-item label="名称:" prop="name">
          <el-input
            v-model="formData.name"
            clearable
            placeholder="请输入名称"
          />
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
  createNameSpace,
  deleteNameSpace,
  listNameSpace,
} from "@/api/k8s"; //  此处请自行替换地址
import { formatTimeToStr } from "@/utils/date";
import infoList from "@/mixins/infoList";
let defaultForm = {
  name: "",
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

      tableData: [],

      dialogFormVisible: false,
      visible: false,
      type: "",
      deleteVisible: false,
      multipleSelection: [],
      formData: Object.assign({}, defaultForm),
      rules: {
        namespace: [
          { required: true, message: "请输入命名空间", trigger: "change" },
        ],
        name: [{ required: true, message: "请输入名称", trigger: "blur" }],
        image: [{ required: true, message: "请输入镜像", trigger: "blur" }],
        labels: [{ required: true, message: "请输入标签", trigger: "blur" }],
        ports: [{ required: true, message: "请输入端口", trigger: "blur" }],
      },
    };
  },
  async created() {
    await this.loadData();
  },
  methods: {
    async loadData() {
      const res = await listNameSpace();
      this.tableData = res.data;
      this.total = res.data.length;
      console.log(res);
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
      const res = await getDeployment({
        name: row.metadata.name,
        name_space: row.metadata.namespace,
      });
      this.type = "update";
      if (res.code == 200) {
        this.formData = {
          namespace: res.data.metadata.namespace,
          name: res.data.metadata.name,
          replicas: res.data.spec.replicas,
          image: res.data.spec.template.spec.containers[0].image,
          labels: "-",
          ports: "-",
        };
        this.dialogFormVisible = true;
      }
      //加载父级
      //await this.getParent(1, 200);
    },
    closeDialog() {
      this.$refs.form.resetFields();
      this.formData = Object.assign({}, defaultForm);
      this.dialogFormVisible = false;
    },
    async remove(row) {
      this.$confirm("此操作将永久删除数据, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning",
      })
        .then(async () => {
          const res = await deleteNameSpace({
            name: row.name
          });
          if (res.code == 200) {
            this.$message({
              type: "success",
              message: "删除成功!",
            });
            // 重新加载
            await this.loadData();
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
          switch (this.type) {
            case "create":
              res = await createNameSpace(this.formData);
              break;
            case "update":
              res = await updateDeployment(this.formData);
              break;
            default:
              res = await createNameSpace(this.formData);
              break;
          }
          if (res.code == 200) {
            this.$message({
              type: "success",
              // message: "创建/更改成功",
              message: res.message,
            });
            this.closeDialog();
            // this.getTableData();
            this.loadData();
          }
        }
      });
    },
    async openDialog() {
      this.type = "create";
      this.dialogFormVisible = true;
    },
  },
};
</script>

<style lang="scss">
.labels {
  background-color: #ecf5ff;
  border-color: #d9ecff;
  display: inline-block;
  height: auto;
  padding: 0 10px;
  line-height: 30px;
  font-size: 12px;
  color: #409eff;
  border-width: 1px;
  border-style: solid;
  border-radius: 4px;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  margin: 4px;
}
</style>
