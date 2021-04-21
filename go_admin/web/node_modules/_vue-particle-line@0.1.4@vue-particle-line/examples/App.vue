<template>
  <div id="app">
    <vue-particle-line class="banner-wraper">
      <banner :text="title"/>
    </vue-particle-line>
    <usage />
    <foot />
  </div>
</template>

<script>
import banner from './components/banner'
import usage from './components/usage'
import foot from './components/footer'
export default {
  name: 'app',
  components: {
    banner,
    usage,
    foot
  },
  data () {
    return {
      title: 'vue-particle-line'
    }
  }
}
</script>

<style lang="scss">
* {
  box-sizing: border-box;
}
html,
body,
#app {
  width: 100%;
  height: 100%;
  background: #282e35;
}
#app {
  // font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
  //   "Helvetica Neue", Helvetica, "PingFang SC", "Hiragino Sans GB",
  //   "Microsoft YaHei", SimSun, sans-serif;
  font-family: Avenir,Helvetica,Arial,sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  font-size: 14px;
  text-align: center;
  color: #fff;
  .banner-wraper {
    display: flex;
    justify-content: center;
    align-items: center;
    background: #283644;
  }
}

code[class*="language-"],
pre[class*="language-"],
pre,
code {
  font-family: Menlo, Monaco, Andale Mono, Ubuntu Mono, monospace !important;
  @media (max-width: 767px) {
    font-size: 0.95em;
  }
}
pre.language-js,
pre.language-html {
  max-width: 650px;
  margin: 1rem auto !important;
  background: #292929;
  border-radius: 3px;
  box-shadow: 2px 6px 17px rgba(0, 0, 0, 0.39);
}
.npm-code {
  background: #292929;
  border-radius: 3px;
  box-shadow: 2px 6px 17px rgba(0, 0, 0, 0.39);
}
h1,
h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
h3 {
  font-weight: 100;
  font-size: 2rem;
}
.white {
  color: #ffffff;
}
.text-left {
  text-align: left;
}
.text-center {
  text-align: center;
}
.footer {
  margin: 1rem 0;
}
</style>
