<template>
  <swiper :options="swiperOption" class="swiper-box">
    <swiper-slide class="swiper-item" v-for="(item,index) in imgs" :key="item.fileId">
      <img :src="baseUrl+item.introImage">
    </swiper-slide>
    <!-- <div class="swiper-pagination" slot="pagination"></div> -->
  </swiper>
</template>
<script>
import { swiper, swiperSlide } from 'vue-awesome-swiper'
export default {
  data() {
    return {
      baseUrl: baseUrl,
      swiperOption: {},
    }
  },
  props: ['imgs'],
  created() {
    var _this = this
    _this.swiperOption = {
      realIndex:1,
      pagination: '.swiper-pagination',
      direction: 'horizontal', //滑动方向：水平 垂直：vertical
      slidesPerView: 1, //slider容器能够同时显示的slides数量
      paginationClickable: true,
      spaceBetween: 30,
      autoplay: false,
      loop: true,
      speed: 1000,
      onTransitionStart(swiper){
        _this.putClickCount(_this.imgs[swiper.activeIndex-1].fileId,_this.imgs[swiper.activeIndex-1].type,)
      }
    }
  },
  mounted(){
     $('.swiper-wrapper').css("width", (this.imgs.length + 3) * document.body.clientWidth);
     var _this = this;
  },
  methods:{
    // 统计作品点击量
    putClickCount(id,type){
      this.$http.post("/v1/frontend/works/record-click",{
        fileId:id,
        type:type,
      })
      .then((res)=>{
        if(res.data.status==200){
        }
      })
    },
  }
}

</script>
<style lang='less'>
.swiper-box {
  width: 100%;
  height: 1.75rem;
  margin: 0 auto;
  position: relative!important;
}

.swiper-slide img {
  width: 100%;
  height: 1.75rem;
}

.swiper-pagination {
  width: 100%;
  height: 0.2rem;
  overflow: hidden;
  text-align: center;
  position: absolute;
  top: 1.5rem;
}

.swiper-pagination .swiper-pagination-bullet {
  display: inline-block;
  width: 0.1rem;
  height: 0.1rem;
  background-color: #fff;
  margin-left: 0.1rem;
  border-radius: 0.1rem;
  opacity: 0.6;
  cursor: pointer;
}

.swiper-pagination .swiper-pagination-bullet-active {
  opacity: 1;
}

.swiper-wrapper {
  width: 100% *11;
  height: 1.75rem;
}

.swiper-item {
  text-align: center;
  font-size: 18px;
  background: #fff;
  display: -webkit-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  -webkit-justify-content: center;
  justify-content: center;
  -webkit-box-align: center;
  float: left;
}

</style>
