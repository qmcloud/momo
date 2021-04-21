<template>
  <div class="vue-particle-line">
    <div class="slot-wraper">
      <slot></slot>
    </div>
    <canvas id="canvas" class="canvas"></canvas>
  </div>
</template>

<script>
import ParticleLine from './particle-line'
// import { debounce } from 'common/js/utils'
export default {
  name: 'vue-particle-line',
  props: {
    lineWidth: {
      type: Number,
      default: 0.3
    },
    dotsNumber: {
      type: Number,
      default: 100
    },
    dotsDistance: {
      type: Number,
      default: 100
    },
    hoverEffect: {
      type: Boolean,
      default: true
    }
  },
  mounted () {
    /* eslint-disable no-new */
    new ParticleLine('canvas', {
      lineWidth: this.lineWidth,
      dotsNumber: this.dotsNumber,
      dotsDistance: this.dotsDistance,
      hoverEffect: this.hoverEffect
    })
    // particleLine.init()
    // window.onresize = debounce(() => particleLine.resize(), 500)
  }
}
</script>

<style lang="scss" scoped>
.vue-particle-line {
  position: relative;
  width: 100%;
  height: 100%;
  .slot-wraper {
    z-index: 1;
  }
  .canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
}
</style>
