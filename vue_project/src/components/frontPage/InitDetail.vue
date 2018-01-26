<template>
  <div>
    <div class="pc-init-detail" v-if="terminal=='pc'">
      <!-- 顶部 -->
      <div class="init-detail-top">
        <img :src="pcImg" style="width: 100%;height: 100%;">
        <div class="opacity-bg"></div>
        <div class="top-wrap">
          <div class="position-all">
            <h2>参赛须知</h2>
            <div class="route-detail">
              <div class="route">
                <div :class="{'active':activeSpan==2}" @click="selectAward(2)">奖项设置</div>
                <div :class="{'active':activeSpan==3}" @click="selectAward(3)">时间流程</div>
                <div :class="{'active':activeSpan==4}" @click="selectAward(4)" style="margin-right: 0">参赛须知</div>
              </div>
              <div class="awards-detail" v-if="activeSpan==2">
                <awardSet :dataList="prizes"></awardSet>
              </div>
              <div class="awards-detail" v-if="activeSpan==3&&activityTime.length!=0">
                <awardTime :datas="activityTime"></awardTime>
              </div>
              <div class="awards-detail" v-if="activeSpan==4">
                <awardStandard :datas="getObj.reviewStandard"></awardStandard>
              </div>
            </div>
            <div class="will-start">
              <p>距离大赛报名开始</p>
              <div class="div-wrap" style="overflow: hidden;">
                <div class="div1">{{days1}}天</div>
                <div class="div2">:</div>
                <div class="div1">{{hours1}}</div>
                <div class="div2">:</div>
                <div class="div1">{{minute1}}</div>
                <div class="div2">:</div>
                <div class="div1">{{second1}}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- 简介底部 -->
      <div class="init-detail-bottom">
        <div class="init-simple-abstract">大赛简介</div>
        <div class="awards-detail" v-if="introImages.length!=0">
          <awardAbstract :images1="introImages" :get-obj="getObj"></awardAbstract>
        </div>
      </div>
    </div>
    <!-- ********************************************    手机端    ********************************************** -->
    <div class="phone-init-detail" v-if="terminal=='phone'">
      <span id="show-box" v-if="!showMenu" @click="showMenu=true"></span>
      <span id="close-ul" v-if="showMenu" @click="showMenu=false"></span>
      <ul id="menu" v-show="showMenu">
        <li v-for="(item,index) in menuLi" :key="item.title" :class="{'activeli':showBG==index+1}" @click="goPath(item.path,index+1)">
          <p class="chinese-title">{{item.title}}</p>
          <p class="english-title">{{item.smallTitle}}</p>
        </li>
      </ul>
      <div class="img">
        <img v-if="getObj.mobileImage" :src="baseUrl+getObj.mobileImage">
        <div class="phone-will-start">
          <p>距离大赛报名开始</p>
          <div class="div-wrap" style="overflow: hidden;">
            <div class="div1">{{days1}}天</div>
            <div class="div2">:</div>
            <div class="div1">{{hours1}}</div>
            <div class="div2">:</div>
            <div class="div1">{{minute1}}</div>
            <div class="div2">:</div>
            <div class="div1">{{second1}}</div>
          </div>
        </div>
      </div>
      <div style="height: 0.1rem;background:#e4e4e4;"></div>
      <!-- ****************    大赛简介  *************************   -->
      <div class="route-detail" id="myRoute">
        <div class="route">
          <div :class="{'active':activeSpan==1}" @click="selectAward(1)">大赛简介</div>
          <div :class="{'active':activeSpan==2}" @click="selectAward(2)">奖项设置</div>
          <div :class="{'active':activeSpan==3}" @click="selectAward(3)">时间流程</div>
          <div :class="{'active':activeSpan==4}" @click="selectAward(4)" style="margin-right: 0">参赛须知</div>
        </div>
        <div class="awards-detail" v-if="activeSpan==1&&introImages.length!=0">
          <awardAbstract :images1="introImages" :get-obj="getObj"></awardAbstract>
        </div>
        <div class="awards-detail" v-if="activeSpan==2">
          <awardSet :dataList="prizes"></awardSet>
        </div>
        <div class="awards-detail" v-if="activeSpan==3&&activityTime.length!=0">
          <awardTime :datas="activityTime"></awardTime>
        </div>
        <div class="awards-detail" v-if="activeSpan==4">
          <awardStandard :datas="getObj.reviewStandard"></awardStandard>
        </div>
      </div>
      <div id="foot1" style="width:100%;height:1px;"></div>
      <!-- 展示二维码的模态框 -->
      <Modal v-model="showQRCode1" id="share-QR-code1">
        <img :src="qrCodeurl">
      </Modal>
    </div>
  </div>
