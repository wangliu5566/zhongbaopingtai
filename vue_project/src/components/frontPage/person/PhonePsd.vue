<template>
  <div class="phone-psd" v-if="terminal!='pc'">
    <div class="set-mail" style="position:relative;">
      <span @click="goPage('/phoneData')">取消</span>
      <span>{{hasPsd?'重置密码':"设置密码"}}</span>
    </div>
    <!-- 修改密码 -->
    <div :style="{minHeight:mailHeight}">
      <Form :model="psdFormDatas" :label-width="90" ref="editUserPsdForm" :rules="editUserPsdFormRules">
        <Form-item label="旧密码:" prop="oldpass" v-if="hasPsd">
          <Input type="password" v-model="psdFormDatas.oldpass" placeholder="请输入旧密码"></Input>
        </Form-item>
        <Form-item label="新密码:" prop="password">
          <Input type="password" v-model="psdFormDatas.password" placeholder="请输入新密码"></Input>
        </Form-item>
        <Form-item label="确认密码:" prop="repass" required>
          <Input type="password" v-model="psdFormDatas.repass" placeholder="请再次输入新密码"></Input>
        </Form-item>
      </Form>
      <div style="text-align: center;">
        <Button type="primary" @click="handleEditPsd('editUserPsdForm')">{{hasPsd?'重置密码':"设置密码"}}</Button>
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
    const chechPassword = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请再次输入新密码'));
      } else if (value !== this.psdFormDatas.password) {
        callback(new Error('两次输入密码不一致'));
      } else {
        callback();
      }
    };
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      isSign: true,
      hasPsd:false,
      //修改密码
      psdFormDatas: {
        oldpass: '',
        password: '',
        repass: '',
      },

       //修改密码验证规则
      editUserPsdFormRules: {
        oldpass: [{
          required: true,
          message: '旧密码不能为空',
          trigger: 'blur',
        }, {
          min: 6,
          max: 20,
          message: '密码在6-20个长度之间',
          trigger: 'blur',
        }],
        password: [{
          required: true,
          message: '新密码不能为空',
          trigger: 'blur',
        }, {
          min: 6,
          max: 20,
          message: '密码在6-20个长度之间',
          trigger: 'blur',
        }],
        repass: [{
          validator: chechPassword,
          trigger: 'blur',
        }]
      }
    }
  },
  mounted() {
    this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
    this.terminal = IsPC() ? 'pc' : 'phone'
    if (this.isSign) {
      this.getPerson()
    } else {
      this.$router.push("/frontPage")
    }

    this.fontSize = (window.innerWidth / 750) * 200;
    this.mailHeight = document.body.clientHeight / this.fontSize - 1.45 + 'rem';
  },
  methods: {
    getPerson() {
      this.$http.get(baseUrlCommon + "/v1/frontend/user/get-user-info", {
          params: {
            userId: sessionStorage.userId,
          }
        })
      .then((res) => {
        if (res.data.status == 200) {
           this.hasPsd = res.data.data.setPwd==1?true:false;
        }
      })
    },
    //确认修改密码
    handleEditPsd(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$http.post(baseUrlCommon+'/v1/frontend/user/reset-pwd',
            {
              oldPwd:this.psdFormDatas.oldpass,
              newPwd:this.psdFormDatas.password,
              rePwd:this.psdFormDatas.repass,
              userId:sessionStorage.userId,
            }
          ).then((res) => {
            if (res.data.status == 200) {
              this.$Message.success({
                content: '密码修改成功',
                duration: 3
              })
             this.$router.push('/phoneData')
            } else  {
              this.$Message.error({
                content: res.data.message,
                duration: 3
              });
              this.psdFormDatas.oldpass = '';
            }
          })
        }
      })
    },
    goPage(path) {
      this.$router.push(path)
    },
  },
}

</script>
<style lang='less'>
.phone-psd {
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

  .ivu-form{
    margin-top: 0.2rem;
  }


  .ivu-input {
    width: 95%;
    border-radius: 0;
    font-size: 0.14rem;
  }
}

</style>
