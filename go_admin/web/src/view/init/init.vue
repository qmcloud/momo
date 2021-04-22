<template>
  <div class="init">
    <p class="in-one a-fadeinT">{{$t('install.title')}}</p>
    <p class="in-two a-fadeinT">{{$t('install.instll')}}</p>
    
    <div class="form-card in-three a-fadeinB">
      <el-form ref="form" :model="form" label-width="100px">
        <button @click="switchLang">EN/中文</button>
        <el-form-item label="lincese"> 
          <el-input v-model="form.lincese" placeholder=""></el-input>
        </el-form-item>
        <el-form-item label="host">
          <el-input v-model="form.host" placeholder=""></el-input>
        </el-form-item>
        <el-form-item label="port">
          <el-input v-model="form.port" placeholder=""></el-input>
        </el-form-item>
        <el-form-item label="userName">
          <el-input v-model="form.userName" placeholder=""></el-input>
        </el-form-item>
        <el-form-item label="password">
          <el-input
            v-model="form.password"
            placeholder=""
          ></el-input>
          <p>{{this.$t('install.mysqlpass')}}</p>
        </el-form-item>
        <el-form-item label="dbName">
          <el-input v-model="form.dbName" placeholder=''></el-input>
        </el-form-item>
        <el-form-item>
          <div style="text-align: right">
            <el-button type="primary" @click="onSubmit">{{$t('install.init')}}</el-button>
          </div>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import { initDB } from "@/api/initdb";
export default {
  name: "Init",
  data() {
    return {
      form: {
        sqlType: "mysql",
        host: "127.0.0.1",
        port: "3306",
        userName: "root",
        password: "",
        dbName: "Leopard",
      }
    };
  },
  methods: {
    reload:function(){
        this.isRouterAlive=false;
        this.$nextTick(function(){
            this.isRouterAlive=true
        })
    },
    switchLang () {
      let lang = ''
      this.lang=localStorage.getItem('lang')
      if (this.lang == 'en') {
        lang = 'cn'
      } else {
        lang = 'en'
      }
      localStorage.setItem('lang',lang)
      this.$i18n.locale = lang
      
    },
    async onSubmit() {
      const loading = this.$loading({
        lock: true,
        text: "正在初始化数据库，请稍候",
        spinner: "el-icon-loading",
        background: "rgba(0, 0, 0, 0.7)",
      });
      try {
        const res = await initDB(this.form);
        if (res.code == 0) {
          this.$message({
            type: "success",
            message: res.msg,
          });
          this.$router.push({name:"login"})
        }
          loading.close();
      } catch (err) {
          loading.close();
      }
    }
  },
};

</script>
<style lang="scss">
.init {
  height: 100vh;
  flex-direction: column;
  display: flex;
  align-items: center;
  background: #fff;
}
.in-one {
  font-size: 26px;
  line-height: 98px;
}
.in-two {
  font-size: 22px;
}
.form-card {
  margin-top: 60px;
  box-shadow: 0px 0px 5px 0px rgba(5, 12, 66, 0.15);
  width: 60vw;
  height: auto;
  background: #fff;
  padding: 20px;
  border-radius: 6px;
}
</style>
