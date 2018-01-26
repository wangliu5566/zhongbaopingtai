<template>
  <div class="phone" v-if="terminal!='pc'">
    <div class="set-mail" style="position:relative;">
      <span @click="goPage('/phoneData')">取消</span>
      <span>绑定手机</span>
      <!-- <span @click="updateMail()">完成</span> -->
    </div>
    <div class="middle" :style="{minHeight:mailHeight}">
      <Form :model="userObj" :label-width="75" ref="formValidate">
        <FormItem label="手机：" prop="email">
          <Input v-model="userObj.phone" placeholder="请输入手机" style="width: 96%"></Input>
        </FormItem>
        <FormItem label="验证码：" prop="code">
          <Input v-model="userObj.code" placeholder="请输入验证码" style="width:50%"></Input>
          <Button v-if="isUse" type="primary" @click="phoneCode()" style="float: right;margin-right: 4%">获取验证码</Button>
          <Button v-if="!isUse" type="primary" disabled>{{remainTime}}s</Button>
        </FormItem>
      </Form>
      <div style="text-align: center;">
        <Button type="primary" @click="savePhone" style="width: 96%;font-size: 14px;">保&nbsp;&nbsp;存</Button>
      </div>
    </div>
    <footer class="phone-foot">
      <div class="foot-div" style="overflow: hidden">电话：010-82783029-803
        <span style="float:right">手机：13811797317</span>
      </div>
      <div class="foot-div">邮箱：qbz@kingchannels.com
        <span style="float:right">京ICP备10010903号-4</span>
      </div>
      <div class="foot-div">地址：北京市海淀区西三旗建材城西路31号B座四层西区</div>
    </footer>
  </div>
</template>
<script>
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      isSign: true,
      mailHeight: "2",
      userObj: {
        phone: "",
        code:"",
        address: [],
        sex: "男"
      },
      isUse: true,
      phoneHash: "",
      remainTime: 60, //倒计时60秒
      hasPsd:false,
    }
  },
  mounted() {
    this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
    this.terminal = IsPC() ? 'pc' : 'phone'
    if (this.isSign) {
      this.getMail()
    } else {
      this.$router.push("/frontPage")
    }

    this.fontSize = (window.innerWidth / 750) * 200;
    this.mailHeight = document.body.clientHeight / this.fontSize - 1.45 + 'rem';
  },
  beforeDestroy() {
    clearTimeout(this.timer);
  },
  methods: {
    getMail() {
      this.$http.get(baseUrlCommon + "/v1/frontend/user/get-user-info", {
        params: {
          userId: sessionStorage.userId,
          accessToken:sessionStorage.accessToken
        }
      })
      .then((res) => {
        if (res.data.status == 200) {
          this.userObj = res.data.data;
          this.hasPsd = res.data.data.setPwd==1?true:false;
          sessionStorage.personObj = JSON.stringify(this.userObj)
        }
      })
    },
    //获取手机验证码，开启倒计时
    phoneCode() {
      this.remainTime = 60;
      this.isUse = true;
      var myreg = /^1[34578][0-9]{9}/;
      if(myreg.test(this.userObj.phone)){
        if(JSON.parse(sessionStorage.personObj).phone == this.userObj.phone ){
           this.$Message.warning('手机并未改变')
        }else{
          this.isUse = false;
          this.remainMin()
          this.$http.get(baseUrlCommon+"/v1/user/verify-code", {
            params: {
              mobile:this.userObj.phone,
            }
          })
          .then((res) => {
            if (res.data.status == 200) {
              this.phoneHash = res.data.data.verify_hash;
            }else{
              this.isUse = true;
              this.timer?clearInterval(this.timer):""
              this.$Message.warning(res.data.message)
            }
          })
        }
      }else{
        this.isUse = true;
        this.$Message.error('请填写正确的电话号码')
      }
    },
    //更改手机号
    savePhone(){
      if(this.userObj.code&&this.userObj.code!=''){
        this.$http.post(baseUrlCommon+"/v1/frontend/user/bind-mobile", {
          mobile:this.userObj.phone ? this.userObj.phone : '',
          verify_hash:this.phoneHash,
          captcha:this.userObj.code ? this.userObj.code : '',
          userId: sessionStorage.userId,
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.phoneHash = '';
            if(this.hasPsd){
                this.$Message.success('手机成功绑定为'+this.userObj.phone)
                var _this = this
                setTimeout(function(){
                 _this.$router.push('/phoneData')
                },2000)
            }else{
              this.$Modal.confirm({
                title: '手机成功绑定为'+this.userObj.phone,
                content: '<p>请前往设置密码，以后可直接使用手机号和密码登录！</p>',
                onOk: () => {
                  this.$router.push('/psd')
                },
                onCancel: () => {
                  this.$router.push('/phoneData')
                }
              });
            }
          }else{
            this.isUse = true;
            this.timer?clearInterval(this.timer):""
            this.$Message.warning(res.data.message)
          }
        })
      }else{
        this.$Message.error('请填写正确的的验证码')
      }
    },
    //倒计时
    remainMin() {
      var _this = this;
      _this.timer = setInterval(function() {
        _this.remainTime--;
        if (_this.remainTime < 0) {
          _this.isUse = true;
          clearInterval(_this.timer)
        }
      }, 1000)
    },
    goPage(path) {
      this.$router.push(path)
    },
  },
}

</script>
<style lang='less'>
.phone {
  .set-mail {
    height: 0.4rem;
    color: white;
    background-color: #000;
    line-height: 0.4rem;
    padding: 0 3%
  }
  .set-mail span:nth-child(2) {
    margin-left: 30%;
    font-size: 0.16rem;
  }
  .set-mail span:nth-child(3) {
    float: right;
    color: #0fcf49;
  }

  .middle {
    width: 100%;
    padding-top: 0.15rem;
    overflow: hidden;
    background-color: #efefef;
  }

  .ivu-input {
    border-radius: 0;
    font-size: 0.14rem;
  }
}

</style>
