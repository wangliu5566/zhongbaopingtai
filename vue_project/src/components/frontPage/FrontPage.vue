<template>
  <div class="wrap" :class="showHeadImg?'show-head-bg':''">
    <div class='pc-head' v-show="terminal=='pc'">
      <img src="/static/img/logo2.png" id="logo" @click="goIndex" style="cursor:pointer;width:13%" v-show="!showHeadImg">
      <img src="/static/img/logo1.png" id="logo" @click="goIndex" style="cursor:pointer;width:13%" v-show="showHeadImg">
      <div class="noLogin1" v-show="!myLogins" @click="goLogin">
        <img src="/static/img/登录（黑）.png" v-show="!showHeadImg">
        <img src="/static/img/登录.png" v-show="showHeadImg">
      </div>
      <div class="noLogin" v-show="myLogins">
        <img :src="url" class="head-portrait" @click="showOut">
        <span class="pc-name">{{userObj.username}}</span>
        <span class="person-open" @click="goPerson">个人中心</span>
        <div class="pc-show-out" v-show="isShow">
          <p class="cla"></p>
          <p class="pc-out-word" @click="goOut">退出</p>
        </div>
      </div>
    </div>
    <!-- 手机 -->
    <div id='phone-head' style="position:relative;" v-show="terminal!='pc'">
      <div class="user-info">
        <img src="../../../static/img/登录.png" v-show="!myLogins" @click="goLogin">
        <img :src="url" v-show="myLogins" @click="showOut" style="border-radius:20px;">
      </div>
      <img class="login-img" src="/static/img/logo1.png" @click="goIndex">
      <div class="show-out" v-show="isShow">
        <p class="cla"></p>
        <p class="out-word" @click="goOut">退出</p>
        <p class="out-word" @click="goPerson">个人中心</p>
      </div>
    </div>
    <router-view :style="{minHeight:currentHeight}" :datas="setBannerize"></router-view>
    <footer class="foot" v-show="terminal=='pc'">
      <div class="foot-div">
        <div><i></i>010-82783029-803 ( 13811797317 )</div>
        <div><i class="icon1"></i>北京市海淀区西三旗建材城西路31号B座四层西区</div>
        <div><i class="icon2"></i>qbz@kingchannels.com</div>
      </div>
      <p class="foot-p">京ICP备10010903号-4</p>
    </footer>
    <footer class="phone-foot" v-show="terminal=='phone'">
      <div class="foot-div" style="overflow: hidden">电话：010-82783029-803
        <span style="float:right">手机：13811797317</span>
      </div>
      <div class="foot-div">邮箱：qbz@kingchannels.com
        <span style="float:right">京ICP备10010903号-4</span>
      </div>
      <div class="foot-div">地址：北京市海淀区西三旗建材城西路31号B座四层西区</div>
    </footer>
    <div class="floatImg" v-show="terminal=='pc'">
      <div class="imgs" @click="goTop"></div>
      <div style="height: 2px;background:white"></div>
      <div class="imgs1" @click="goIndex"></div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      myLogins: false,
      userObj: {
        username: '',
      },
      url: '',
      isShow: false,
      currentHeight: document.body.clientHeight - 175,
      setBannerize: document.body.clientWidth / 1920,
      fontSize: 100,
      showHeadImg: false,
    }
  },
  created() {
    this.fetchDate();
  },
  mounted() {
    this.terminal = IsPC() ? 'pc' : 'phone'
    this.setBannerize = document.body.clientWidth / 1920
    let winWidth = window.innerWidth;
    this.fontSize = (winWidth / 750) * 200;

    if (this.terminal == 'pc') {
      this.currentHeight = document.body.clientHeight - 175 + 'px';
      this.setBannerize = document.body.clientWidth / 1920
    } else {
      this.currentHeight = document.body.clientHeight / this.fontSize - 1.65 + 'rem';
    }
    const that = this
    window.onresize = () => {
      return (() => {
        if (this.terminal == 'pc') {
          that.currentHeight = document.body.clientHeight - 175 + 'px';
          that.setBannerize = document.body.clientWidth / 1920
        } else {
          that.currentHeight = document.body.clientHeight / this.fontSize - 1.65 + 'rem';
        }
      })()
    }
  },
  watch: {
    "$route": "fetchDate",
    currentHeight(val) {
      this.currentHeight = val
    },
    setBannerize(val) {
      this.setBannerize = val
    }
  },
  methods: {
    fetchDate() {
      var path = this.$route.fullPath;
      this.showHeadImg = path.indexOf('/news?') > 0 ? true : false; //控制报道页面的头部样式
      if (path.indexOf('user_id') > 0 && path.indexOf('access_token') > 0) {
        let userId = path.split('user_id=')[1];
        this.$cookie.set('zxjq_userId', userId, { expires: 10 });
        sessionStorage.userId = userId;
        if (path.indexOf('access_token=') != -1) {
          this.$cookie.set('zxjq_accessToken', path.split('access_token=')[1].split("&")[0], { expires: 10 });
          sessionStorage.accessToken = path.split('access_token=')[1].split("&")[0];
        } else if (path.indexOf('access_token%3D') != -1) {
          this.$cookie.set('zxjq_accessToken', path.split('access_token%3D')[1].split("&")[0], { expires: 10 });
          sessionStorage.accessToken = path.split('access_token%3D')[1].split("&")[0];
        }
        path = path.split('&access_token')[0];
        this.myLogins = true;
        this.getUserInfo(this.$cookie.get('zxjq_userId'), this.$cookie.get('zxjq_accessToken'));
        let routeQuery = this.$route.query;
        let routePath = this.$route.path;
        let newRouteQuery = {};
        for (var item in routeQuery) {
          if (item != 'user_id' && item != 'access_token') {
            newRouteQuery[item] = routeQuery[item];
          }
        }
        this.$router.push({
          path: routePath,
          query: newRouteQuery
        })

      } else {
        let cUserId = this.$cookie.get('zxjq_userId');
        let cAccessToken = this.$cookie.get('zxjq_accessToken');
        sessionStorage.userId = cUserId;
        sessionStorage.accessToken = cAccessToken;
        if (cUserId && cUserId != '' && cAccessToken && cAccessToken != '') {
          this.getUserInfo(cUserId, cAccessToken);
          this.myLogins = true;
        } else {
          this.myLogins = false;
        }
      }
    },
    goLogin() {
      login(this.$route.fullPath);
    },
    goOut() {
      this.isShow = false;
      this.myLogins = false;
      sessionStorage.removeItem('userId');
      sessionStorage.removeItem('userObj');
      sessionStorage.removeItem('accessToken');
      this.$cookie.delete('zxjq_userId');
      this.$cookie.delete('zxjq_accessToken');

      this.$router.push("/frontPage/index");
    },
    getUserInfo(userId, accessToken) {
      this.$http.get(baseUrlCommon + "/v1/frontend/user/get-user-info", {
          params: {
            userId: userId,
            accessToken: accessToken,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.userObj = res.data.data;
            sessionStorage.setItem('userObj', JSON.stringify({
              username: res.data.data.username ? res.data.data.username : '',
              bigAvatar: res.data.data.bigAvatar ? res.data.data.bigAvatar : '',
              middleAvatar: res.data.data.middleAvatar ? res.data.data.middleAvatar : '',
            }));
            if (res.data.data.bigAvatar) {
              this.url = res.data.data.bigAvatar
            } else if (res.data.data.middleAvatar) {
              this.url = res.data.data.middleAvatar
            } else {
              this.url = "../../../static/img/default_photo.png";
            }
          }
        })
        .catch((err) => {
          console.log(err)
        })
    },
    showOut() {
      this.isShow = !this.isShow;
    },
    goIndex() {
      this.$router.push("/frontPage/index")
    },
    goTop() {
      const sTop = document.documentElement.scrollTop || document.body.scrollTop;
      this.scrollTop(window, sTop, 0, 500);
    },
    goPerson() {
      if (this.terminal != 'pc') {
        this.isShow = !this.isShow;
        this.$router.push('/phonePerson')
      } else {
        this.$router.push('/frontPage/person')
      }
    },
  }

}