</template>
<script>
import awardAbstract from "./AwardAbstract.vue"
import awardSet from "./AwardSet.vue"
import awardTime from "./AwardTime.vue"
import awardStandard from "./AwardStandard.vue"
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      activityId: "",
      activeSpan: 2,
      getObj: {},
      introImages: [], //简介图片
      prizes: [], //奖项设置
      activityTime: [], //时间流程
      activityEndTime: 10, //报名倒计时
      days1: '0',
      hours1: '0',
      minute1: '0',
      second1: '0',
      pcImg: '/static/img/slider1.jpg',
      showMenu: false,
      menuLi: [{
        title: "首页",
        smallTitle: "HOME",
        path: "/index"
      }, {
        title: "分享活动",
        smallTitle: "SHARE ACTIVITIES",
        path: ""
      }, {
        title: "联系我们",
        smallTitle: "CONTACT US",
        path: ""
      }],
      showBG: 1,
      qrCodeurl: '',
      showQRCode1: false
    }
  },
  components: {
    awardAbstract,
    awardSet,
    awardTime,
    awardStandard
  },
  beforeDestroy() {
    clearTimeout(this.timer);
    clearInterval(this.timer2)
  },
  mounted() {
    this.isSign = sessionStorage.userId != '' && sessionStorage.userId != undefined ? true : false;
    this.terminal = IsPC() ? 'pc' : 'phone'
    if (this.$route.query.activityId) {
      this.activityId = this.$route.query.activityId
      this.getData();
    } else {
      this.$router.push("/frontPage/index")
    }
  },
  methods: {
    //获取数据
    getData(id, type) {
      this.$http.get("/v1/frontend/activity/get-info", {
          params: {
            activityId: this.$route.query.activityId,
            type: this.$route.query.type,
            userId: sessionStorage.userId ? sessionStorage.userId : '',
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.getObj = res.data.data.activity; //活动详情
            this.introImages = res.data.data.introImages; //大赛简介
            this.prizes = res.data.data.prizes; //奖项设置
            this.activityTime = res.data.data.activityTime; //时间流程
            this.pcImg = baseUrl + res.data.data.activity.pcImage;

            this.showLastTime(this.activityId);
            this.showTime()
          }
        })

    },
    selectAward(index) {
      this.activeSpan = index;
    },
    //获取报名倒计时
    showLastTime(id) {
      this.$http.get("/v1/frontend/activity/end-time", {
          params: {
            activityId: id,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.activityEndTime = res.data.data ? res.data.data : 0;
            if (this.activityEndTime <= 0) {
              clearTimeout(this.timer);
              this.$router.push("/frontPage/index")
            } else {
              var _this = this
              _this.timer = setTimeout(function() {
                _this.showLastTime(_this.activityId)
              }, 1000 * 10)
            }
          } else {
            this.activityEndTime = 0;
            clearTimeout(this.timer);
          }
        })
    },
    showTime() {
      var _this = this
      _this.timer2 = setInterval(function() {
        if (_this.activityEndTime <= 0) {
          clearInterval(_this.timer2);
        } else {
          _this.activityEndTime--;
          _this.days1 = Math.floor(_this.activityEndTime / (3600 * 24));
          var lastDay = _this.activityEndTime % (3600 * 24)
          var hours1 = Math.floor(lastDay / 3600);
          _this.hours1 = hours1 <= 9 ? '0' + hours1 : hours1;
          var minute1 = Math.floor((lastDay % 3600) / 60);
          _this.minute1 = minute1 <= 9 ? '0' + minute1 : minute1;
          var second1 = Math.floor((lastDay % 3600) % 60);
          _this.second1 = second1 <= 9 ? '0' + second1 : second1;
        }
      }, 1000)
    },
    goPath(path, index) {
      this.showBG = index;
      if (index == 1) {
        this.$router.push("/frontPage/index")
      } else if (index == 2) { //分析活动
        this.getImg()
        this.showQRCode1 = true;
      } else if (index == 3) {
        var anchor = this.$el.querySelector('#foot1')
        document.body.scrollTop = anchor.offsetTop
      }
      this.showMenu = false;
    },
    //获取二维码
    getImg() {
      this.$http.get("/v1/admin/image/qr-code", {
          params: {
            detail: window.location.href.indexOf('&access_token') != -1 ? window.location.href.split('&access_token')[0] : window.location.href
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.qrCodeurl = "data:image/png;base64," + res.data.data;
          }
        })
    },
  }
}

