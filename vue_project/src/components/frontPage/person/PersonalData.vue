<template>
  <div class="personal" v-if="terminal=='pc'">
    <div class="show-head">
      <img :src="url" @click="updateImg">
      <div class="update" @click="updatePerson">修改资料</div>
    </div>
    <ul class="person-message">
      <li>账号：<span>{{userObj.username}}</span></li>
      <li>电话：
        <span>{{userObj.phone?userObj.phone:'暂无'}}</span>
        <span class="click-update" @click="openPhone">{{userObj.phone?'修改电话':'绑定电话'}}</span>
      </li>
      <li>邮箱：
        <span>{{userObj.email?userObj.email:'暂无'}}</span>
        <span class="click-update" @click="openEmail">{{userObj.email?'修改邮箱':'绑定邮箱'}}</span>
      </li>
      <li>城市：<span>{{userObj.address[0]?userObj.address[0]+'-'+userObj.address[1]:'暂无'}}</span></li>
      <li>性别：<span>{{userObj.sex?userObj.sex:'保密'}}</span></li>
    </ul>

    <!-- 修改资料 -->
    <Modal v-model="updateModal" width="440" id="pcUpdate" :closable="false" :mask-closable="false" title="修改资料">
      <Form :model="userObj" :label-width="80" ref="formValidate" :rules="ruleValidate">
        <FormItem label="城市：">
          <Cascader v-model="userObj.address" :data="cityList" :load-data="loadData" @on-change="handleChange"></Cascader>
        </FormItem>
        <FormItem label="性别：">
          <Select v-model="userObj.sex" placeholder="请选择">
            <Option value="男">男</Option>
            <Option value="女">女</Option>
            <Option value="保密">保密</Option>
          </Select>
        </FormItem>
      </Form>
      <div slot="footer">
        <button @click="closeModal">取消</button>
        <button @click="handleSubmit('formValidate')">保存</button>
      </div>
    </Modal>
    
    <!-- 绑定电话 -->
     <Modal v-model="updatePhone" width="440" id="pcUpdate" :closable="false" :mask-closable="false" title="绑定电话">
      <Form :model="updateObj" :label-width="80" ref="formValidate" :rules="ruleValidate">
        <FormItem label="电话：" prop="phone">
          <Input v-model="updateObj.phone" placeholder="请输入电话"></Input>
        </FormItem>
        <FormItem label="验证码：" prop="code">
          <Input v-model="updateObj.code" placeholder="请输入验证码" style="width:68%"></Input>
          <Button v-if="isUse" type="primary" @click="phoneCode">获取验证码</Button>
          <Button v-if="!isUse" type="primary" disabled>{{remainTime}}s</Button>
        </FormItem>
      </Form>
      <div slot="footer">
        <button @click="closePhone">取消</button>
        <button @click="savePhone()">保存</button>
      </div>
    </Modal>

     <!-- 绑定邮箱 -->
     <Modal v-model="updateEmail" width="440" id="pcUpdate" :closable="false" :mask-closable="false" title="绑定邮箱">
      <Form :model="updateObj" :label-width="80" ref="formValidate" :rules="ruleValidate">
        <FormItem label="邮箱：" prop="email">
          <Input v-model="updateObj.email" placeholder="请输入邮箱"></Input>
        </FormItem>
        <FormItem label="验证码：" prop="code">
          <Input v-model="updateObj.code" placeholder="请输入验证码" style="width:68%"></Input>
          <Button v-if="isUse" type="primary" @click="emailCode()">获取验证码</Button>
          <Button v-if="!isUse" type="primary" disabled>{{remainTime}}s</Button>
        </FormItem>
      </Form>
      <div slot="footer">
        <button @click="closeEmail">取消</button>
        <button @click="saveEmail">保存</button>
      </div>
    </Modal>

    <!-- 修改头像 -->
    <Modal v-model="modalImg" width="700" id="imgUpdate" :mask-closable="false">
      <object id="FaustCplus" width="650" height="440" type="application/x-shockwave-flash" data="/static/js/FaustCplus.swf">
        <param name="menu" value="true">
        <param name="scale" value="noScale">
        <param name="allowFullscreen" value="true">
        <param name="allowScriptAccess" value="always">
        <param name="wmode" value="transparent">
        <param name="bgcolor" value="#FFFFFF">
        <param name="flashvars" value="jsfunc=uploadImg&amp;imgUrl=/static/img/default_photo.png&amp;pid=75642723&amp;uploadSrc=false&amp;showBrow=true&amp;showCame=true&amp;uploadUrl=http://share.jqweike.com/v1/frontend/user/upload-image&amp;pSize=300|300|120|120|80|80|50|50">
      </object>
      <div slot="footer"></div>
    </Modal>
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
      url: "",
      userObj: {
        phone: "",
        email: "",
        address: [],
        sex: "男"
      },
      updateObj:{
        phone: "",
        email: "",
        code:""
      },
      isUse: true,
      remainTime:60,  //倒计时60秒
      updateModal: false,
      updatePhone:false,
      updateEmail:false,
      phoneHash:"",
      emailHash:"",
      cityList: [],
      ruleValidate: {
        email: [
          // { required: true, message: '邮箱不能为空', trigger: 'blur' },
          { type: 'email', message: '邮箱格式不正确', trigger: 'blur' }
        ],
      },
      modalImg: false,
      hasPsd:false,
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
  beforeDestroy() {
    clearTimeout(this.timer);
  },
  methods: {
    goIndex(index, path) {
      this.liIndex = index;
      this.$router.push(path)
    },
    getPerson() {
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
          if (res.data.data.bigAvatar) {
            this.url = res.data.data.bigAvatar
          } else if (res.data.data.middleAvatar) {
            this.url = res.data.data.middleAvatar
          } else {
            this.url = "../../../../static/img/default_photo.png";
          }
        }
      })
    },
    //修改个人资料模态框
    updatePerson() {
      this.updateModal = true;
      this.getProvice()
    },
    //打开手机模态框
    openPhone(){
      this.updatePhone = true;
      this.updateObj.phone = this.userObj.phone
    },
    //获取手机验证码，开启倒计时
    phoneCode() {
      this.remainTime = 60;
      this.isUse = true;
      var myreg = /^1[34578][0-9]{9}/;
      if(myreg.test(this.updateObj.phone)){
        this.isUse = false;
        this.remainMin()
        this.$http.get(baseUrlCommon+"/v1/user/verify-code", {
          params: {
            mobile:this.updateObj.phone,
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
      }else{
        this.isUse = true;
        this.$Message.error('请填写正确的电话号码');
      }
    },
    //更改手机号
    savePhone(){
      if(this.updateObj.code&&this.updateObj.code!=''){
        this.$http.post(baseUrlCommon+"/v1/frontend/user/bind-mobile", {
          mobile:this.updateObj.phone,
          verify_hash:this.phoneHash,
          captcha:this.updateObj.code,
          userId: sessionStorage.userId,
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.phoneHash = '';
            this.updateObj.code = '';
            this.timer?clearInterval(this.timer):""
            if(this.hasPsd){
                this.$Message.success('手机成功绑定为'+this.updateObj.phone)
                var _this = this
                setTimeout(function(){
                  _this.getPerson()
                  _this.closePhone();
                },2000)
            }else{
              this.$Modal.confirm({
                title: '手机成功绑定为'+this.updateObj.phone,
                content: '<p>请前往设置密码，以后可直接使用手机号和密码登录！</p>',
                onOk: () => {
                  this.$router.push('/frontPage/person/setPsd')
                },
                onCancel: () => {
                  this.timer?clearInterval(this.timer):""
                  this.getPerson()
                  this.closePhone();
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
        this.$Message.error('请填写正确的的验证码');
      }
    },
    closePhone(){
      this.updatePhone = false
      this.isUse = true;
      this.remainTime = 60;
      this.getPerson()
    },
    //打开邮箱模态框
    openEmail(){
      this.updateEmail = true;
      this.updateObj.email = this.userObj.email;
    },
    //邮箱验证码
    emailCode(){
      this.remainTime = 60;
      this.isUse = true;
      var reg = /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/
      if(reg.test(this.updateObj.email)){
        this.isUse = false;
        this.remainMin()
        this.$http.get(baseUrlCommon+"/v1/frontend/user/send-code-email", {
          params: {
            email:this.updateObj.email,
            userId:sessionStorage.userId
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.emailHash = res.data.data.verify_hash;
            this.$Message.success(res.data.message)
          }else{
            this.isUse = true;
            this.timer?clearInterval(this.timer):""
            this.$Message.warning(res.data.message)
          }
        })
      }else{
        this.isUse = true;
        this.$Message.error('请填写正确的邮箱格式')
      }
    },
     //更改绑定邮箱
    saveEmail(){
      if(this.updateObj.code&&this.updateObj.code!=''){
        this.$http.post(baseUrlCommon+"/v1/frontend/user/bind-email-by-code", {
          email:this.updateObj.email,
          verify_hash:this.emailHash,
          captcha:this.updateObj.code,
          userId: sessionStorage.userId,
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.emailHash = '';
            this.updateObj.code = '';
            this.timer?clearInterval(this.timer):""
            if(this.hasPsd){
              this.$Message.success('邮箱成功绑定为'+this.updateObj.email)
              var _this = this
              setTimeout(function(){
                _this.getPerson()
                _this.closeEmail();
              },2000)
            }else{
              this.$Modal.confirm({
                title: '邮箱成功绑定为'+this.updateObj.email,
                content: '<p>请前往设置密码，以后可直接使用邮箱密码登录！</p>',
                onOk: () => {
                  this.$router.push('/frontPage/person/setPsd')
                },
                onCancel: () => {
                  this.getPerson()
                  this.closeEmail();
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
    closeEmail(){
      this.updateEmail = false;
      this.isUse = true;
      this.remainTime = 60;
      this.getPerson()
    },
    //倒计时
    remainMin(){
      var _this = this;
      _this.timer = setInterval(function(){
        _this.remainTime--;
        if(_this.remainTime<0){
           _this.isUse = true;
           clearInterval(_this.timer)
        }
      },1000)
    },
    getProvice() {
      this.$http.get("/v1/admin/area/province", {})
      .then((res) => {
        if (res.data.status == 200) {
          this.cityList = res.data.data;
        }
      })
    },
    //请求城市数据
    loadData(item, callback) {
      item.loading = true;
      setTimeout(() => {
        this.$http.get("/v1/admin/area/city", {
            params: {
              code: item.id,
            }
          })
          .then((res) => {
            if (res.data.status == 200) {
              item.children = res.data.data;

            }
          })
        item.loading = false;
        callback();
      }, 1000);
    },
    handleChange(value, selectedData) {
      this.userObj.address = [];
      var arr = selectedData.map(o => o.label).join(', ');
      this.userObj.address = arr.split(",")
    },
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.updataDatas()
        } else {
          this.$Message.error({
            content: '请填写正确的邮箱格式',
            duration: 3,
          });
        }
      })
    },
    updataDatas() {
      var itemObj = JSON.parse(sessionStorage.personObj)
      if (itemObj.sex == this.userObj.sex && itemObj.address[0] == this.userObj.address[0] && itemObj.address[1] == this.userObj.address[1]) {
        this.$Message.error({
          content: '您还未修改过任何数据',
          duration: 3,
        });
      } else {
        this.$http.post(baseUrlCommon + "/v1/frontend/user/update-user-info", {
            userId: sessionStorage.userId,
            province: this.userObj.address[0] ? this.userObj.address[0] : "",
            city: this.userObj.address[1] ? this.userObj.address[1] : "",
            sex: this.userObj.sex,
          })
          .then((res) => {
            if (res.data.status == 200) {
              this.$Message.success({
                content: '保存成功',
                duration: 3,
              })
              var _this = this
              setTimeout(function() {
                _this.getPerson()
                _this.updateModal = false
              }, 2000)
            }
          })
          .catch((err) => {
            console.log(err)
          })
      }
    },
    closeModal() {
      this.updateModal = false;
      this.getPerson()
    },
    updateImg() {
      this.modalImg = true;
    }
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
.personal {

  .show-head {
    height: 220px;
    position: relative;

    img {
      width: 140px;
      height: 140px;
      border-radius: 70px;
      margin: 43px 0 0 385px;
    }
  }
  .update {
    width: 100px;
    height: 30px;
    line-height: 30px;
    position: absolute;
    right: 10px;
    top: 40px;
    font-size: 16px;
    cursor: pointer;
    color: #0097d6;
  }

  .person-message li {
    height: 57px;
    border-bottom: 1px solid #e4e4e4;
    width: 838px;
    margin-left: 50px;
    line-height: 57px;
    font-size: 16px;
  }

  .click-update{
    cursor: pointer;
    margin-left: 80px;
    color:#0097d6;
    font-size: 14px;
  }
}

#pcUpdate {
  .ivu-form-item {
    margin-bottom: 20px;
  }
  .ivu-modal-body {
    padding-right: 40px;
  }

  
  .ivu-modal-footer {
    padding-top: 0!important;
    padding-right: 40px!important;

    button {
      width: 90px;
      height: 34px;
      border: 1px solid #a9a9a9;
      outline: none;
      background-color: #fff;
      font-size: 15px;
      line-height: 34px;
      text-align: center;
      cursor: pointer;
    }
    button:nth-child(2) {
      border-color: #0197d6;
      background-color: #0197d6;
      color: white;
    }
  }
}

#imgUpdate {}

</style>
