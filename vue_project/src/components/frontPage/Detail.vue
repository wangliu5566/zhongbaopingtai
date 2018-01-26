<template>
  <div class="pc-detail" v-if="terminal=='pc'">
    <div class="top">
      <img v-if="getObj.pcImage" :src="baseUrl+getObj.pcImage" :style="{height:BannerHeight+'px'}">
      <div class="time1">
        <Row style="height: 100%;min-width: 500px;">
          <Col span="12">
          <div class="end-num">
            <p>距离大赛报名截止</p>
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
          </Col>
          <Col span="12">
          <div class="end-time" @click="login" v-if="status==1">
            立即登录<img src="../../../static/img/箭头.png" alt="">
          </div>
          <div class="end-time" @click="signUp" v-if="status==2">
            立即报名<img src="../../../static/img/箭头.png" alt="">
          </div>
          <div class="end-time" @click="isCanApply" v-if="status==3">
            立即参赛<img src="../../../static/img/箭头.png" alt="">
          </div>
          <div class="end-time" v-if="status==4">
            已经参赛
          </div>
          <div class="end-time" v-if="status==5">
            报名结束
          </div>
          </Col>
        </Row>
      </div>
    </div>
    <!-- 大赛简介 -->
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
      <div class="once-join">
        <Button type="info" @click="goApply">立即参赛</Button>
      </div>
    </div>
    <!-- 参赛作品 -->
    <div class="join-active" v-if="activityId!=''">
      <games></games>
    </div>
    <!-- 所有活动报道 -->
    <div class="actives">
      <newList></newList>
    </div>
    <!-- 更多活动 -->
    <div class="more-active" id='moreActive'>
      <moreActive></moreActive>
    </div>
  </div>
  <!-- ********************************************    手机端    ************************************************************ -->
  <div class="phone-detail" v-else>
    <div class="top">
      <span class="show-box" v-if="!showMenu" @click="showBox()"></span>
      <span class="close-ul" v-if="showMenu" @click="showBox()"></span>
      <ul class="menu" v-show="showMenu">
        <li v-for="(item,index) in menuLi" :key="item.title" :class="{'activeli':showBG==index+1}" @click="goPath(item.path,index+1)">
          <p class="chinese-title">{{item.title}}</p>
          <p class="english-title">{{item.smallTitle}}</p>
        </li>
      </ul>
      <div class="mobile-img">
        <img v-if="getObj.pcImage" :src="baseUrl+getObj.pcImage">
      </div>
    </div>
    <!-- 大赛简介 -->
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
      <Button type="info" class="phone-join" @click="phoneJoin" v-if="status==1||status==2">立即报名</Button>
      <Button type="info" class="phone-join" @click="phoneJoin" v-if="status==3">已经报名，请到PC上传作品</Button>
      <Button type="info" class="phone-join" @click="phoneJoin" v-if="status==4">已经参赛</Button>
      <Button type="info" class="phone-join" @click="phoneJoin" v-if="status==5">报名结束</Button>
    </div>
    <!-- 更多活动 -->
    <div class="more-active" id='moreActive'>
      <moreActive></moreActive>
    </div>
    <div id="foot1" style="width:100%;height:1px;"></div>
    <!-- 展示二维码的模态框 -->
    <Modal v-model="showQRCode" id="share-QR-code">
      <img :src="qrCodeurl">
    </Modal>
  </div>
