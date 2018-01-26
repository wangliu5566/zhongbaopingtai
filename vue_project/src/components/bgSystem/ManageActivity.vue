<template>
  <div class="bg-system-activity">
    <div class="system-title">
      <Icon type="ios-pulse-strong" size="24"></Icon>
      <router-link :to="backUrl">活动数据</router-link><span v-if="$route.name"> <span class="pointer">></span><span class="next-title">{{$route.name}}</span></span>
    </div>
    <Row class="pt20">
      <div class="activity-main tc">
        <Radio-group v-model="defaultType" type="button" size="large" @on-change="changeType">
          <Radio label="status" v-if="permissionList.indexOf('data/active-point')!=-1?true:false">活动状态</Radio>
          <Radio label="rate" v-if="permissionList.indexOf('data/participate')!=-1?true:false">活动参与率</Radio>
          <Radio label="analysis" v-if="permissionList.indexOf('data/list')!=-1?true:false">活动分析</Radio>
          <Radio label="trend" v-if="permissionList.indexOf('data/trend')!=-1?true:false">活动趋势</Radio>
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
        path: '/wrap/data/activity/' + value,
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
.bg-system-activity {
  .activity-main {
    width: 800px;
    margin: 0 auto;
    .activity-status-header {
      width: 100%;
      border: 1px solid #dddee1;
      border-radius: 4px;
      padding: 20px;
      margin: 20px 0;
      .activity-show-echart {
        width: 70%;
        height: 400px;
      }
      .activity-show-bar {
        width: 100%;
        height: 500px;
      }
      .activity-show-line {
        width: 100%;
        height: 500px;
      }
    }
    .view-type {
      width: 40px;
      display: inline-block;
    }
    /*    .ivu-radio-group-button .ivu-radio-wrapper {
   padding: 0 25.6px;
 } */
  }
}

</style>
