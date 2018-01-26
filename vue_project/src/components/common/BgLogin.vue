<template>
  <div class="bg-system-login">
    <div class="login-container" v-if="loadLoginForm">
      <h3 class='title'>桥帮主后台管理系统</h3>
      <span class='sub-title'>用户登录</span>
      <Form ref="bgSystemLoginForm" :model="loginData" :rules="bgSystemLoginRules">
        <Form-item prop="username">
          <Input type="text" v-model="loginData.username" placeholder="用户名" @on-change="isChange = true;" @on-enter="handleSubmitLogin('bgSystemLoginForm')">
          <Icon type="ios-person" slot="prepend" size="18" style="padding:0 2px;color:#97a8be;"></Icon>
          </Input>
        </Form-item>
        <Form-item prop="password">
          <Input type="password" v-model="loginData.password" placeholder="密码" @on-change="isChange = true" @on-enter="handleSubmitLogin('bgSystemLoginForm')">
          <Icon type="ios-unlocked" slot="prepend" size="18" style="padding:0 2px;color:#97a8be;"></Icon>
          </Input>
        </Form-item>
        <Form-item>
          <Row>
            <Col span="18">
            <Tooltip content="30天内记住密码？" placement="bottom">
              <i-switch v-model="loginData.rememberMe" @on-change="rememberChange">
                <span slot="open">开</span>
                <span slot="close">关</span>
              </i-switch>
            </Tooltip>
            </Col>
            <Col span="6">
            <Button class="pull-right" long :loading="submitLoading" type="primary" @click="handleSubmitLogin('bgSystemLoginForm')">
              <span v-if="!submitLoading">登录</span>
              <span v-else>登录中</span>
            </Button>
            </Col>
          </Row>
        </Form-item>
      </Form>
    </div>
  </div>
