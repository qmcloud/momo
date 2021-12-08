<template>
  <div class="big">
    <el-row>
      <div class="card">
        <el-col :xs="24" :lg="16" :md="16">
          <div class="car-left">
            <el-row>
              <div>
                <el-col :xs="4" :md="3" :lg="3">
                  <span class="card-img">
                    <img :src="userInfo.headerImg" alt="" />
                  </span>
                </el-col>
                <el-col :xs="20" :lg="12" :md="12">
                  <div class="text">
                    <h4>
                      早安，管理员:{{ userInfo.nickName }}，
                      请开始您一天的工作吧！
                    </h4>
                    <p class="tips-text">
                      <i class="el-icon-sunny el-icon"></i>
                      <span>今日晴，0℃ - 10℃，天气寒冷，注意添加衣物。</span>
                    </p>
                  </div>
                </el-col>
              </div>
            </el-row>
          </div>
        </el-col>
        <el-col :xs="24" :lg="8" :md="8">
          <div class="car-right">
            <el-row>
              <el-col :span="8"
                ><div class="car-item">
                  <span class="flow"><i class="el-icon-s-grid"></i></span>
                  <span>馆藏图书 </span>
                  <b>{{counts.totalBook}} 册</b>
                </div></el-col
              >
              <el-col :span="8"
                ><div class="car-item">
                  <span class="user-number">
                    <i class="el-icon-s-custom"></i>
                  </span>
                  <span>累计借阅 </span>
                  <b>{{counts.totalBorrow}} 次</b>
                </div></el-col
              >
              <el-col :span="8"
                ><div class="car-item">
                  <span class="feedback">
                    <i class="el-icon-star-on"></i>
                  </span>
                  <span>待归还 </span>
                  <b>{{counts.totalShouldReturn}} 册</b>
                </div></el-col
              >
            </el-row>
          </div>
        </el-col>
      </div>
    </el-row>
    <div class="shadow">
      <el-row :gutter="20">
        <el-col
          :span="4"
          v-for="(card, key) in toolCards"
          :key="key"
          @click.native="toTarget(card.name)"
          :xs="8"
        >
          <el-card shadow="hover" class="grid-content">
            <i :class="card.icon" :style="{ color: card.color }"></i>
            <p>{{ card.label }}</p>
          </el-card>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
import TodoList from "./component/todoList";
import { mapGetters } from "vuex";
import { counts } from "@/api/book"; //  此处请自行替换地址
export default {
  name: "Dashboard",
  data() {
    return {
      counts: {},
      toolCards: [
        {
          label: "图书管理",
          icon: "el-icon el-icon-setting",
          name: "books",
          color: "#69c0ff",
        },
        {
          label: "借阅记录",
          icon: "el-icon el-icon-document-checked",
          name: "bookOrder",
          color: "#ff85c0",
        },
        {
          label: "用户管理",
          icon: "el-icon el-icon-monitor",
          name: "readers",
          color: "#ff9c6e",
        },
        {
          label: "菜单管理",
          icon: "el-icon el-icon-menu",
          name: "systemMenuList",
          color: "#b37feb",
        },
        {
          label: "API管理",
          icon: "el-icon el-icon-cpu",
          name: "systemApiList",
          color: "#ffd666",
        },
        {
          label: "角色管理",
          icon: "el-icon el-icon-user",
          name: "systemRoleList",
          color: "#5cdbd3",
        },
      ],
    };
  },
  computed: {
    ...mapGetters("user", ["userInfo"]),
  },
  components: {
    // musicPlayer, //音乐播放器
    TodoList, //TodoList
    // RaddarChart, //雷达图
    // stackMap, //堆叠图
    // Sunburst, //旭日图
  },
  created(){
    this.loadCounts()
  },
  methods: {
    toTarget(name) {
      this.$router.push({ name });
    },
    async loadCounts(row) {
      const res = await counts({ id: 0 });
      console.log(res)
      if (res.code == 200) {
        this.counts=res.data.counts
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.big {
  margin: 100px 0 0 0;
  padding-top: 0;
  background-color: rgb(243, 243, 243);
  padding-top: 15px;
  .top {
    width: 100%;
    height: 360px;
    margin-top: 20px;
    overflow: hidden;
    .chart-container {
      position: relative;
      width: 100%;
      height: 100%;
      padding: 20px;
      background-color: #fff;
    }
  }
  .mid {
    width: 100%;
    height: 380px;
    .chart-wrapper {
      height: 340px;
      background: #fff;
      padding: 16px 16px 0;
      margin-bottom: 32px;
    }
  }
  .bottom {
    width: 100%;
    height: 300px;
    // margin: 20px 0;
    .el-row {
      margin-right: 4px !important;
    }
    .chart-player {
      width: 100%;
      height: 270px;
      padding: 10px;
      background-color: #fff;
    }
  }
}
</style>
