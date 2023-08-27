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
      <el-table-column label="名称空间" prop="namespace" width="120"/>
      <el-table-column label="名字" prop="name" width="150"/>
      <el-table-column label="Type" prop="type" width="120"/>
      <el-table-column label="CLUSTER-IP" prop="cluster_ip" width="120"/>

      <el-table-column label="Port(s)" width="270">
        <template slot-scope="scope">
          <div>
            <el-tag v-for="(item, index) in scope.row.ports" :key="index"
              >{{ item.protocol }}/{{ item.port }}/{{ item.nodePort }}/{{
                item.targetPort
              }}</el-tag
            >
          </div>
        </template>
      </el-table-column>

      <el-table-column label="标签" width="270">
        <template slot-scope="scope">
          <div>
            <span
              class="labels"
              v-for="(item, index) in scope.row.labels"
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
            <el-tag v-for="(item, index) in scope.row.selector" :key="index"
              >{{ index }}:{{ item }}</el-tag
            >
          </div>
        </template>
      </el-table-column>

      <el-table-column label="创建时间" prop="age" width="160" />

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

        <el-form-item label="DeploymentName:" prop="appName">
          <el-input
            v-model="formData.appName"
            clearable
            placeholder="请输入DeploymentName"
            :disabled="type == 'update'"
          />
        </el-form-item>

        <el-form-item label="标签:" prop="labels">
          <el-input
            type="textarea"
            v-model="formData.labels"
            clearable
            placeholder="请输入标签"
          />
        </el-form-item>

        <el-form-item label="端口:" prop="ports">
          <el-input
            type="textarea"
            v-model="formData.ports"
            clearable
            placeholder="请输入端口"
          />
        </el-form-item>
        tcp:80:http
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
  createService,
  deleteService,
  updateService,
  getService,
  listService,
  listNameSpace,
} from "@/api/k8s"; //  此处请自行替换地址
import { formatTimeToStr } from "@/utils/date";
import infoList from "@/mixins/infoList";
let defaultForm = {
  name: "",
  namespace: "default",
  appName: "",
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
        appName: [{ required: true, message: "请输入镜像", trigger: "blur" }],
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
    async loadData() {
      let data = {};
      data.name = "a";
      data.name_space = this.selectedNamespace;
      const res = await listService(data);
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
      const res = await getService({
        name: row.name,
        name_space: row.namespace,
      });
      this.type = "update";
      if (res.code == 200) {

        let _labels = "";
        for (let key in res.data.spec.selector) {
          // console.log(key + "---" + obj[key]);
          _labels+=`${key}:${res.data.spec.selector[key]}\n`
        }

        let _ports = "";
        for (let key in res.data.spec.ports) {
          // console.log(key + "---" + obj[key]);
          _ports+=`${res.data.spec.ports[key]['name']}:${res.data.spec.ports[key]['port']}:${res.data.spec.ports[key]['protocol']}\n`
        }

        this.formData = {
          namespace: res.data.metadata.namespace,
          name: res.data.metadata.name,
          appName:res.data.metadata.labels['app'],
          labels: _labels,
          ports: _ports,
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
          const res = await deleteService({
            name: row.name,
            name_space: row.namespace,
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
              res = await createService(this.formData);
              break;
            case "update":
              res = await updateService(this.formData);
              break;
            default:
              res = await createService(this.formData);
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
