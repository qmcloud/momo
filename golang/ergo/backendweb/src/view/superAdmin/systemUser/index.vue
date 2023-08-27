<template>
  <div>
    <div class="search-term">
      <el-form :inline="true" :model="searchInfo" class="demo-form-inline">
        <el-form-item label="用户名">
          <el-input
            v-model="searchInfo.keyword"
            placeholder="输入用户名"
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
              <el-button
                size="mini"
                type="primary"
                @click="deleteBatchSystemUser"
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
      <el-table-column label="头像" width="100" align="center">
        <template slot-scope="scope">
          <div v-if="scope.row.avatar">
            <img :src="'/uploads/'+scope.row.avatar" width="80" height="80"/>
          </div>
          <div v-else>-无-</div>
        </template>
      </el-table-column>
      <el-table-column
        label="用户账号"
        prop="user_name"
        width="120"
        fixed="left"
      />

      <el-table-column label="部门" prop="department_name" width="120" />
      <el-table-column label="角色" prop="role_name" width="120" />

      <el-table-column label="用户昵称" prop="nick_name" width="120" />

      <el-table-column label="用户类型" width="80" align="center">
        <template slot-scope="scope">
          {{ scope.row.user_type | formatuserType }}
        </template>
      </el-table-column>

      <el-table-column label="用户邮箱" prop="email" width="120" />

      <el-table-column label="手机号码" prop="phonenumber" width="120" />

      <el-table-column label="性别" width="60" align="center">
        <template slot-scope="scope">
          {{ scope.row.sex | formatSex }}
        </template>
      </el-table-column>


      <el-table-column label="帐号状态" width="80" align="center">
        <template slot-scope="scope">
          {{ scope.row.status | formatStatus }}
        </template>
      </el-table-column>

      <el-table-column label="删除标志" width="80" align="center">
        <template slot-scope="scope">
          {{ scope.row.del_flag | formatdelFlag }}
        </template>
      </el-table-column>

      <el-table-column label="最后登录IP" prop="login_ip" width="120" />

      <el-table-column label="备注" prop="remark" width="120" />

      <el-table-column label="最后登录时间" width="160">
        <template slot-scope="scope">
          {{ scope.row.login_date | formatDate }}
        </template>
      </el-table-column>

      <!--
        <el-table-column label="密码" prop="password" width="120" />
        <el-table-column label="创建者" prop="create_by" width="120" />
        <el-table-column label="更新者" prop="update_by" width="120" />
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
            @click="updateSystemUser(scope.row)"
            >变更</el-button
          >
          <el-button
            @click="deleteSystemUser(scope.row)"
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
        <el-form-item label="部门:" prop="dept_id">
          <el-select v-model="formData.dept_id" placeholder="请选择部门">
            <el-option label="-不指定-" :value="0"></el-option>
            <el-option
              :label="item.name"
              :value="item.id"
              v-for="(item, index) in departments"
              :key="index"
            ></el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="角色:" prop="role_id">
          <el-select v-model="formData.role_id" placeholder="请选择角色">
            <el-option label="-不指定-" :value="0"></el-option>
            <el-option
              :label="item.name"
              :value="item.id"
              v-for="(item, index) in roles"
              :key="index"
            ></el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="用户账号:" prop="user_name">
          <el-input
            v-model="formData.user_name"
            clearable
            placeholder="请输入用户账号"
          />
        </el-form-item>

        <el-form-item label="密码:" prop="password">
          <el-input
            type="password"
            v-model="formData.password"
            clearable
            placeholder="请输入密码"
          />
          <div style="color: red" v-if="type != 'create'">密码留空不修改</div>
        </el-form-item>

        <el-form-item label="手机号码:" prop="phonenumber">
          <el-input
            v-model="formData.phonenumber"
            clearable
            placeholder="请输入手机号码"
          />
        </el-form-item>

        <el-form-item label="用户昵称:" prop="nick_name">
          <el-input
            v-model="formData.nick_name"
            clearable
            placeholder="请输入用户昵称"
          />
        </el-form-item>

        <el-form-item label="用户邮箱:" prop="email">
          <el-input
            v-model="formData.email"
            clearable
            placeholder="请输入用户邮箱"
          />
        </el-form-item>

        <!--
        <el-form-item label="头像地址:" prop="avatar">
          <el-input
            v-model="formData.avatar"
            clearable
            placeholder="请输入头像地址"
          />
        </el-form-item>
        -->

        <el-form-item label="删除:" prop="del_flag">
          <el-radio-group v-model="formData.del_flag">
            <el-radio :label="0" name="del_flag">保留</el-radio>
            <el-radio :label="1" name="del_flag">删除</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item label="帐号状态:" prop="status">
          <el-radio-group v-model="formData.status">
            <el-radio :label="0" name="status">正常</el-radio>
            <el-radio :label="1" name="status">停用</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item label="性别:" prop="sex">
          <el-radio-group v-model="formData.sex">
            <el-radio :label="0" name="sex">男</el-radio>
            <el-radio :label="1" name="sex">女</el-radio>
            <el-radio :label="2" name="sex">未知</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item label="用户类型:" prop="status">
          <el-radio-group v-model="formData.user_type">
            <el-radio :label="0" name="user_type">系统用户</el-radio>
            <el-radio :label="1" name="user_type">其它</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item label="备注:" prop="remark">
          <el-input
            type="textarea"
            v-model="formData.remark"
            clearable
            placeholder="请输入备注"
          />
        </el-form-item>

        <!--
          <el-form-item label="创建者:">
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
      <el-form-item label="用户ID:">
          <el-input
            v-model="formData.id"
            clearable
            placeholder="请输入用户ID"
          />
        </el-form-item>
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
  createSystemUser,
  deleteSystemUser,
  deleteSystemUserByIds,
  updateSystemUser,
  findSystemUser,
  getSystemUserList,
} from "@/api/systemUser"; //  此处请自行替换地址
import { systemDepartmentList } from "@/api/systemDepartment";
import { systemRoleList } from "@/api/systemRole";
import { formatTimeToStr } from "@/utils/date";
import infoList from "@/mixins/infoList";
let defaultForm = {
  avatar: "",
  create_by: "",
  del_flag: 0,
  dept_id: 0,
  role_id: 0,
  email: "",
  id: 0,
  login_ip: "",
  nick_name: "",
  password: "",
  phonenumber: "",
  remark: "",
  sex: 0,
  status: 0,
  update_by: "",
  user_name: "",
  user_type: 0,
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
    formatSex: function (v) {
      if (v == 0) {
        return "男";
      } else if (v == 1) {
        return "女";
      } else {
        return "未知";
      }
    },
    formatuserType: function (v) {
      if (v == 0) {
        return "系统用户";
      }
      return "其它";
    },
    formatStatus: function (v) {
      if (v == 0) {
        return "正常";
      }
      return "停用";
    },
    formatdelFlag: function (v) {
      if (v == 0) {
        return "保留";
      }
      return "删除";
    },
  },
  mixins: [infoList],
  data() {
    const validatePassword = (rule, value, callback) => {
      if (this.type == "create") {
        if (value.length < 6) {
          callback(new Error("密码长度不少于6位"));
        } else {
          callback();
        }
      } else {
        if (value.length > 0 && value.length < 6) {
          callback(new Error("密码长度不少于6位"));
        } else {
          callback();
        }
      }
    };
    return {
      departments: [],
      roles: [],

      listApi: getSystemUserList,
      dialogFormVisible: false,
      visible: false,
      type: "",
      deleteVisible: false,
      multipleSelection: [],
      formData: defaultForm,
      rules: {
        user_name: [
          { required: true, message: "请输入用户名", trigger: "blur" },
        ],
        password: [
          { required: true, trigger: "blur", validator: validatePassword },
        ],
      },
    };
  },
  async created() {
    await this.getTableData();
  },
  methods: {
    //加载部门
    async getDepartments(page, pageSize) {
      const res = await systemDepartmentList({ page, pageSize });
      console.log(res);
      if (res.code == 200) {
        this.departments = res.data.list;
      }
    },
    //加载角色
    async getRoles(page, pageSize) {
      const res = await systemRoleList({ page, pageSize });
      console.log(res);
      if (res.code == 200) {
        this.roles = res.data.list;
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
      console.log("=========handleSelectionChange================");
      console.log(this.multipleSelection);
    },
    async deleteBatchSystemUser() {
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
      console.log("=========deleteBatchSystemUser================");
      console.log(this.multipleSelection);
      console.log(ids);
      // return;

      const res = await deleteSystemUserByIds({ ids: ids.join(",") });
      if (res.code == 200) {
        this.$message({
          type: "success",
          message: "删除成功",
        });
        if (this.tableData.length == ids.length) {
          this.page--;
        }
        this.deleteVisible = false;
        this.getTableData();
      }
    },
    async updateSystemUser(row) {
      const res = await findSystemUser({ id: row.id });
      this.type = "update";
      if (res.code == 200) {
        this.formData = res.data.item;
        this.formData.password = "";
        this.dialogFormVisible = true;
      }
      // 加载部门
      await this.getDepartments(1, 200);
      // 加载角色
      await this.getRoles(1, 200);
    },
    closeDialog() {
      this.$refs.form.resetFields();
      this.formData = defaultForm;
      this.dialogFormVisible = false;
    },
    async deleteSystemUser(row) {
      this.$confirm("此操作将永久删除所有角色下该api, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning",
      })
        .then(async () => {
          const res = await deleteSystemUser({ id: row.id });
          if (res.code == 200) {
            this.$message({
              type: "success",
              message: "删除成功!",
            });
            if (this.tableData.length == 1) {
              this.page--;
            }
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
          this.formData.role_id = parseInt(this.formData.role_id);
          switch (this.type) {
            case "create":
              this.formData.id = 0;
              res = await createSystemUser(this.formData);
              break;
            case "update":
              res = await updateSystemUser(this.formData);
              break;
            default:
              res = await createSystemUser(this.formData);
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
      // 加载部门
      await this.getDepartments(1, 200);
      // 加载角色
      await this.getRoles(1, 200);
    },
  },
};
</script>

<style>
</style>
