<template>
  <div class="question" ref="question">
    <div class="img-pc" v-if="terminal=='pc'">
      <img v-if="questionObj.pcImage" :src="baseUrl+questionObj.pcImage">
    </div>
    <div class="pc-question" v-if="terminal=='pc'">
      <h3 class="question-name"><i class="left-i"></i> <span style="margin-left: 20px;">{{questionObj.name}}</span></h3>
      <!-- <textarea style="resize:none" readonly="readonly" class="abstract-word">{{questionObj.intro}}</textarea> -->
      <div class="content">
        <div class="button-wrap">
          <img v-if="questionObj.smallImage" :src="baseUrl+questionObj.smallImage">
          <button v-if="status==1" @click="logins">立即登录</button>
          <button v-if="status==2" @click="signUp">立即报名</button>
          <button v-if="status==3" @click="onceJoin">立即答卷</button>
          <button v-if="status==4" @click="getAward">立即抽奖</button>
          <button v-if="status==5">活动结束</button>
          <button v-if="status==6">已经抽奖</button>
          <div class="QR-code">
            <img :src="qrCodeurl">
          </div>
        </div>
        <div class="show-user" id="wrap-ul">
          <ul class="wrap-ul" ref="uls" :style="{top:topNum+'px'}" @mouseenter="stopPlay" @mouseleave="autoPlay">
            <li v-if="userList.length==0">暂无用户获取积分</li>
            <li v-if="userList.length!=0" v-for='item in userList' :key="item.id">
              <div>用户: {{item.username}}</div>
              <div>获得:<span>{{item.score}}</span>积分</div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 更多活动 -->
      <div class="more-active">
        <moreActive></moreActive>
      </div>
    </div>
    <!-- ********************************    手机端    ************************************************* -->
    <div v-else class="phone-question">
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
        <button v-if="status==1" @click="logins">立即登录</button>
        <button v-if="status==2" @click="signUp">立即报名</button>
        <button v-if="status==3" @click="onceJoin">立即答卷</button>
        <button v-if="status==4" @click="getAward">立即抽奖</button>
        <button v-if="status==5">活动结束</button>
        <button v-if="status==6">已经抽奖</button>
      </div>
      <div style="height: 0.1rem;background:#e4e4e4;"></div>
      <!-- 新增简介和封面图 -->
      <div class="phone-abstract">
        <h3><i></i> <span>{{questionObj.name}}</span></h3>
        <!-- <textarea style="resize: none;" readonly="readonly" class="abstract1">{{questionObj.intro}}</textarea> -->
        <div class="float-div">
          <img :src="phoneSmallImg">
        </div>
      </div>
      <div style="height: 0.1rem;background:#e4e4e4;"></div>
      <p class="hr"></p>
      <div class="title-list" id="luckyDraw">积分抽奖名单</div>
      <div class="show-user" id="wrap-ul">
        <ul class="wrap-ul" ref="uls" :style="{top:topNum+'px'}" @mouseenter="stopPlay" @mouseleave="autoPlay">
          <li v-if="userList.length==0">暂无用户获取积分</li>
          <li v-if="userList.length!=0" v-for='item in userList' :key="item.id">
            用户: {{item.username}} <span>获得:{{item.score}}积分</span>
          </li>
        </ul>
      </div>
    </div>
    <div id="foot1" style="width:100%;height:1px;"></div>
    <!-- 展示二维码的模态框 -->
    <Modal v-model="showQRCode1" id="share-QR-code1">
      <img :src="qrCodeurl">
    </Modal>
    <Modal v-model="modalOpen" title="提示" @on-ok="ok">
      <p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;立即前往答卷页面，请点击确定</p>
    </Modal>
    <!-- 抽奖模态框 -->
    <Modal v-model="modals" width="600" class="modal-1">
      <p class="lucky-prize">抽奖环节</p>
      <div class="rotate-con-pan">
        <div class="rotate-con-zhen"></div>
      </div>
    </Modal>
  </div>
