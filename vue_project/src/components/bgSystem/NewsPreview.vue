<template>
  <div class="white-bg">
    <div class="slider-img" v-if="newsData.headImage">
      <img :src="baseUrl+newsData.headImage">
    </div>
    <div class="content">
      <h3>{{newsData.title}}</h3>
      <p class="date">{{newsData.publishTime}}</p>
      <div v-html="newsData.mainBody"></div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      baseUrl: baseUrl,
    }
  },
  props: ['newsData']
}

</script>
<style lang="less">
.white-bg {
  width: 1200px;
  padding: 0 56px;
  margin: 0 auto 40px;
  background-color: #fff;
  overflow: hidden;
  box-shadow: 0 0 8px #c0c0c0;

  .slider-img {
    width: 100%;
    height: 540px;
    margin-top: 40px;
    overflow: hidden;
    img {
      width: 100%;
      height: 100%;
    }
  }

  .content {
    width: 100%;
    margin-bottom: 30px;
    font-size: 18px;
    line-height: 32px;
  }

  h3 {
    font-size: 36px;
    font-weight: 700;
    line-height: 120px;
    text-align: center;
  }

  .date {
    font-size: 16px;
    color: #999;
    margin-bottom: 20px;
    height: 50px;
    line-height: 50px;
    background-color: #ececec;
    padding-left: 20px;
  }

  .title {
    margin-top: 35px;
  }
}

</style>
