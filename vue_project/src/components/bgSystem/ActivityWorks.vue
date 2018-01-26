<template>
  <div class="bg-system-activityworks">
    <div class="system-title">
      <Icon type="android-options" size="24"></Icon>
      <router-link :to="backUrl">作品数据</router-link><span v-if="$route.name"> <span class="pointer">></span><span class="next-title">{{$route.name}}</span></span>
    </div>
    <Row class="pt20 pb10">
      <div class="view-main tc">
        <Radio-group v-model="defaultType" type="button" size="large" @on-change="changeType">
          <Radio label="worksanalysis" v-if="permissionList.indexOf('analysis/works')!=-1?true:false">作品分析</Radio>
          <Radio label="worksclick"  v-if="permissionList.indexOf('analysis/click')!=-1?true:false">作品点击量</Radio>
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
    setDefaultType() {
      var newUrl = this.$route.fullPath.split('/');
      this.defaultType = newUrl[4];
    },
    changeType(value) {
      this.$router.push({
        path: '/wrap/data/activityworks/' + value,
        query: {}
      })
    }
  },
  mounted() {
    this.setDefaultType();
  }
}

</script>
<style lang="less">
.bg-system-activityworks {
  .view-main {
    width: 800px;
    margin: 0 auto;
    .activity-works-header {
      width: 100%;
      border: 1px solid #dddee1;
      border-radius: 4px;
      padding: 20px;
      margin: 20px 0;
    }
    .activity-show-echart {
      width: 70%;
      height: 400px;
    }
    .activity-click-line {
      width: 100%;
      height: 500px
    }
  }
}

</style>
