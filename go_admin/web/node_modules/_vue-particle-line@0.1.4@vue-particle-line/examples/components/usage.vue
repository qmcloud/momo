<template>
  <div class="main-section">
    <h3 class="section-title white">How to use</h3>
    <pre class="npm-code white">
              <code>
npm install vue-particle-line --save
              </code>
     </pre>

    <br>

    <div class="wrap-code text-left">
      <h4 class="white">Main.js file</h4>
      <pre class="language-js"><code>
import Vue from 'vue'
import vueParticleLine from 'vue-particle-line'
import 'vue-particle-line/dist/vue-particle-line.css'
Vue.use(vueParticleLine)
        </code></pre>
    </div>
    <br>
    <div class="wrap-code text-left">
      <h4 class="white">App.vue file - Simple example</h4>
      <pre class="language-html"><code>
{{appCode}}
        </code></pre>
    </div>
    <br>
    <div class="wrap-table">
      <h4 class="white text-left">Props</h4>
      <pre class="language-html">编写中</pre>
    </div>
  </div>
</template>

<script>
import Prism from 'prismjs'
const appCode = `<template>
  <div id="app">
    <vue-particle-line>
      <router-view />
    </vue-particle-line>
  </div>
</template>`
export default {
  name: 'usage',
  data () {
    return {
      appCode
    }
  },
  mounted () {
    Prism.highlightAll()
  }
}
</script>

<style lang="scss" scoped>
.main-section {
  max-width: 650px;
  margin: auto;
  padding: 0 1rem;
}
.section-title {
  margin: 2rem 0 0 0;
}
.main-title {
  color: #fff;
  z-index: 999;
  h2 {
    font-size: 7rem;
    font-family: "Teko", sans-serif;
    text-shadow: 8px 8px #000000;
    text-transform: uppercase;
    margin: 0;
    @media (max-width: 767px) {
      font-size: 3rem;
    }
  }
  // p.subtitle {
  //   font-size: 1.7rem;
  //   margin: 0;
  //   @media (max-width: 767px) {
  //     font-size: 1rem;
  //   }
  // }
}
</style>
