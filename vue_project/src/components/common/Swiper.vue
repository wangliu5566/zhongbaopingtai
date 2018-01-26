<template>
  <swiper :options="swiperOption" class="swiper-box">
    <swiper-slide class="swiper-item" v-for="(item,index) in imgList" :key="item.id">
      <img :src="baseUrl+item.mobileImage">
    </swiper-slide>
    <div class="swiper-pagination" slot="pagination"></div>
  </swiper>
</template>
<script>
import {
  swiper,
  swiperSlide
} from 'vue-awesome-swiper'

export default {
  data() {
    return {
      baseUrl: baseUrl,
      imgList: [],
      swiperOption: {},
    }
  },
  created() {
    var _this = this
    _this.swiperOption = {
      pagination: '.swiper-pagination',
      direction: 'horizontal', //滑动方向：水平 垂直：vertical
      slidesPerView: 1, //slider容器能够同时显示的slides数量
      paginationClickable: true,
      spaceBetween: 30,
      autoplay: 3000,
      loop: true,
      speed: 1000,
      onTap: function(swiper, event) {
        if (event.target.nodeName == "IMG") {
          var curPath = event.target.currentSrc.split(_this.baseUrl)[1];
          _this.getIndex(curPath)
        }
      }
    }
    this.getDatas();
  },
  methods: {
    //获取数据
    getDatas() {
      this.$http.get("/v1/frontend/index/get-pictures", {})
        .then((res) => {
          if (res.data.status == 200) {
            this.imgList = res.data.data;
            $('.swiper-wrapper').css("width", (this.imgList.length + 3) * document.body.clientWidth);
          }
        })
        .catch((err) => {
          console.log(err)
        })
    },
    getIndex(curPath) {
      var _this = this
      this.imgList.forEach(function(item, index) {
        if (item.mobileImage == curPath || item.mobileImage == curPath.replace(/\//g, '\\')) {
          var arrAll = _this.imgList
          _this.goDetail(arrAll[index].id, arrAll[index].type, 2)
        }
      })
    },
    //跳转到详情
    goDetail(id, type, status) {
      if (type == 1) {
        this.$router.push({ path: "/frontPage/detail", query: { activityId: id, type: type, status: status } })
      } else if (type == 2) {
        this.$router.push({ path: "/frontPage/question", query: { questionId: id, type: type, status: status } })
      }
    }
  }
}

</script>
<style lang='less'>
.swiper-box {
  width: 100%;
  height: 100%;
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
  /*-ms-flex-align: center;*/
  /*-webkit-align-items: center;*/
  /*align-items: center;*/
}

</style>
