<template>
  <div>
    <div class="pc-init-question" v-if="terminal=='pc'">
      <img :src="pcImg" style="width: 100%;height: 100%;">
      <div class="opacity-bg"></div>
      <div class="top-wrap">
        <div class="position-all">
          <!-- 倒计时 -->
          <div class="will-start">
            <p>距离问卷报名开始</p>
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
          <!-- 内容 -->
          <div class="init-q-content">
            <h3><i></i> <span>{{questionObj.name}}</span></h3>
            <div class="wrap-ab">
              <textarea style="resize:none" readonly="readonly" class="abstract-word">{{questionObj.intro}}</textarea>
              <div class="float-div">
                <img :src="bigImg">
                <div class="QR-code">
                  <img :src="qrCodeurl">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ********************************************    手机端    ***************************************** -->
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
        <img v-if="questionObj.mobileImage" :src="baseUrl+questionObj.mobileImage">
        <div class="phone-will-start">
          <p>距离问卷报名开始</p>
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
      <!-- 新增简介和封面图 -->
      <div class="phone-abstract">
        <h3><i></i> <span>{{questionObj.name}}</span></h3>
        <textarea style="resize: none;" readonly="readonly" class="abstract1">{{questionObj.intro}}</textarea>
        <div class="float-div">
          <img :src="phoneSmallImg">
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
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      questionId: "",
      questionObj: {},
      signStatus: 0, //用户报名状态
      activityEndTime: 10, //报名倒计时
      pcImg: '/static/img/slider1.jpg', //背景大图
      bigImg: '', //封面图
      days1: '0',
      hours1: '0',
      minute1: '0',
      second1: '0',
      qrCodeurl: '', //二维码
      phoneSmallImg: '', //手机端的封面图
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
  beforeDestroy() {
    clearTimeout(this.timer);
    clearInterval(this.timer2)
  },
  mounted() {
    this.isSign = sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null" ? false:true;
    this.terminal = IsPC() ? 'pc' : 'phone'
    if (this.$route.query.questionId) {
      this.questionId = this.$route.query.questionId
      this.getDatas();
      this.showLastTime(this.questionId) //请求倒计时
      this.showTime() //倒计时动态变化
      this.getImg() //获取二维码
    } else {
      this.$router.push("/frontPage/index")
    }
  },
  methods: {
    //获取数据
    getDatas() {
      this.$http.get("/v1/frontend/activity/get-info", {
          params: {
            activityId: this.questionId,
            type: this.$route.query.type,
            userId: sessionStorage.userId
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.questionObj = res.data.data.activity;
            this.pcImg = baseUrl + res.data.data.activity.pcImage;
            this.bigImg = baseUrl + res.data.data.activity.bigImage;
            this.phoneSmallImg = baseUrl + res.data.data.activity.smallImage;
            this.signStatus = res.data.data.signStatus;
          }
        })
        .catch((err) => {
          console.log(err)
        })
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
                _this.showLastTime(_this.questionId)
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
        .catch((err) => {})
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
    background: url('../../../static/img/计时.png') no-repeat;
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
  /*-------------------------  下面内容部分  --------------------  */
  .phone-abstract {
    width: 94%;
    margin-left: 3%;
    margin-bottom: 0.2rem;
  }

  h3 {
    line-height: 0.3rem;
    margin-top: 0.1rem;
    font-size: 0.15rem;
  }

  .abstract1 {
    width: 100%;
    height: 2.7rem;
    border: none;
    overflow-y: visible;
    font-size: 0.14rem;
    line-height: 0.25rem;
    outline: none;
    margin-bottom: 0.2rem;
    margin-top: 0.1rem;
  }

  img {
    width: 100%;
    height: 1.75rem;
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

.pc-init-question {
  width: 100%;
  height: 799px;
  overflow: hidden;
  position: relative;

  .opacity-bg {
    position: absolute;
    top: 0;
    width: 100%;
    height: 799px;
    background-color: black;
    opacity: 0.6;
    z-index: 1;
  }
  .top-wrap {
    width: 100%;
    height: 799px;
    overflow: hidden;
    z-index: 12;
    position: absolute;
    top: 0;
    left: 0;
  }
  .position-all {
    width: 1200px;
    height: 100%;
    margin: 0 auto;
  }

  /*-----------------------   倒计时  -------------------------- */
  .will-start {
    width: 300px;
    height: 86px;
    padding-left: 15px;
    font-weight: 700;
    background-color: #565251;
    border-radius: 15px;
    margin: 85px 0 85px 450px;
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
  /*------------------------ 下面内容 ------------------------------------*/
  .init-q-content {
    i {
      display: inline-block;
      width: 5px;
      height: 27px;
      background-color: white;
      margin-top: 1px;
      position: absolute;
      top: 10px;
    }

    h3 {
      width: 100%;
      height: 45px;
      font-size: 30px;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      margin-top: -8px;
      margin-bottom: 18px;
      position: relative;

      span {
        margin-left: 20px;
        color: white;
      }
    }

    .wrap-ab {
      height: 370px;
      width: 100%;
      overflow: hidden;
    }

    .abstract-word {
      width: 620px;
      height: 370px;
      border: 2px solid #313131;
      overflow-y: visible;
      padding: 15px;
      font-size: 14px;
      line-height: 25px;
      outline: none;
      float: left;
      margin-right: 20px;
    }

    .float-div {
      width: 560px;
      height: 100%;
      overflow: hidden;
      border: 1px solid #999;
      float: left;
      position: relative;

      img {
        width: 100%;
        height: 100%;
      }
    }

    .QR-code {
      position: absolute;
      top: 270px;
      left: 0;
      width: 100px;
      height: 100px;
      z-index: 10;
    }
  }
}

</style>
