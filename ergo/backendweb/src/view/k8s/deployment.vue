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

        <el-form-item label="命名空间:" prop="namespace">
          <el-select v-model="selectedNamespace" placeholder="请选择命名空间">
            <el-option label="全部" value=""></el-option>
            <el-option
              :label="item.name"
              :value="item.name"
              v-for="(item, index) in namespaces"
              :key="index"
            ></el-option>
          </el-select>
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

      <el-table-column label="命名空间" prop="metadata.namespace" />

      <el-table-column label="名字" prop="metadata.name" />

      <el-table-column label="副本(Ready/All/Avai)">
        <template slot-scope="scope">
          <div>
            {{ scope.row.status.readyReplicas }}/{{
              scope.row.status.replicas
            }}/{{ scope.row.status.availableReplicas }}
          </div>
        </template>
      </el-table-column>

      <el-table-column label="标签" width="270">
        <template slot-scope="scope">
          <div>
            <span
              class="labels"
              v-for="(item, index) in scope.row.metadata.labels"
              :key="index"
              >{{ index }}:{{ item }}</span
            >
            <!--<el-tag v-for="(item,index) in scope.row.metadata.labels" :key="index" style="display:inline-block">{{index}}:{{item}}</el-tag>-->
          </div>
        </template>
      </el-table-column>

      <el-table-column label="选择器" width="270">
        <template slot-scope="scope">
          <div>
            <el-tag
              v-for="(item, index) in scope.row.spec.selector.matchLabels"
              :key="index"
              >{{ index }}:{{ item }}</el-tag
            >
          </div>
        </template>
      </el-table-column>

      <el-table-column label="创建时间" width="160">
        <template slot-scope="scope">
          {{ scope.row.metadata.creationTimestamp | formatDate }}
        </template>
      </el-table-column>

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
        <el-form-item label="命名空间:" prop="namespace">
          <el-select v-model="formData.namespace" placeholder="请选择命名空间">
            <el-option
              :label="item.name"
              :value="item.name"
              v-for="(item, index) in namespaces"
              :key="index"
              :disabled="type == 'update'"
            ></el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="名称:" prop="name">
          <el-input
            v-model="formData.name"
            clearable
            placeholder="请输入名称"
            :disabled="type == 'update'"
          />
        </el-form-item>

        <el-form-item label="副数:" prop="replicas">
          <el-input
            v-model="formData.replicas"
            clearable
            placeholder="请输入副数"
          />
        </el-form-item>

        <el-form-item label="镜像:" prop="image">
          <el-input
            v-model="formData.image"
            clearable
            placeholder="请输入镜像"
          />
        </el-form-item>

        <el-form-item label="标签:" prop="labels">
          <el-input
            type="textarea"
            v-model="formData.labels"
            clearable
            placeholder="请输入标签"
            :disabled="type == 'update'"
          />
        </el-form-item>

        <el-form-item label="端口:" prop="ports">
          <el-input
            type="textarea"
            v-model="formData.ports"
            clearable
            placeholder="请输入端口"
            :disabled="type == 'update'"
          />
        </el-form-item>

        <!--
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
        -->

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
  listDeployment,
  deleteDeployment,
  createDeployment,
  getDeployment,
  updateDeployment,
  listNameSpace,
} from "@/api/k8s"; //  此处请自行替换地址
import { formatTimeToStr } from "@/utils/date";
import infoList from "@/mixins/infoList";
let defaultForm = {
  namespace: "default",
  name: "",
  replicas: 1,
  image: "",
  labels: "",
  ports: "",
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
      selectedNamespace: "",

      namespaces: [],

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
    await this.listNameSpace();
  },
  methods: {
    async loadData() {
      let data = {};
      data.name = "a";
      data.name_space = this.selectedNamespace;
      const res = await listDeployment(data);
      this.tableData = res.data;
      this.total = res.data.length;
      console.log(res);
    },
    //加载名称空间
    async listNameSpace() {
      const res = await listNameSpace({});
      console.log(res);
      if (res.code == 200) {
        this.namespaces = res.data;
      }
    },
    // 条件搜索前端看此方法
    search() {
      this.loadData();
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
          const res = await deleteDeployment({
            name: row.metadata.name,
            name_space: row.metadata.namespace,
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
          this.formData.replicas = parseInt(this.formData.replicas);
          switch (this.type) {
            case "create":
              res = await createDeployment(this.formData);
              break;
            case "update":
              res = await updateDeployment(this.formData);
              break;
            default:
              res = await createDeployment(this.formData);
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
      //加载名称空间
      await this.listNameSpace();
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
