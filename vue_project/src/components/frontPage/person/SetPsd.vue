<template>
  <div class="set-psd" v-if="terminal=='pc'">
    <h3>{{hasPsd?'重置密码':"设置密码"}}</h3>

     <!-- 修改密码 -->
    <Form :model="psdFormDatas" :label-width="80" ref="editUserPsdForm" :rules="editUserPsdFormRules">
      <Form-item label="旧密码" prop="oldpass" v-if="hasPsd">
        <Input type="password" v-model="psdFormDatas.oldpass" placeholder="请输入旧密码"></Input>
      </Form-item>
      <Form-item label="新密码" prop="password">
        <Input type="password" v-model="psdFormDatas.password" placeholder="请输入新密码"></Input>
      </Form-item>
      <Form-item label="确认密码" prop="repass" required>
        <Input type="password" v-model="psdFormDatas.repass" placeholder="请再次输入新密码"></Input>
      </Form-item>
    </Form>
    <div style="text-align: center;">
      <Button type="primary" @click="handleEditPsd('editUserPsdForm')">{{hasPsd?'重置密码':"设置密码"}}</Button>
    </div>

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
    if (this.isSign && this.terminal == 'pc') {
     this.getPerson()
    } else if (this.isSign && this.terminal == 'phone') {
      this.$router.push('/phoneData')
    } else {
      this.$router.push("/frontPage")
    }
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
              this.getPerson()
              this.$Message.success({
                content: '密码修改成功',
                duration: 3
              })
              this.psdFormDatas.oldpass ='' 
              this.psdFormDatas.password ='' 
              this.psdFormDatas.repass ='' 
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
    
  },
  watch: {
    modalImg: {
      handler(curVal, oldVal) {
        if (!curVal) { //展示头像的模态框一旦关闭就重新请求数据
          this.$router.go(0)
        }　　　　　　
      }
    }
  }
}

</script>
<style lang='less'>
.set-psd{
  padding: 20px;
  overflow: hidden;

  h3{
    line-height: 50px;
    text-align: center;
    font-size: 20px;
  }

  .ivu-form{
    margin-top: 20px;
    width: 60%;
    margin-left: 20%;
  }
 
}


</style>