</template>
<script>
import moreActive from "../common/MoreActive.vue"
export default {
  data() {
    return {
      baseUrl: baseUrl,
      topNum: 0,
      questionId: '',
      type: '',
      questionObj: {},
      terminal: 'pc',
      userList: [],
      isSign: false,
      status: 1,
      signStatus: 0, //从后台拿到的数据
      modals: false,
      modalOpen: false,
      phoneSmallImg: "", //二维码
      qrCodeurl: '',
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
        title: "抽奖名单",
        smallTitle: "LUCKY DRAW",
        path: ""
      }, {
        title: "联系我们",
        smallTitle: "CONTACT US",
        path: ""
      }],
      showBG: 1,
      showQRCode1: false
    }
  },
  mounted() {
    this.isSign = sessionStorage.userId != '' && !!sessionStorage.userId && sessionStorage.userId != "null"? true : false;
    this.terminal = IsPC() ? 'pc' : 'phone'
    if (this.$route.query.questionId) {
      this.questionId = this.$route.query.questionId
      this.type = this.$route.query.type;
      this.getDatas();
      this.getUserList();
      this.getImg();
    } else {
      this.$router.push("/frontPage/index")
    }

    let url = "../../../static/getPrize/jQueryRotate.min.js"
    let script = document.createElement('script')
    script.setAttribute('src', url)
    this.$refs.question.appendChild(script);
    let url1 = "../../../static/getPrize/script1.min.js"
    let script1 = document.createElement('script')
    script1.setAttribute('src', url1)
    this.$refs.question.appendChild(script1);
  },
  components: {
    moreActive
  },
  methods: {
    getDatas() {
      this.$http.get("/v1/frontend/activity/get-info", {
          params: {
            activityId: this.questionId,
            type: this.type,
            userId: sessionStorage.userId
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.questionObj = res.data.data.activity;
            this.signStatus = res.data.data.signStatus;
            this.phoneSmallImg = baseUrl + res.data.data.activity.smallImage;
            this.getStatus();
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

    },
    //获取用户状态
    getStatus() {
      if (this.$route.query.status == 3) {
        this.status = 5; //活动结束
      } else if (this.$route.query.status == 2) {
        if (!this.isSign) {
          this.status = 1; //未登录
        } else if (this.isSign && this.signStatus == 0) { //
          this.status = 2; //已登录未报名
        } else if (this.isSign && this.signStatus == 1) { //
          this.status = 3; //已报名未答卷
        } else if (this.isSign && this.signStatus == 2) { //
          this.status = 4; //已答卷
        } else if (this.isSign && this.signStatus == 3) { //
          this.status = 6; //已抽奖
        }
      } else if (this.$route.query.status == 1) {
        this.status = 7; //活动还未开始
      }
    },
    //获取用户列表
    getUserList() {
      this.$http.get("/v1/frontend/user-score/lists", {})
        .then((res) => {
          if (res.data.status == 200) {
            this.userList = res.data.data;
            this.autoPlay();
          }
        })
        .catch((err) => {
          console.log(err)
        })
    },
    autoPlay() {
      if (this.userList.length < 13) {
        this.topNum = 0;
      } else {
        var _this = this
        if (_this.terminal == 'pc') {
          _this.timer = setInterval(function() {
            if (_this.userList.length * 40 + _this.topNum <= 480) {
              _this.topNum = 0;
            } else {
              _this.topNum = _this.topNum - 40;
            }
          }, 2000)
        } else {
          _this.timer = setInterval(function() {
            if (_this.userList.length * 40 + _this.topNum <= 400) {
              _this.topNum = 0;
            } else {
              _this.topNum = _this.topNum - 40;
            }
          }, 2000)
        }

      }
    },
    stopPlay() {
      var _this = this
      clearInterval(_this.timer)
    },
    //登录
    logins() {
      //跳转到登录页面
      login(this.$route.fullPath)
    },
    //立即报名
    signUp() {
      this.$http.get("/v1/frontend/activity/user-sign-up", {
          params: {
            activityId: this.questionId,
            userId: sessionStorage.userId,
          }
        })
        .then((res) => {
          if (res.data.status == '200') {
            this.getDatas();
            this.$Message.success({
              content: res.data.message,
              closable: true
            });
          } else {
            this.$Message.warning({
              content: res.data.message,
              closable: true
            });
          }
        })
        .catch((err) => {
          console.log(err)
        })
    },
    //立即答卷
    onceJoin() {
      this.$http.get("/v1/frontend/activity/get-info", {
          params: {
            activityId: this.questionId,
            type: this.type,
            userId: sessionStorage.userId
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.signStatus = res.data.data.signStatus;
            if (this.signStatus == 1) { //已报名
              this.modalOpen = true;
            } else if (this.signStatus == 2) { //已答卷
              this.status = 4;
            }
          }
        })
        .catch((err) => {
          console.log(err)
        })
    },
    //立即抽奖
    getAward() {
      this.modals = true;
      this.startPrize()
    },
    startPrize() {
      var _this = this
      $(".rotate-con-zhen").rotate({
        bind: {
          click: function() {
            var a = runzp();
            $(this).rotate({
              duration: 5000, //转动时间
              angle: 0, //起始角度
              animateTo: 1440 + a.angle, //结束的角度
              easing: $.easing.easeOutSine, //动画效果，需加载jquery.easing.min.js
              callback: function() {
                $.ajax({
                  url: baseUrl + '/v1/frontend/activity/lucky-draw',
                  type: 'get',
                  data: {
                    activityId: location.href.split('questionId=')[1].split('&')[0],
                    userId: sessionStorage.userId,
                  },
                  success: function(res) {
                    if (res.status == 200) {
                      message.showMessage('success', '恭喜获得积分：' + res.data);
                      _this.status = 6;
                      _this.getUserList();
                    } else {
                      message.showMessage('warning', res.message);
                    }
                  }
                })
              }
            });
          }
        }
      });
    },
    ok() {
      window.open(this.questionObj.questionAnswerDetail + "&code=" + sessionStorage.accessToken + '&activityId=' + this.questionId + '&callback=' + window.location.href);
    },
    goPath(path, index) {
      this.showBG = index;
      if (index == 1) {
        this.$router.push("/frontPage/index")
      } else if (index == 2) { //分享活动
        this.getImg()
        this.showQRCode1 = true;
      } else if (index == 3) {
        var anchor = this.$el.querySelector('#foot1')
        document.body.scrollTop = anchor.offsetTop
      } else if (index == 4) {
        var anchor = this.$el.querySelector('#luckyDraw')
        document.body.scrollTop = anchor.offsetTop
      }
      this.showMenu = false;
    },
  }
}

</script>
<style lang='less'>
.phone-question {
  position: relative;
  .img {
    width: 100%;
    height: 2.15rem;
    overflow: hidden;
    position: relative;

    button {
      width: 60%;
      line-height: 0.44rem;
      border: none;
      background-color: #f63051;
      color: white;
      font-size: 0.15rem;
      font-weight: 700;
      position: absolute;
      top: 1.55rem;
      left: 20%;
      outline: none;
    }
  }
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
    font-size: 14px;
    line-height: 25px;
    outline: none;
    margin-bottom: 0.2rem;
    margin-top: 0.1rem;
  }

  img {
    width: 100%;
    height: 175px;
  }

  .hr {
    width: 6%;
    height: 0.04rem;
    margin-top: 0.2rem;
    background-color: #000;
    margin-left: 3%;
  }

  .title-list {
    font-size: 0.16rem;
    font-weight: 700;
    line-height: 40px;
    margin-left: 3%;
  }
  .show-user {
    width: 94%;
    margin: 0 auto 30px;
    height: 400px;
    border: 1px solid #666;
    overflow: hidden;
    position: relative;
  }
  .wrap-ul {
    width: 100%;
    overflow: hidden;
    padding: 0 4%;
    font-size: 14px;
    position: absolute;
    top: 0;
    left: 0;
  }
  .wrap-ul li {
    height: 40px;
    line-height: 40px;
    cursor: pointer;
    border-bottom: 1px solid #999;
  }

  li span {
    float: right;
    margin-right: 10px;
  }
}

.question {
  width: 100%;

  .img-pc {
    width: 100%;
    overflow: hidden;
    margin-bottom: 25px;

    img {
      width: 100%;
      height: 100%;
    }
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

.pc-question {
  width: 1200px;
  margin: 0 auto;
  overflow: hidden;


  .question-name {
    width: 1200px;
    margin: 0 auto;
    font-size: 28px;
    text-align: left;
    line-height: 90px;
    position: relative;
    color: #323232;
  }

  .left-i {
    display: inline-block;
    width: 5px;
    height: 27px;
    background-color: #323232;
    margin-top: 1px;
    position: absolute;
    top: 30px;
  }

  .abstract-word {
    width: 1200px;
    height: 370px;
    overflow-y: visible;
    padding: 15px;
    font-size: 14px;
    line-height: 25px;
    outline: none;
    margin-bottom: 50px;
  }

  .QR-code {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100px;
    height: 100px;
    z-index: 10;
  }
  .content {
    width: 1200px;
    height: 488px;
    margin: 0 auto;
  }

  .button-wrap,
  .show-user {
    position: relative;
    height: 100%;
    overflow: hidden;
    float: left;

    img {
      width: 100%;
      height: 100%;
    }

    button {
      width: 30%;
      height: 60px;
      line-height: 60px;
      font-size: 24px;
      border: none;
      background-color: #f22b64;
      color: white;
      font-weight: 700;
      position: absolute;
      bottom: 31px;
      right: 2%;
      outline: none;
      border: none;
      cursor: pointer;
    }
  }
  .button-wrap {
    width: 870px;
    margin-right: 30px;
  }

  .show-user {
    width: 300px;
    border: 2px solid #313131;
    position: relative;
  }

  .wrap-ul {
    width: 256px;
    margin: 0 20px;
    position: absolute;
    top: 0;
    left: 0;
  }
  .wrap-ul li {
    height: 40px;
    line-height: 40px;
    cursor: pointer;
    font-size: 15px;
  }

  li div:nth-child(1) {
    float: left;
    width: 155px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-right: 10px;
  }

  li div:nth-child(2) {
    float: left;
    width: 90px;
  }
  li span {
    display: inline-block;
    width: 25px;
    height: 40px;
    text-align: center;
  }

  .ivu-btn-primary {
    border: none;
  }
}

@media screen and (min-width: 778px) {
  .modal-1 .lucky-prize {
    line-height: 40px;
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 15px;
    padding-left: 45px;
  }

  .modal-1 .rotate-con-pan {
    background: url('/static/img/disk.jpg') no-repeat 0 0;
    background-size: 100% 100%;
    position: relative;
    width: 480px;
    height: 480px;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    margin: 0 auto;
  }

  .modal-1 .rotate-con-zhen {
    width: 112px;
    height: 224px;
    background: url("/static/img/start.png") no-repeat 0 0;
    background-size: 100% 100%;
    cursor: pointer;
    position: absolute;
    left: 180px;
    top: 140px;
  }
}

@media screen and (max-width: 778px) {
  .modal-1 .lucky-prize {
    line-height: 40px;
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 15px;
    padding-left: 45px;
  }

  .modal-1 .rotate-con-pan {
    background: url('/static/img/disk.jpg') no-repeat 0 0;
    background-size: 100% 100%;
    position: relative;
    width: 260px;
    height: 260px;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    margin: 0 auto;
  }

  .modal-1 .rotate-con-zhen {
    width: 60px;
    height: 120px;
    background: url("/static/img/start.png") no-repeat 0 0;
    background-size: 100%;
    cursor: pointer;
    position: absolute;
    left: 100px;
    top: 70px;
  }
}

</style>
