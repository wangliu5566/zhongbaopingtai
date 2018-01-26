<template>
  <div class="bg-system-role">
    <div class="system-title">
      <Icon type="ios-paper" size="24"></Icon>
      <router-link :to="backUrl">角色管理</router-link><span v-if="$route.name"> <span class="pointer">></span><span class="next-title">{{$route.name}}</span></span>
    </div>
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
    }
  },
  methods: {
    createBackUrl() {
      let backUrl = this.$route.path.split('/');
      this.backUrl = '/' + backUrl[1] + '/' + backUrl[2] + '/' + backUrl[3];
    }
  },
  mounted() {
    this.createBackUrl();
  }
}

</script>
<style lang="less">


</style>
