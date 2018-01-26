<template>
  <div class="bg-system-view">
    <div class="system-title">
      <Icon type="android-apps" size="24"></Icon>
      <router-link :to="backUrl">流量管理</router-link><span v-if="$route.name"> <span class="pointer">></span><span class="next-title">{{$route.name}}</span></span>
    </div>
    <Row class="pt20 pb10">
      <div class="view-main tc">
        <Radio-group v-model="defaultType" type="button" size="large" @on-change="changeType">
          <Radio label="date">日数据</Radio>
          <Radio label="week">周数据</Radio>
          <Radio label="month">月数据</Radio>
        </Radio-group>
      </div>
    </Row>
    <Row class="pt10 pb10">
      <transition name="router-fade" mode="out-in">
        <router-view></router-view>
      </transition>
    </Row>
  </div>
</template>
<script>
export default {
  data() {
    return {
      backUrl: '',
      defaultType: '',

      permissionList: window.sessionStorage.getItem('bg_user_permission'),
    }
  },
  methods: {
    changeType(value) {
      this.$router.push({
        path: '/wrap/data/view/' + value,
        query: {}
      })
    },
    setDefaultType() {
      var newUrl = this.$route.fullPath.split('/');
      this.defaultType = newUrl[4];
    }
  },
  mounted() {
    this.setDefaultType();
  }

}

</script>
<style lang="less">
.bg-system-view {
  .view-main {
    width: 800px;
    margin: 0 auto;
    .view-type {
      width: 40px;
      display: inline-block;
    }
    .view-header,
    .view-container {
      border: 1px solid #dddee1;
      border-radius: 4px;
      padding: 10px 20px;
      margin: 20px 0 0 0;
    }
    .view-container {
      padding: 20px;
    }
    .ivu-col {
      text-align: center;
      padding: 5px 0;
    }
    .show-view-data {
      height: 500px;
      width: 100%;
      margin-top: 20px;
      margin-bottom: 20px;
    }
    .data-up {
      color: red;
    }
    .data-down {
      color: green;
    }
  }
}

</style>