</template>
<script>
import newList from "../common/NewList.vue"
import games from "../common/Games.vue"
import moreActive from "../common/MoreActive.vue"
import awardAbstract from "./AwardAbstract.vue"
import awardSet from "./AwardSet.vue"
import awardTime from "./AwardTime.vue"
import awardStandard from "./AwardStandard.vue"
export default {
  data() {
    return {
      activityId: "",
      type: "",
      baseUrl: baseUrl,
      terminal: 'pc',
      showMenu: false,
      showBG: 1,
      menuLi: [{
        title: "首页",
        smallTitle: "HOME",
        path: "/index"
      }, {
        title: "参赛作品",
        smallTitle: "ENTRIES",
        path: ""
      }, {
        title: "大赛简介",
        smallTitle: "MEGAGAME ABSTRACT",
        path: ""
      }, {
        title: "分享活动",
        smallTitle: "SHARE ACTIVITIES",
        path: ""
      }, {
        title: "更多活动",
        smallTitle: "MORE ACTIVITIES",
        path: ""
      }, {
        title: "联系我们",
        smallTitle: "CONTACT US",
        path: ""
      }],
      activeSpan: 1,
      getObj: {},
      introImages: [],
      prizes: [],
      activityTime: [],
      awardStandard: [],
      status: 1,
      isSign: true,
      getActivityStatus: 2, //0是所有，1,是未开始，2是正在进行，3是已结束
      activityEndTime: 10,
      getStatusFromRes: 0,
      days1: '0',
      hours1: '0',
      minute1: '0',
      second1: '0',
      BannerHeight: 300,
      showQRCode: false,
      qrCodeurl: '',
    }
  },
  props: ['datas'],
  mounted() {
    this.isSign = sessionStorage.userId != '' && !!sessionStorage.userId && sessionStorage.userId != "null"? true : false;
    this.terminal = IsPC() ? 'pc' : 'phone'

    if (location.href.indexOf('workId') > 0 && this.terminal != 'pc') { //处理分享跳转到作品详情
      console.log(location.href)
      this.$router.push({ path: "/frontPage/production", query: { productionId: location.href.split('workId=')[1] } });
    } else if (this.$route.query.activityId) {
      this.activityId = this.$route.query.activityId
      this.type = this.$route.query.type
      this.getActivityStatus = this.$route.query.status;
      this.getData(this.activityId, this.type);
    } else {
      this.$router.push("/frontPage/index");
    }

    this.BannerHeight = this.datas * 500;
  },
  components: {
    newList,
    games,
    moreActive,
    awardAbstract,
    awardSet,
    awardTime,
    awardStandard
  },
  beforeDestroy() {
    clearTimeout(this.timer);
    clearInterval(this.timer2)
  },
  methods: {
    showBox() {
      this.showMenu = !this.showMenu;
    },
    goPath(path, index) {
      this.showBG = index;
      if (index == 1) {
        this.$router.push("/frontPage/index")
      } else if (index == 2) {
        this.$router.push({ path: "/frontPage/gameList", query: { activityId: this.activityId, type: this.type, status: this.$route.query.status } })
      } else if (index == 3) {
        var anchor = this.$el.querySelector('#myRoute')
        document.body.scrollTop = anchor.offsetTop
      } else if (index == 4) {
        this.getImg()
        this.showQRCode = true;
      } else if (index == 5) {
        var anchor = this.$el.querySelector('#moreActive')
        document.body.scrollTop = anchor.offsetTop
      } else if (index == 6) {
        var anchor = this.$el.querySelector('#foot1')
        document.body.scrollTop = anchor.offsetTop
      }
      this.showMenu = false;
    },
    selectAward(index) {
      this.activeSpan = index;
    },
    //获取数据
    getData(id, type) {
      clearTimeout(this.timer);
      clearInterval(this.timer2)
      this.$http.get("/v1/frontend/activity/get-info", {
          params: {
            activityId: id,
            type: type,
            userId: sessionStorage.userId,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.getObj = res.data.data.activity; //活动详情
            this.introImages = res.data.data.introImages; //大赛简介
            this.prizes = res.data.data.prizes; //奖项设置
            this.activityTime = res.data.data.activityTime; //时间流程
            this.getStatusFromRes = res.data.data.signStatus;

            if (res.data.data.activity.status == 3) { //活动已经结束
              this.activityEndTime = 0;
              this.days1 = '0';
              this.hours1 = '0';
              this.minute1 = '0';
              this.second1 = '0';
            } else { //请求倒计时
              if (this.terminal == 'pc') { //手机没有
                this.showLastTime(this.activityId);
                this.showTime()
              }
            }
            this.getStatus();
          }
        })

    },
    getStatus() {
      if (this.activityEndTime <= 0) {
        this.status = 5; //活动结束
      } else {
        if (!this.isSign) {
          this.status = 1; //未登录
        } else if (this.isSign && this.getStatusFromRes == 0) { //
          this.status = 2; //已登录未报名
        } else if (this.isSign && this.getStatusFromRes == 1) { //
          this.status = 3; //已报名未参赛
        } else if (this.isSign && this.getStatusFromRes == 2) { //
          this.status = 4; //已登录已参赛
        }

        console.log(this.status)
      }
    },
    login() {
      login(this.$route.fullPath)
    },
    //立即报名
    signUp() {
      this.$http.get("/v1/frontend/activity/user-sign-up", {
          params: {
            activityId: this.activityId,
            userId: sessionStorage.userId
          }
        })
        .then((res) => {
          if (res.data.status == '200') {
            if (this.terminal == 'pc') {
              this.$Message.success({
                content: res.data.message,
                duration: 3,
              });
            } else {
              this.$Message.success({
                content: '您已报名成功 快去PC端提交作品吧！',
                duration: 3,
              });
            }
            var _this = this
            setTimeout(function() {
              _this.getData(_this.activityId, _this.type);
            }, 2000)
          } else {
            this.$Message.warning({
              content: res.data.message,
              duration: 3,
            });
          }
        })
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
    //手机端报名
    phoneJoin() {
      if (sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null") { //未登录
        this.$Modal.confirm({
          title: '提示',
          content: '<h3>您还没登录，立即去登录</h3>',
          onOk: () => {
            login(this.$route.fullPath)
          }
        });
      } else {
        this.signUp()
      }
    },
    goApply() {
      if (this.$route.query.status == 3) {
        this.$Message.warning({
          content: '活动已结束',
          duration: 3,
        })
      } else {
        if (sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null") { //未登录
          this.$Modal.confirm({
            title: '提示',
            content: '<h3>您还没登录，立即去登录</h3>',
            onOk: () => {
              login(this.$route.fullPath)
            }
          });
        } else { //已登录
          if (this.getStatusFromRes == 0) { //未报名
            this.$Modal.confirm({
              title: '提示',
              content: '<h3>您还没报名，立即报名？</h3>',
              onOk: () => {
                this.signUp()
              }
            });
          } else if (this.getStatusFromRes == 1) { //已报名未参赛
            this.isCanApply()
          } else if (this.getStatusFromRes == 2) { //已参赛
            this.$Modal.warning({
              title: '提示',
              content: '<h3>您已经参赛！</h3>'
            });
          }
        }

      }
    },
    isCanApply() {
      this.$http.get("/v1/frontend/works/can-match", {
          params: {
            activityId: this.activityId,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.$router.push({ path: "/frontPage/apply", query: { activityId: this.activityId, type: this.type, status: this.getActivityStatus } })
          } else if (res.data.status == 1005) {
            this.$Message.warning({
              content: '参赛时间已过，不能参赛',
              duration: 3,
            });
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
        .catch((err) => {})
    },
  },
  watch: {
    datas: function() {//注意：当观察的数据为对象或数组时，curVal和oldVal是相等的，因为这两个形参指向的是同一个数据对象
      this.BannerHeight = this.datas * 500;　　　　　　　
    }
  }
}

</script>
<style lang="less">
.phone-detail {
  width: 100%;

  .mobile-img {
    height: 1.75rem;
  }
  .mobile-img img {
    width: 100%;
    height: 100%;
  }

  .show-box,
  .close-ul {
    display: block;
    width: 10%;
    height: 0.3rem;
    background: url("/static/img/menu.png") no-repeat 0 5px;
    -webkit-background-size: 25px;
    background-size: 25px;
    position: absolute;
    right: 10px;
    top: 15px;
    font-size: 25px;
    z-index: 100009
  }
  .close-ul {
    background: url("/static/img/叉.png") no-repeat 0 5px;
    -webkit-background-size: 17px;
    background-size: 17px;
  }
  .menu {
    z-index: 109;
    position: absolute;
    right: 0;
    top: 0.6rem;
    width: 60%;
    background-color: #000;
    li {
      padding-top: 13px;
      height: 70px;
      padding-left: 20%;
    }
    .chinese-title {
      font-size: 17px;
      color: white;
      font-weight: 700;
    }
    .english-title {
      font-size: 13px;
      color: #939393;
      font-weight: 400;
    }

    .activeli {
      background-color: #bf233b;
    }
  }
  .route-detail {
    width: 94%;
    margin: 0 auto;
  }
  .route {
    border-bottom: 1px solid #999;
    margin-bottom: 15px;
  }
  .route div {
    display: inline-block;
    width: 24%;
    text-align: center;
    font-size: 0.14rem;
    color: #666;
    font-weight: 700;
    line-height: 47px;
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

#share-QR-code {
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

.pc-detail {
  width: 100%;

  .top {
    position: relative;
    margin-bottom: 100px;
    min-height: 200px;
    img {
      width: 100%;
    }
  }

  .time1 {
    width: 46%;
    height: 86px;
    position: absolute;
    top: 90%;
    left: 27%;
    border-radius: 10px;
    overflow: hidden;
    min-width: 500px;
  }

  .end-num {
    height: 86px;
    padding-left: 10px;
    font-weight: 700;
    background-color: #fff;
    min-width: 200px;
    border: 1px solid #ddd;
  }

  .end-num p {
    font-size: 14px;
    line-height: 30px;
    font-weight: 400;
  }

  .div-wrap div {
    float: left;
    height: 50px;
  }

  .div-wrap .div1 {
    width: 20%;
    height: 57px;
    background: url('/static/img/计时.png') no-repeat;
    -webkit-background-size: 28% 50px;
    background-size: 98% 50px;
    font-size: 24px;
    font-weight: 700;
    line-height: 50px;
    text-align: center;
  }

  .div-wrap .div2 {
    width: 5%;
    line-height: 50px;
    font-size: 30px;
  }
  .end-time {
    height: 86px;
    background-color: #f63051;
    font-size: 24px;
    color: white;
    line-height: 86px;
    text-align: center;
    min-width: 200px;
    cursor: pointer;
    img {
      width: 27px;
      height: 11px;
      margin-left: 5px;
    }
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
  }
  .route div {
    float: left;
    width: 280px;
    border: 2px solid #313131;
    font-size: 18px;
    margin-right: 26px;
    font-weight: 700;
    cursor: pointer;
    color: #333;
    text-align: center;
  }

  .awards-detail {
    height: auto;
    overflow: hidden;
    margin-top: 48px;
  }

  .route .active {
    color: white;
    background: #313131;
    border: 2px solid #313131;
  }

  .join-active {
    width: 100%;
    overflow: hidden;
    padding-bottom: 30px;
    background-color: #f2f2f2;
  }

  .actives {
    width: 100%;
    background-color: #fff;
    overflow: hidden;
  }

  .once-join {
    text-align: center;
    margin: 26px 0;
  }
  .once-join button {
    width: 132px;
    height: 50px;
    line-height: 40px;
    font-size: 18px;
    background-color: #f63051;
    border-color: #f63051;
  }
}

</style>
