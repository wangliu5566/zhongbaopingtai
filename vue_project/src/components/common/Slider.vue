<template>
  <div>
    <div style="font-size: 30px;text-align: center;" v-if="imgList.length==0">暂无图片</div>
    <div v-if="imgList.length!=0" class="slider1" :style="{height:BannerHeight+'px'}" @mouseenter="stopPlay()" @mouseleave="autoPlay()">
      <img v-if="imgCount!=0" :src="baseUrl+url" @click="goDetail(imgObj.id,imgObj.type,imgObj.status)">
      <div class="leftImg" @click="preImg()"><img src="../../assets/left.png" :style="{width:leftRightWidth+'px',height:leftRightWidth+'px'}"></div>
      <div class="rightImg" @click="nextImg()"><img src="../../assets/right1.png" :style="{width:leftRightWidth+'px',height:leftRightWidth+'px'}"></div>
      <button class="once-btn" :style="{fontSize:BannerFontSize+'px'}" v-if="imgObj.signStatus==0" @click="signUp">立即报名</button>
      <button class="once-btn" :style="{fontSize:BannerFontSize+'px'}" v-if="imgObj.signStatus==1" @click="goApply(imgObj.type)">{{imgObj.type==1?'立即参赛':'立即答卷'}}</button>
      <button class="once-btn" :style="{fontSize:BannerFontSize+'px'}" v-if="imgObj.signStatus==2" @click="goAward(imgObj.type)">{{imgObj.type==1?'已经参赛':'立即抽奖'}}</button>
      <button class="once-btn" :style="{fontSize:BannerFontSize+'px'}" v-if="imgObj.signStatus==3">已经抽奖</button>
      <ul class="sliderUl">
        <li v-for="(item,index) in imgList" @click='clickImgLi(index)' :class="{'activeImg':setIndex==index}"></li>
      </ul>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      baseUrl: baseUrl,
      setIndex: 0,
      imgList: [],
      imgObj: {},
      imgCount: 0,
      url: "",
      BannerFontSize: parseInt(document.body.clientWidth / 1920 * 22),
      BannerHeight: parseInt(document.body.clientWidth / 1920 * 500),
      setBannerize: 0.7,
      leftRightWidth: parseInt(document.body.clientWidth / 1920 * 60)
    }
  },
  props: ['sliderData'],
  mounted() {
    this.setInitSize();
  },
  created() {
    this.getDatas();
  },
  beforeDestroy() {
    clearInterval(this.timer)
  },
  methods: {
    setInitSize() {
      this.BannerFontSize = this.sliderData * 22
      this.BannerHeight = this.sliderData * 500
      this.leftRightWidth = this.sliderData * 60
    },
    //获取数据
    getDatas() {
      clearInterval(this.timer)
      this.$http.get("/v1/frontend/index/get-pictures", {
          params: {
            userId: sessionStorage.userId
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.imgList = res.data.data;
            this.imgCount = res.data.data.length;
            this.imgObj = this.imgList[0];
            this.url = this.imgList[0].pcImage;
            this.autoPlay()
          }
        })
    },
    autoPlay() {
      var _this = this
      _this.timer = setInterval(function() {
        _this.setIndex += 1;
        if (_this.setIndex == _this.imgCount) {
          _this.setIndex = 0;
        }
        _this.imgObj = _this.imgList[_this.setIndex];
        _this.url = _this.imgList[_this.setIndex].pcImage;
      }, 4000)
    },
    stopPlay() {
      clearInterval(this.timer)
    },
    nextImg() {
      if (this.setIndex >= this.imgCount) {
        this.setIndex = 0
      }
      this.setIndex += 1;
      if (this.setIndex == this.imgCount) {
        this.setIndex = 0;
      }

      this.url = this.imgList[this.setIndex].pcImage;
      this.imgObj = this.imgList[this.setIndex];
    },
    preImg() {
      if (this.setIndex < 0) {
        this.setIndex = this.imgCount - 1
      }
      this.setIndex -= 1;
      if (this.setIndex < 0) {
        this.setIndex = this.imgCount - 1;
      }
      this.url = this.imgList[this.setIndex].pcImage;
      this.imgObj = this.imgList[this.setIndex];
    },
    clickImgLi(thisIndex) {
      this.setIndex = thisIndex;
      this.imgObj = this.imgList[this.setIndex];
      this.url = this.imgList[this.setIndex].pcImage;
    },
    //跳转到详情
    goDetail(id, type, status) {
      if (type == 1) {
        this.$router.push({ path: "/frontPage/detail", query: { activityId: id, type: type, status: status } })
      } else if (type == 2) {
        this.$router.push({ path: "/frontPage/question", query: { questionId: id, type: type, status: status } })
      }
    },
    //立即报名
    signUp() {
      if (sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null") { //未登录
        this.$Modal.confirm({
          title: '',
          content: '<h3>您还没登录，立即去登录</h3>',
          onOk: () => {
            login(this.$route.fullPath)
          }
        });
      } else { //已登录
        this.$http.get("/v1/frontend/activity/user-sign-up", {
            params: {
              activityId: this.imgObj.id,
              userId: sessionStorage.userId
            }
          })
          .then((res) => {
            if (res.data.status == 200) {
              this.$Message.success({
                content: res.data.message,
                duration: 3,
              });
              var _this = this
              setTimeout(function() {
                _this.getDatas();
              }, 1200)
            } else {
              this.$Message.warning({
                content: res.data.message,
                duration: 3,
              });
            }
          })
      }
    },
    //立即跳转到参赛页面
    goApply(type) {
      if (type == 1) {
        this.$http.get("/v1/frontend/works/can-match", {
            params: {
              activityId: this.imgObj.id,
            }
          })
          .then((res) => {
            if (res.data.status == 200) {
              this.$router.push({ path: "/frontPage/apply", query: { activityId: this.imgObj.id, type: type, status: this.imgObj.status } })
            } else if (res.data.status == 1005) {
              this.$Message.warning({
                content: '参赛时间已过，不能参赛',
                duration: 3,
              });
            }
          })
      } else if (type == 2) {
        this.$router.push({ path: "/frontPage/question", query: { questionId: this.imgObj.id, type: type, status: this.imgObj.status } })
      }
    },
    //立即抽奖页面
    goAward(type) {
      if (type == 2) {
        this.$router.push({ path: "/frontPage/question", query: { questionId: this.imgObj.id, type: type, status: this.imgObj.status } })
      }
    }
  },
  watch: {
    sliderData: function() {
      this.setInitSize()　　　　　　　 }
  }
}

</script>
<style lang='less'>
.slider1 {
  width: 100%;
  overflow: hidden;
  position: relative;

  img {
    width: 100%;
    height: 100%;
    cursor: pointer;
  }

  .once-btn {
    position: absolute;
    left: 51%;
    top: 76%;
    background-color: #000;
    border: none;
    border-radius: 22px;
    z-index: 100;
    color: white;
    outline: none;
    padding: 7px 16px;
    cursor: pointer;
  }


  .leftImg,
  .rightImg {
    width: 60px;
    height: 60px;
    position: absolute;
    top: 45%;
  }
  .leftImg {
    left: 100px;
  }
  .rightImg {
    right: 100px;
  }
  .sliderUl {
    position: absolute;
    left: 45%;
    bottom: 20px;
  }
  .sliderUl li {
    width: 14px;
    height: 14px;
    float: left;
    margin-right: 10px;
    cursor: pointer;
    border-radius: 14px;
    opacity: 0.6;
    background-color: #fff;
  }
  .sliderUl .activeImg {
    opacity: 1;
  }
}

</style>
