<template>
  <div class="person" v-if="terminal=='pc'">
    <div class="wrap-all">
      <div class="wrap-left">
        <div class="head-img">
          <img :src="url">
        </div>
        <p class="user-name">{{userObj.username}}</p>
        <p class="user-mail">{{userObj.email?userObj.email:'暂无邮箱'}}</p>
        <ul class="menu-person">
          <li :class="liIndex==0?'active':''" @click="goIndex(0,'/frontPage/person/myactivities')">我的活动</li>
          <li :class="liIndex==1?'active':''" @click="goIndex(1,'/frontPage/person/mymatch')">我的作品</li>
          <li :class="liIndex==2?'active':''" @click="goIndex(2,'/frontPage/person/personalData')">个人资料</li>
          <li :class="liIndex==3?'active':''" @click="goIndex(3,'/frontPage/person/setPsd')">{{hasPsd?'重置密码':"设置密码"}}</li>
        </ul>
      </div>
      <div class="wrap-right">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      isSign: true,
      liIndex: 0,
      url: "/static/img/default_photo.png",
      userObj: {},
    }
  },
  mounted() {
    this.isSign = sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null" ? false:true;
    this.terminal = IsPC() ? 'pc' : 'phone'
    if (this.isSign) {
      this.changeRoute()
      this.getPerson()
    } else {
      this.$router.push("/frontPage")
    }
  },
  methods: {
    changeRoute() {
      if (this.$route.fullPath.indexOf('myactivities') != -1) {
        this.liIndex = 0
      } else if (this.$route.fullPath.indexOf('mymatch') != -1) {
        this.liIndex = 1
      } else if (this.$route.fullPath.indexOf('personalData') != -1) {
        this.liIndex = 2
      } else if (this.$route.fullPath.indexOf('/setPsd') != -1) {
        this.liIndex = 3
      }
    },
    goIndex(index, path) {
      if (this.terminal == 'pc') {
        this.liIndex = index;
        this.$router.push(path)
      }
    },
    getPerson() {
      this.$http.get(baseUrlCommon + "/v1/frontend/user/get-user-info", {
          params: {
            userId: sessionStorage.userId,
            accessToken: sessionStorage.accessToken
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.userObj = res.data.data;
            if (res.data.data.bigAvatar) {
              this.url = res.data.data.bigAvatar
            } else if (res.data.data.middleAvatar) {
              this.url = res.data.data.middleAvatar
            } else {
              this.url = "/static/img/default_photo.png";
            }
          }
        })
        .catch((err) => {
          console.log(err)
        })
    },
    fetchDate() {
      this.changeRoute()
    }
  },
  watch: {
    "$route": "fetchDate",
  }
}

</script>
<style lang='less'>
.person {
  width: 100%;
  background-color: #f2f2f2;
  overflow: hidden;

  .wrap-all {
    width: 1200px;
    margin: 0 auto;
    margin-top: 37px;
  }

  .wrap-left {
    width: 270px;
    height: 700px;
    background-color: #fff;
    margin-right: 15px;
    float: left;
  }
  .wrap-right {
    float: left;
    width: 915px;
    min-height: 700px;
    background-color: #fff;
    overflow: hidden;
  }
  .head-img {
    width: 140px;
    height: 140px;
    margin: 40px auto 16px;
    img {
      width: 100%;
      height: 100%;
      border-radius: 70px;
    }
  }
  .user-name {
    font-size: 20px;
    text-align: center;
    line-height: 40px;
    color: #333;
  }
  .user-mail {
    font-size: 12px;
    color: #999;
    line-height: 20px;
    text-align: center;
    padding-bottom: 20px;
    border-bottom: 1px solid #e4e4e4;
  }
  .menu-person {
    margin-top: 21px;
    overflow: hidden;
  }
  .menu-person li {
    height: 60px;
    line-height: 60px;
    font-size: 18px;
    text-align: center;
    cursor: pointer;
    border-right: 7px solid #fff;
  }

  .menu-person .active {
    border-right: 7px solid #0099d2;
    background-color: #e3eef2;
    color: #0099d2;
  }
}

</style>
