<template>
  <div>
    <el-row>
      <el-col :span="6">
        <div class="fl-left avatar-box">
          <div class="user-card">
            <!--<div class="user-headpic-update" :style="{ 'background-image': `url(${(userInfo.headerImg && userInfo.headerImg.slice(0, 4) !== 'http')?path+userInfo.headerImg:userInfo.headerImg})`,'background-repeat':'no-repeat','background-size':'cover' }" >-->
            <div
              class="user-headpic-update"
              :style="{
                'background-image': `url(${userInfo.headerImg})`,
                'background-repeat': 'no-repeat',
                'background-size': 'cover',
              }"
            >
              <span class="update" @click="openChangeAvatarDialog()">
                <i class="el-icon-edit"></i>
                重新上传</span
              >
            </div>
            <div class="user-personality">
              <p class="nickname">{{ userInfo.nickName }}</p>
              <p class="person-info">这个家伙很懒，什么都没有留下</p>
            </div>
            <div class="user-information">
              <ul>
                <li><i class="el-icon-user"></i>{{ userInfo.nickName }}</li>
                <li>
                  <i class="el-icon-data-analysis"></i
                  >上海网络科技有限公司-技术部
                </li>
                <li>
                  <i class="el-icon-video-camera-solid"></i>中国·上海市·徐汇区
                </li>
                <li>
                  <i class="el-icon-medal-1"></i>goLang/JavaScript/Vue/Gorm
                </li>
              </ul>
            </div>
          </div>
        </div>
      </el-col>
      <el-col :span="18">
        <div class="user-addcount">
          <el-tabs v-model="activeName" @tab-click="handleClick">
            <el-tab-pane label="账号绑定" name="second">
              <ul>
                <li>
                  <p class="title">密保手机</p>
                  <p class="desc">
                    已绑定手机:1245678910
                    <a href="#">立即修改</a>
                  </p>
                </li>
                <li>
                  <p class="title">密保邮箱</p>
                  <p class="desc">
                    已绑定邮箱：abcd@com.cn
                    <a href="#">立即修改</a>
                  </p>
                </li>
                <li>
                  <p class="title">密保问题</p>
                  <p class="desc">
                    未设置密保问题
                    <a href="#">去设置</a>
                  </p>
                </li>
                <li>
                  <p class="title">修改密码</p>
                  <p class="desc">
                    修改个人密码
                    <a href="#" @click="showPassword = true">修改密码</a>
                  </p>
                </li>
              </ul>
            </el-tab-pane>
          </el-tabs>
        </div>
      </el-col>
    </el-row>


    <el-dialog
      :visible.sync="showPassword"
      @close="clearPassword"
      title="修改密码"
      width="360px"
    >
      <el-form
        :model="pwdModify"
        :rules="rules"
        label-width="80px"
        ref="modifyPwdForm"
      >
        <el-form-item :minlength="6" label="原密码" prop="password">
          <el-input show-password v-model="pwdModify.password"></el-input>
        </el-form-item>
        <el-form-item :minlength="6" label="新密码" prop="newPassword">
          <el-input show-password v-model="pwdModify.newPassword"></el-input>
        </el-form-item>
        <el-form-item :minlength="6" label="确认密码" prop="confirmPassword">
          <el-input
            show-password
            v-model="pwdModify.confirmPassword"
          ></el-input>
        </el-form-item>
      </el-form>
      <div class="dialog-footer" slot="footer">
        <el-button @click="showPassword = false">取 消</el-button>
        <el-button @click="savePassword" type="primary">确 定</el-button>
      </div>
    </el-dialog>

    <!--<el-drawer title="修改头像" :visible.sync="showUpdateAvatar">-->

    <el-dialog
      :visible.sync="showUpdateAvatar"
      @close="clearPassword"
      title="修改头像"
      width="360px"
    >
      <el-form
        :model="pwdModify"
        :rules="rules"
        label-width="80px"
        ref="modifyPwdForm"
      >
        <el-form-item :minlength="6" label="">
          <el-upload
            ref="upload_avatarUrl"
            :action="`${path}/admin/changeAvatar`"
            accept=".jpg,.jpeg,.png,.gif,.bmp,.pdf,.JPG,.JPEG,.PBG,.GIF,.BMP,.mp4,.MP4,.apk,.APk"
            list-type="picture-card"
            :class="{ imgHide: imgHideUpload }"
            :headers="headers"
            :limit="1"
            :auto-upload="false"
            :file-list="fileList_avatarUrl"
            :on-exceed="handleExceed_avatarUrl"
            :before-upload="handleBeforeUpload_avatarUrl"
            :on-preview="handlePictureCardPreview_avatarUrl"
            :on-success="handleSuccess_avatarUrl"
            :on-remove="handleRemove_avatarUrl"
            :on-change="handleChange_avatarUrl"
          >
            <i class="el-icon-plus"></i>
          </el-upload>
          <el-dialog :visible.sync="dialogVisible_avatarUrl" append-to-body>
            <img width="100%" :src="dialogImageUrl_avatarUrl" alt="" />
          </el-dialog>
        </el-form-item>
      </el-form>
      <div class="dialog-footer" slot="footer">
        <el-button @click="showUpdateAvatar = false">取 消</el-button>
        <el-button @click="confirmChangeAvatar" type="primary"
          >确定上传</el-button
        >
      </div>
    </el-dialog>
    <!--</el-drawer>-->
  </div>
