<template>
  <div class="bg-system-view">
    <div class="system-title">
      <Icon type="person-stalker" size="24"></Icon>
      <router-link :to="backUrl">发起者列表</router-link><span v-if="$route.name"> <span class="pointer">></span><span class="next-title">{{$route.name}}</span></span>
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
      backUrl: '/wrap/data/publisher'
    }
  }
}

</script>
<style lang="less">


</style>