</script>
</script>
<style lang='less'>
/*pc*/

.wrap {
  width: 100%;
  overflow: hidden;
  position: relative;
}

.show-head-bg {
  background: url('../../../static/img/新闻页1.png') no-repeat;

  .pc-head {
    width: 1200px;
    height: 65px;
    margin: 25px auto 0;
    .pc-out-word {
      color: white;
    }
    .noLogin {
      color: white;
    }
  }
}

.pc-head {
  width: 1200px;
  height: 65px;
  margin: 25px auto 0;

  .pc-name {
    display: inline-block;
    width: 100px;
    height: 40px;
    margin: 0 10px;
    text-align: left;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
  .person-open {
    display: inline-block;
    width: 65px;
    height: 40px;
    margin-top: -20px;
    position: absolute;
    top: 20px;
    right: 0;
  }
  .person-open:hover {
    color: #0d96ca;
  }
  .pc-show-out {
    position: absolute;
    top: 30px;
    left: 0;
    width: 100px;
    height: 50px;
    z-index: 100008989;
  }

  .cla {
    width: 5px;
    height: 8px;
    margin-left: 10px;
    border: 10px solid transparent;
    border-bottom: 10px solid #232323;
  }
  .pc-out-word {
    width: 100%%;
    height: 40px;
    line-height: 40px;
    background-color: #232323;
    color: #999;
    text-align: center;
    font-size: 16px;
  }
  .head-portrait {
    display: inline-block;
    width: 36px;
    height: 36px;
    margin-right: 15px;
    position: absolute;
    left: 0;
    border-radius: 20px;
  }
  .noLogin1 {
    width: 36px;
    float: right;
    margin-right: 20px;
    cursor: pointer;
    img {
      width: 100%;
    }
  }
  .noLogin {
    width: 240px;
    height: 40px;
    line-height: 40px;
    float: right;
    text-align: center;
    font-size: 16px;
    cursor: pointer;
    margin-top: 12px;
    color: #000000;
    position: relative;
  }
}

.floatImg {
  width: 40px;
  height: 80px;
  position: fixed;
  bottom: 90px;
  right: 20px;
  z-index: 18;
}

.imgs,
.imgs1 {
  height: 40px;
  background: url("../../../static/img/箭头2.png") no-repeat 10px 7px gray;
  -webkit-background-size: 50%;
  background-size: 50%;
  cursor: pointer;
}

.imgs1 {
  background: url("../../../static/img/首页.png") no-repeat 8px 8px gray;
  -webkit-background-size: 24px;
  background-size: 24px;
}

.imgs1:hover,
.imgs:hover {
  background-color: #f53051;
}

.foot {
  height: 85px;
  width: 100%;
  background-color: #000;
  color: white;
  font-size: 12px;
  position: relative;

  .foot-div,
  .foot-p {
    width: 1200px;
    margin: 0 auto;
    text-align: center;
    font-size: 14px;
  }
  .foot-p {
    color: #646464;
  }

  .foot-div div {
    float: left;
    width: 33.33%;
    line-height: 54px;
    font-size: 16px;
    position: relative;
  }

  i {
    display: inline-block;
    width: 30px;
    height: 30px;
    position: absolute;
    top: 20px;
    left: 10%;
    background: url("../../../static/img/电话.png") no-repeat;
    -webkit-background-size: 14px;
    background-size: 14px;
  }
  .icon1 {
    left: -1%;
    top: 18px;
    background: url("../../../static/img/地址.png") no-repeat;
    -webkit-background-size: 14px;
    background-size: 14px;
  }
  .icon2 {
    top: 22px;
    left: 20%;
    background: url("../../../static/img/邮箱.png") no-repeat;
    -webkit-background-size: 16px;
    background-size: 16px;
  }
}

</style>
