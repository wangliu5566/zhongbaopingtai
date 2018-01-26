<template>
	<div class="index">
		<!-- 轮播 -->
		<div class="slider">
			 <slider v-if="terminal=='pc'" :sliderData="datas"></slider>
			 <swiper v-if="terminal!='pc'"></swiper>
		</div>
		<!-- 正在进行的活动 -->
		<activing></activing>

		<!-- 即将开始的活动 -->
		<initActivity></initActivity>

		<!-- 所有的活动报道 -->
		<div class="all-active" v-if="loadNewList">
			<newList></newList>
		</div>
		
	</div>
</template>

<script>
import slider from "../common/Slider.vue"
import activing from "../common/Activing.vue"
import newList from "../common/NewList.vue"
import swiper from "../common/Swiper.vue"
import initActivity from "../common/InitActivity.vue"

export default {
  data() {
    return {
      activities: [],
      news: [],
      terminal: 'pc',
      loadNewList: false,
    }
  },
  props: ['datas'],
  components: {
    slider,
    activing,
    newList,
    swiper,
    initActivity
  },
  mounted() {
    this.terminal = IsPC() ? 'pc' : 'phone'
  },
  created() {
    setTimeout(() => {
      this.loadNewList = true;
    }, 0);
  }
}

</script>
<style lang='less'>

  #showShadow {
    height: 35px;
    background: url("/static/img/out.png") no-repeat #f2f2f2;
  }

</style>