</template>
<script>
import sha1 from 'crypto-js/sha1';
export default {
  data() {
    return {
      loginData: {
        username: window.sessionStorage.getItem('bg_user_info') && JSON.parse(window.sessionStorage.getItem('bg_user_info')).username ? JSON.parse(window.sessionStorage.getItem('bg_user_info')).username : window.localStorage.getItem('bg_user_username') ? window.localStorage.getItem('bg_user_username') : '',
        password: '',
        rememberMe: window.sessionStorage.getItem('bg_user_info') && JSON.parse(window.sessionStorage.getItem('bg_user_info')).rememberMe ? JSON.parse(window.sessionStorage.getItem('bg_user_info')).rememberMe : window.localStorage.getItem('bg_user_rememberMe') == "true" ? true : false,
      },

      loadLoginForm: false,

      //用户名或者密码是否更改过
      isChange: false,

      bgSystemLoginRules: {
        username: [{
          required: true,
          message: '请填写用户名',
          trigger: 'blur'
        }],
        password: [{
          required: true,
          message: '请填写密码',
          trigger: 'blur'
        }]
      },

      submitLoading: false,

    }
  },
  methods: {
    handleSubmitLogin(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if ((window.sessionStorage.getItem('bg_user_info') && JSON.parse(window.sessionStorage.getItem('bg_user_info')).rememberMe && !this.isChange) || (window.localStorage.getItem('bg_user_rememberMe')) && !this.isChange) {
            if (this.$cookie.get('bg_user_access_token')) {
              this.submitLoading = true;
              this.$http.post('/v1/admin/site/login',
                Object.assign({}, this.loginData, {
                  access_token: this.$cookie.get('bg_user_access_token')
                })
              ).then((res) => {
                this.submitLoading = false;
                if (res.data.status == 200) {
                  window.sessionStorage.setItem('bg_user_info', JSON.stringify({
                    username: this.loginData.username,
                    access_token: res.data.data.access_token,
                    agency: res.data.data.agency,
                    agencyId: res.data.data.agencyId,
                    realName: res.data.data.realName,
                    id: res.data.data.id,
                    rememberMe: this.loginData.rememberMe,
                    password: this.$cookie.get('bg_user_psd')
                  }));
                  window.sessionStorage.setItem('isSuper', sha1(res.data.data.isAdministrator).toString())
                  window.localStorage.setItem('bg_user_username', this.loginData.username);
                  window.localStorage.setItem('bg_user_rememberMe', this.loginData.rememberMe);
                  this.$Message.success('欢迎登录桥帮主平台后台管理系统');
                  window.sessionStorage.setItem('bg_user_permission', res.data.data.permission.join(','));
                  this.$router.push({
                    path: '/wrap',
                    query: {}
                  })
                } else if (res.data.status == 1005) {
                  this.$Message.error(res.data.message);
                } else if (res.data.status == 1004) {
                  this.$Message.error(res.data.message);
                  window.sessionStorage.setItem('bg_user_info', JSON.stringify({
                    username: '',
                    access_token: '',
                    agency: '',
                    agencyId: '',
                    realName: '',
                    id: '',
                    rememberMe: false,
                    password: ''
                  }));
                  this.loginData = {
                    username: '',
                    password: '',
                    rememberMe: false,
                  };
                  window.localStorage.setItem('bg_user_username', '');
                  window.localStorage.setItem('bg_user_rememberMe', false);
                }
              })
            }
          } else {
            this.submitLoading = true;
            this.$http.post('/v1/admin/site/login', {
                username: this.loginData.username,
                password: this.loginData.password
              })
              .then((res) => {
                this.submitLoading = false;
                switch (parseInt(res.data.status)) {
                  case 200:
                    if (this.loginData.rememberMe) {
                      let sha1Psd = sha1(res.data.data.password);
                      this.$cookie.set('bg_user_psd', sha1Psd, { expires: 30 });
                      this.$cookie.set('bg_user_access_token', res.data.data.access_token, { expires: 30 });
                    }
                    window.sessionStorage.setItem('bg_user_info', JSON.stringify({
                      username: this.loginData.username,
                      access_token: res.data.data.access_token,
                      agency: res.data.data.agency,
                      agencyId: res.data.data.agencyId,
                      realName: res.data.data.realName,
                      id: res.data.data.id,
                      rememberMe: this.loginData.rememberMe,
                      password: sha1(res.data.data.password).toString()
                    }));

                    window.sessionStorage.setItem('isSuper', sha1(res.data.data.isAdministrator).toString())
                    window.localStorage.setItem('bg_user_username', this.loginData.username);
                    window.localStorage.setItem('bg_user_rememberMe', this.loginData.rememberMe);
                    window.localStorage.setItem('bg_user_isOverdue', false);
                    window.sessionStorage.setItem('bg_user_permission', res.data.data.permission.join(','));
                    this.$Message.success('欢迎登录桥帮主平台后台管理系统');
                    this.$router.push({
                      path: '/wrap',
                      query: {}
                    })
                    break;
                  case 1005:
                    this.$Message.error(res.data.message)
                    break;
                  case 1004:
                    this.$Message.error(res.data.message);
                    window.sessionStorage.setItem('bg_user_info', JSON.stringify({
                      username: '',
                      access_token: '',
                      agency: '',
                      agencyId: '',
                      realName: '',
                      id: '',
                      rememberMe: false
                    }));
                    this.loginData = {
                      username: '',
                      password: '',
                      rememberMe: false,
                    };
                    window.localStorage.setItem('bg_user_username', '');
                    window.localStorage.setItem('bg_user_rememberMe', false);
                    break;
                  default:
                    // statements_def
                    break;
                }

              })
          }

        }
      })
    },
    //初始化用户名
    initPsd() {
      if ((window.sessionStorage.getItem('bg_user_info') && JSON.parse(window.sessionStorage.getItem('bg_user_info')).username && JSON.parse(window.sessionStorage.getItem('bg_user_info')).rememberMe) || (window.localStorage.getItem('bg_user_username') && window.localStorage.getItem('bg_user_rememberMe')) != "false") {
        let lastPsd = this.$cookie.get('bg_user_psd');
        if (lastPsd && !this.isChange) {
          if (this.loginData.username) {
            this.loginData.password = lastPsd.slice(0, 18);
          }
        } else {
          if (window.localStorage.getItem('bg_user_isOverdue') == "false") {
            this.$Message.error({
              content: '您记录的登录有效时间已过期，请重新输入密码',
              duration: 4
            });
            window.localStorage.setItem('bg_user_isOverdue', true);
          }
          window.sessionStorage.setItem('bg_user_info', JSON.stringify(Object.assign({}, JSON.parse(window.sessionStorage.getItem('bg_user_info')), {
            rememberMe: false,
            access_token: '',
          })));
          this.loginData.rememberMe = false;
        }
      }

    },

    rememberChange(value) {
      if (window.sessionStorage.getItem('bg_user_info') && JSON.parse(window.sessionStorage.getItem('bg_user_info')).username && JSON.parse(window.sessionStorage.getItem('bg_user_info')).rememberMe && !this.isChange) {
        if (!value) {
          this.loginData.password = '';
        }
      }
    },
  },
  watch: {
    'loginData.username': function(val, oldVal) {
      if (!val && oldVal) {
        this.loginData.password = '';
      } else if (val.length == 1) {
        this.loginData.password = '';
      }
    }
  },
  created() {
    setTimeout(() => {
      this.loadLoginForm = true;
    }, 0);
    this.initPsd();
    window.sessionStorage.setItem('bg_system_menu', JSON.stringify([]));
    var _this = this;
    window.onkeydown = function(e) {
      if (e.keyCode == 13) {
        _this.handleSubmitLogin('bgSystemLoginForm');
      }
    };

  }
}

</script>
<style lang="less">
.bg-system-login {
  width: 100%;
  height: 100%;
  position: relative;
  background-color: #e9ecf3;

  .login-container {
    width: 400px;
    height: 300px;
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    background-color: #fff;
    box-shadow: 0 0 8px 0 rgba(0, 0, 0, .06), 0 1px 0 0 rgba(0, 0, 0, .02);
    border-radius: 5px;
    -moz-border-radius: 5px;
    background-clip: padding-box;
    border: 2px solid #e7ecf1;
    padding: 35px 35px 45px;

    .title {
      font-weight: normal;
      font-size: 16px;
      text-align: center;
    }
    .sub-title {
      font-size: 16px;
      font-weight: bold;
      display: block;
      text-align: center;
      color: #505458;
      margin: 10px 0 20px;
    }
  }
}

</style>