</script>
<style lang="less">
.phone-init-detail {
  position: relative;

  .img {
    width: 100%;
    height: 2.45rem;
    overflow: hidden;
    position: relative;
  }

  /*-------------------  手机端倒计时  ------------------------*/
  .phone-will-start {
    width: 60%;
    height: 0.9rem;
    position: absolute;
    top: 1.33rem;
    left: 20%;
    z-index: 78;
    background-color: #fff;
    border-radius: 10px;
    padding: 0 3% 0.1rem 3%;
    webkit-box-shadow: #666 0px 0px 10px;
    -moz-box-shadow: #666 0px 0px 10px;
    box-shadow: #666 0px 0px 10px;
  }

  .phone-will-start p {
    font-size: 0.14rem;
    line-height: 0.3rem;
    font-weight: 400;
    color: #666;
  }

  .div-wrap div {
    float: left;
    height: 0.5rem;
  }

  .div-wrap .div1 {
    width: 21%;
    height: 0.57rem;
    background: url('/static/img/计时.png') no-repeat;
    -webkit-background-size: 28% 0.5rem;
    background-size: 98% 0.5rem;
    font-size: 0.18rem;
    font-weight: 700;
    line-height: 0.5rem;
    text-align: center;
  }

  .div-wrap .div2 {
    width: 5%;
    line-height: 0.44rem;
    font-size: 0.3rem;
  }

  /*  -------------------  手机内容部分 ----------------------*/
  .route-detail {
    width: 94%;
    margin: 0 auto;
  }
  .route {
    border-bottom: 1px solid #999;
    margin-bottom: 0.15rem;
  }
  .route div {
    display: inline-block;
    width: 24.2%;
    text-align: center;
    font-size: 0.14rem;
    color: #666;
    font-weight: 700;
    line-height: 0.47rem;
    display: inline-block;
  }
  .route .active {
    color: #000;
    border-bottom: 2px solid #f8354c;
  }
  .phone-join {
    width: 100%;
    background-color: #f9344c;
    border: none;
    outline: none;
  }
}

#share-QR-code1 {
  .ivu-modal {
    width: 180px!important;
    margin: 0 auto;
  }
  .ivu-modal-wrap {
    top: 15%;
  }
  .ivu-modal-footer {
    display: none!important;
  }
}

.pc-init-detail {
  width: 100%;
  overflow: hidden;

  .init-detail-top {
    width: 100%;
    height: 890px;
    background-repeat: no-repeat;
    -webkit-background-size: 100% 890px;
    background-size: 100% 890px;
    position: relative;
  }
  .opacity-bg {
    position: absolute;
    top: 0;
    width: 100%;
    height: 890px;
    background-color: black;
    opacity: 0.6;
    z-index: 1;
  }
  .top-wrap {
    width: 100%;
    height: 890px;
    overflow: hidden;
    z-index: 1034;
    position: absolute;
    top: 0;
    left: 0;
  }
  .position-all {
    width: 1200px;
    height: 100%;
    margin: 0 auto;
    position: relative;
  }

  h2 {
    line-height: 150px;
    font-size: 25px;
    text-align: center;
    color: white;
  }

  .route-detail {
    width: 1200px;
    margin: 0 auto;
    overflow: hidden;
  }

  .route {
    height: 48px;
    line-height: 44px;
    overflow: hidden;
    margin-left: 150px;
    margin-bottom: 48px;
  }
  .route div {
    float: left;
    width: 280px;
    border: 2px solid white;
    font-size: 18px;
    margin-right: 26px;
    font-weight: 700;
    cursor: pointer;
    color: white;
    text-align: center;
  }

  .awards-detail {
    height: auto;
    overflow: hidden;
    margin-top: 48px;
  }

  .route .active {
    color: white;
    background: #f63051;
    border: 2px solid #f63051;
  }

  .pc-set {
    border-color: white;
    color: white;
    min-height: 110px;
    max-height: 450px;
    overflow: hidden;
    overflow-y: auto;
  }
  .pc-set .set-right {
    color: white;
  }

  .pc-set .set-item {
    border-color: white;
    color: white;
  }
  .pc-time table {
    border-color: white;
    color: white;
  }

  .pc-time td,
  .pc-time th {
    border-color: white;
  }

  .pc-standard {
    border-color: white;
    color: white;
  }
  .pc-standard h3 {
    color: white;
  }

  .pc-standard .word {
    background-color: transparent;
    color: white;
  }

  .will-start {
    width: 300px;
    height: 86px;
    padding-left: 15px;
    font-weight: 700;
    background-color: #565251;
    border-radius: 15px;
    position: absolute;
    bottom: 40px;
    left: 450px;
  }

  .will-start p {
    font-size: 14px;
    line-height: 30px;
    font-weight: 400;
    color: white;
  }

  .div-wrap div {
    float: left;
    height: 50px;
  }

  .div-wrap .div1 {
    width: 20%;
    height: 57px;
    background: url('../../../static/img/计时.png') no-repeat;
    -webkit-background-size: 28% 50px;
    background-size: 98% 50px;
    font-size: 24px;
    font-weight: 700;
    line-height: 50px;
    text-align: center;
  }

  .div-wrap .div2 {
    width: 5%;
    line-height: 44px;
    font-size: 30px;
    color: white;
    padding-left: 1%;
  }

  .init-detail-bottom {
    width: 1200px;
    height: 614px;
    margin: 0 auto;
  }

  .awards-detail {
    width: 1200px;
    margin: 0 auto;
  }
  .init-simple-abstract {
    width: 280px;
    height: 48px;
    line-height: 45px;
    border: 2px solid #313131;
    font-size: 18px;
    margin: 50px 0 40px;
    font-weight: 700;
    cursor: pointer;
    color: white;
    text-align: center;
    background-color: #313131;
  }
}

</style>