</template>
<script>
import { updatePassword } from "@/api/systemUser";
import { mapGetters, mapMutations } from "vuex";
import { store } from "@/store/index";
const path = process.env.VUE_APP_BASE_API;
export default {
  name: "Person",
  data() {
    return {
      dialogVisible_avatarUrl: false,
      fileList_avatarUrl: [],
      dialogImageUrl_avatarUrl: "",
      selectedfile_avatarUrl: false,
      headers: {
        authorization: "",
      },
      imgHideUpload: false, 

      path: path,
      activeName: "second",
      showPassword: false,
      showUpdateAvatar: false,
      pwdModify: {},
      rules: {
        password: [
          { required: true, message: "请输入密码", trigger: "blur" },
          { min: 6, message: "最少6个字符", trigger: "blur" },
        ],
        newPassword: [
          { required: true, message: "请输入新密码", trigger: "blur" },
          { min: 6, message: "最少6个字符", trigger: "blur" },
        ],
        confirmPassword: [
          { required: true, message: "请输入确认密码", trigger: "blur" },
          { min: 6, message: "最少6个字符", trigger: "blur" },
          {
            validator: (rule, value, callback) => {
              if (value !== this.pwdModify.newPassword) {
                callback(new Error("两次密码不一致"));
              } else {
                callback();
              }
            },
            trigger: "blur",
          },
        ],
      },
    };
  },
  created() {
    this.fileList_avatarUrl.push({ url: this.userInfo.headerImg });
    this.headers.authorization = this.token;
    if (
      this.userInfo &&
      this.userInfo.headerImg &&
      this.userInfo.headerImg.length > 0
    ) {
      this.imgHideUpload = true;
    }
  },
  computed: {
    ...mapGetters("user", ["userInfo", "token"]),
  },
  methods: {
    ...mapMutations("user", ["ResetUserInfo"]),
    openChangeAvatarDialog() {
      this.fileList_avatarUrl = [];
      this.fileList_avatarUrl.push({ url: this.userInfo.headerImg });
      this.showUpdateAvatar = true;
    },
    savePassword() {
      this.$refs.modifyPwdForm.validate((valid) => {
        if (valid) {
          updatePassword({
            // username: this.userInfo.userName,
            oldPassword: this.pwdModify.password,
            password: this.pwdModify.newPassword,
          }).then((res) => {
            if (res.code == 200) {
              this.$message.success("修改密码成功！");
            }
            this.showPassword = false;
          });
        } else {
          return false;
        }
      });
    },

    async confirmChangeAvatar() {
      // var postData = new FormData();
      // const authorization = store.getters["user/token"];
      // var headers = {
      //   "Content-Type": "multipart/form-data",
      //   authorization,
      // };
      // let url = `${process.env.VUE_APP_BASE_API}/admin/changeAvatar`;
      // let res = await axios.post(url, postData, { headers: headers });
      // console.log(res);
      if (!this.selectedfile_avatarUrl) {
        this.$message.warning("请选择一个文件");
        return;
      }
      this.$refs.upload_avatarUrl.submit();
    },

    clearPassword() {
      this.pwdModify = {
        password: "",
        newPassword: "",
        confirmPassword: "",
      };
      this.$refs.modifyPwdForm.clearValidate();
    },
    openChooseImg() {
      this.$refs.chooseImg.open();
    },
    async enterImg(url) {
      const res = await setUserInfo({ headerImg: url, ID: this.userInfo.ID });
      if (res.code == 0) {
        this.ResetUserInfo({ headerImg: url });
        this.$message({
          type: "success",
          message: "设置成功",
        });
      }
    },
    handleClick(tab, event) {
      console.log(tab, event);
    },

    // android上传 begin
    handleBeforeUpload_avatarUrl(file) {
      console.log("before");
      if (
        !(
          file.type === "image/png" ||
          file.type === "image/gif" ||
          file.type === "image/jpg" ||
          file.type === "image/jpeg"
        )
      ) {
        this.$notify.warning({
          title: "警告",
          message:
            "请上传格式为image/png, image/gif, image/jpg, image/jpeg的图片",
        });
      }
      let size = file.size / 1024 / 1024 / 2;
      if (size > 2) {
        this.$notify.warning({
          title: "警告",
          message: "图片大小必须小于2M",
        });
      }
    },
    // 文件超出个数限制时的钩子
    handleExceed_avatarUrl(files, fileList) {
      console.log(files, fileList);
      if (this.selectedfile_avatarUrl || fileList.length >= 1) {
        this.$message.warning("上传的图片不能大于1张");
        return;
      }
    },
    // 点击文件列表中已上传的文件时的钩子
    handlePictureCardPreview_avatarUrl(file) {
      this.dialogImageUrl_avatarUrl = file.url;
      this.dialogVisible_avatarUrl = true;
    },
    async handleSuccess_avatarUrl(response, file, fileList) {
      if (response.code == 200) {
        console.log(response.data);
        this.showUpdateAvatar = false;
        // 更新store中头像
        await store.dispatch("user/changeAvatarStore", response.data);
      }
    },
    handleChange_avatarUrl(file, fileList) {
      this.imgHideUpload = fileList.length >= 1;
      console.log(file);
      //this.$refs[this.Up].submit();
      this.selectedfile_avatarUrl = true;
    },
    // 文件列表移除文件时的钩子
    handleRemove_avatarUrl(file, fileList) {
      this.imgHideUpload = fileList.length >= 1;
      this.fileList_avatarUrl = [];
      this.selectedfile_avatarUrl = false;
    },
    // 点击上传
    uploadFile_avatarUrl() {
      if (!this.selectedfile_avatarUrl) {
        this.$message.warning("请选择一个文件");
        return;
      }
      this.$refs.upload_avatarUrl.submit();
    },
    // android上传 end
  },
};
</script>
<style lang="scss">
.imgHide .el-upload--picture-card {
  display: none;
}
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}
.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  line-height: 178px;
  text-align: center;
}
.avatar {
  width: 178px;
  height: 178px;
  display: block;
}
.avatar-box {
  box-shadow: -2px 0 20px -16px;
  width: 80%;
  height: 100%;
  .user-card {
    min-height: calc(90vh - 200px);
    padding: 30px 20px;
    text-align: center;
    .el-avatar {
      border-radius: 50%;
    }
    .user-personality {
      padding: 24px 0;
      text-align: center;
      p {
        font-size: 16px;
      }
      .nickname {
        font-size: 26px;
      }
      .person-info {
        margin-top: 6px;
        font-size: 14px;
        color: #999;
      }
    }
    .user-information {
      width: 100%;
      height: 100%;
      text-align: left;
      ul {
        display: inline-block;
        height: 100%;
        li {
          i {
            margin-right: 8px;
          }
          padding: 20px 0;
          font-size: 16px;
          font-weight: 400;
          color: #606266;
        }
      }
    }
  }
}
.user-addcount {
  ul {
    li {
      .title {
        padding: 10px;
        font-size: 18px;
        color: #696969;
      }
      .desc {
        font-size: 16px;
        padding: 0 10px 20px 10px;
        color: #a9a9a9;
        a {
          color: rgb(64, 158, 255);
          float: right;
        }
      }
      border-bottom: 2px solid #f0f2f5;
    }
  }
}
.user-headpic-update {
  width: 120px;
  height: 120px;
  line-height: 120px;
  margin: 0 auto;
  display: flex;
  justify-content: center;
  border-radius: 20px;
  &:hover {
    color: #fff;
    background: linear-gradient(
        to bottom,
        rgba(255, 255, 255, 0.15) 0%,
        rgba(0, 0, 0, 0.15) 100%
      ),
      radial-gradient(
          at top center,
          rgba(255, 255, 255, 0.4) 0%,
          rgba(0, 0, 0, 0.4) 120%
        )
        #989898;
    background-blend-mode: multiply, multiply;
    .update {
      color: #fff;
    }
  }
  .update {
    height: 120px;
    width: 120px;
    text-align: center;
    color: transparent;
  }
}
</style>