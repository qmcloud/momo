<template>
  <div class="bottom-info">
    <div>
      <span>Powered by</span>
      <span>
      <a href="#">admin</a>
      </span>
    </div>
  </div>
</template>
<script>
export default {
    name:"BottomInfo"
}
</script>
<style lang="scss">
.bottom-info {
  color: #888;
  height: 30px;
  line-height: 12px;
  a {
    color: #888;
  }
  div {
    display: flex;
    justify-content: start;
    padding-left: 15px;
    span{
      margin: 0 3px;
    }
  }
}
</style>
