<template>
  <div class="account-edit-common">
    <Row class="pt10 pb10">
      <Button type="primary" icon="chevron-left" @click="goBackAccount">返回列表</Button>
    </Row>
    <Row class="pt20">
      <div class="form-container">
        <Form ref="accountAddFromHeader" :model="accountDatas" inline :label-width="100" :rules="accountRules">
          <Form-item label="用户名：" style="width:48%;" prop="username" required :error="usernameStatus">
            <Input type="text" :readonly="!isAdd" :disabled="!isAdd" v-model="accountDatas.username" placeholder="由数字、字母、“_”和“-”组成" @on-blur="checkUsername"></Input>
          </Form-item>
          <Form-item label="密码：" style="width:48%;" prop="password" v-show="isAdd">
            <Input type="password" v-model="accountDatas.password" placeholder="长度为6-16位"></Input>
          </Form-item>
          <Form-item label="" style="width:48%;" v-show="!isAdd">
          </Form-item>
          <Form-item label="真实姓名：" style="width:48%;" prop="realName">
            <Input type="text" :readonly="!isAdd" :disabled="!isAdd" v-model="accountDatas.realName" placeholder="请输入真实姓名"></Input>
          </Form-item>
          <Form-item label="确认密码：" style="width:48%;" prop="checkPsd" required v-show="isAdd">
            <Input type="password" v-model="accountDatas.checkPsd" placeholder="请再次输入密码"></Input>
          </Form-item>
          <Form-item label="" style="width:48%;" v-show="!isAdd">
          </Form-item>
          <Form-item label="机构：" style="width:48%;" prop="agency">
            <v-select v-model="accountDatas.agency" placeholder="请选择机构" :options="computedCompanyList"></v-select>
            <!-- <Select v-model="accountDatas.agencyId" label-in-value @on-change="setCompany">
              <Option v-for="(item,index) in companyList" :label="item.name" :value="item.id" :key="item.id+index"></Option>
            </Select> -->
          </Form-item>
          <Form-item label="部门：" style="width:48%;" prop="department">
            <v-select v-model="accountDatas.departments" placeholder="请选择部门" :options="['产品部','市场部','销售部']"></v-select>
            <!--             <Select v-model="accountDatas.department" placeholder="请选择部门">
              <Option value="产品部">产品部</Option>
              <Option value="市场部">市场部</Option>
              <Option value="销售部">销售部</Option>
            </Select> -->
          </Form-item>
          <Form-item label="角色：" style="width:96%;" prop="role">
            <Radio-group v-model="accountDatas.role">
              <Radio :label="item.name" v-for="(item,index) in roleList" :key="index">
                <span class="role-btn">{{item.name}}</span>
              </Radio>
            </Radio-group>
          </Form-item>
          <Form-item label="头像：" style="width:96%;">
            <Row>
              <Col style='height:120px;width:120px;'>
              <div v-if="userCover==''||userCover==null?true:false" class="without-user-cover">
                暂无头像
              </div>
              <img v-else :src="baseUrl+'/'+userCover" alt="" style="width:120px;height:120px;">
              </Col>
              <!-- <Col span="8" v-else style="height:120px;border:1px solid #dddee1;border-radius:4px;">
              <img :src="baseUrl+'/'+userCover" alt="">
              </Col> -->
            </Row>
          </Form-item>
          <Form-item label="上传头像：" style="width:96%;">
            <object id="FaustCplus" width="650" height="440" type="application/x-shockwave-flash" data="/static/js/FaustCplus.swf">
              <param name="menu" value="true">
              <param name="scale" value="noScale">
              <param name="allowFullscreen" value="true">
              <param name="allowScriptAccess" value="always">
              <param name="wmode" value="transparent">
              <param name="bgcolor" value="#FFFFFF">
              <!-- 正式 -->
              <param name="flashvars" value="jsfunc=uploadevent&amp;imgUrl=/static/img/default_photo.png&amp;pid=75642723&amp;uploadSrc=false&amp;showBrow=true&amp;showCame=true&amp;uploadUrl=http://crowdsourcing.jqweike.com/v1/admin/user/upload-image&amp;pSize=300|300|120|120|80|80|50|50">
              <!-- 测试 -->
              <!-- <param name="flashvars" value="jsfunc=uploadevent&amp;imgUrl=/static/img/default_photo.png&amp;pid=75642723&amp;uploadSrc=false&amp;showBrow=true&amp;showCame=true&amp;uploadUrl=http://crowdtest.jqweike.com/v1/admin/user/upload-image&amp;pSize=300|300|120|120|80|80|50|50"> -->
            </object>
          </Form-item>
          <Form-item class="tr pt20" style="width:98%;">
            <Button type="ghost" v-if="!isAdd" @click="cancelHandle">取消</Button>
            <Button type="primary" :disabled='isAdd&&hasAdd' @click="submitHandle('accountAddFromHeader')">确认{{isAdd?'新增':'修改'}}</Button>
          </Form-item>
        </Form>
      </div>
    </Row>
  </div>
</template>
<script>
export default {
  data() {
    return {
      isAdd: '',
      baseUrl: baseUrl,
      usernameStatus: '',
      hasAdd: false,

      userCover: '',

      coverTimer: '',
    }
  },
  props: [
    'accountDatas',
    'accountRules',
    'roleList',
    'companyList'
  ],
  methods: {

    setCoverMain() {
      swfobject.embedSWF("/static/js/FaustCplus.swf", "uploadUserCover", "650", "500", "10.0.0", "expressInstall.swf", this.flashvars, this.params, this.attributes)
    },

    checkIsAdd() {
      let index = this.$route.path.indexOf('/add');
      if (index != -1) {
        this.isAdd = true;
        let _this = this;
        window.sessionStorage.setItem('user-big-cover', '');
        window.sessionStorage.setItem('user-small-cover', '');
        this.coverTimer = window.setInterval(function() {
          console.log(1111)
          _this.userCover = window.sessionStorage.getItem('user-big-cover');
        }, 2000)
      } else {
        this.isAdd = false;
        let _this = this;
        this.coverTimer = window.setInterval(function() {
          console.log(1111)
          _this.userCover = window.sessionStorage.getItem('user-big-cover');
        }, 2000)
      }
    },
    goBackAccount() {
      this.$router.push({
        path: '/wrap/system/account'
      })
    },
    cancelHandle() {
      this.$router.push({
        path: '/wrap/system/account'
      })
    },
    submitHandle(formName) {
      switch (this.isAdd) {
        case true:
          this.addNewAccount(formName);
          break;
        case false:
          this.editAccount(formName);
          break;
        default:
          break;
      }
    },
    addNewAccount(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if (window.sessionStorage.getItem('user-big-cover') == '' || window.sessionStorage.getItem('user-big-cover') == null) {
            this.$Modal.confirm({
              title: '操作提示',
              content: '<h3>还没有为该用户上传头像，确认新建？</h3>',
              cancelText: '返回上传头像',
              okText: '确认新增',
              onOk: () => {
                this.submitAddAccountData();
              },
            });
          } else {
            this.submitAddAccountData();
          }
        }
      })
    },

    //新建角色交互
    submitAddAccountData() {
      this.$http.post('/v1/admin/user/create',
        Object.assign({}, this.accountDatas, {
          agencyId: this.accountDatas.agency.value,
          company: this.accountDatas.agency.label,
          department: this.accountDatas.departments,
          bigImage: window.sessionStorage.getItem('user-big-cover'),
          smallImage: window.sessionStorage.getItem('user-small-cover'),
        })
      ).then((res) => {
        if (res.data.status == 200) {
          this.$Message.success('用户新增成功');
          this.hasAdd = true;
          window.sessionStorage.setItem('user-big-cover', '');
          window.sessionStorage.setItem('user-small-cover', '');
          this.$router.push('/wrap/system/account');

          let sessionFilter = JSON.parse(window.sessionStorage.getItem('bg_user_filter'));
          if (sessionFilter != null && sessionFilter != {}) {
            window.sessionStorage.setItem('bg_user_filter', JSON.stringify(Object.assign({}, sessionFilter, {
              cp: 1
            })))
          } 
        }
      })
    },

    editAccount(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          let sendDatas = {};
          let bigCover = window.sessionStorage.getItem('user-big-cover');
          let smallCover = window.sessionStorage.getItem('user-small-cover');
          if (typeof(this.accountDatas.agency) == 'string') {
            if (bigCover != '' && smallCover != '') {
              sendDatas = Object.assign({}, this.accountDatas, {
                userId: this.accountDatas.id,
                bigImage: bigCover,
                smallImage: smallCover
              })
            } else {
              sendDatas = Object.assign({}, this.accountDatas, {
                userId: this.accountDatas.id
              })
            }
          } else {
            if (bigCover != '' && smallCover != '') {
              sendDatas = Object.assign({}, this.accountDatas, {
                userId: this.accountDatas.id,
                bigImage: bigCover,
                smallImage: smallCover,
                agencyId: this.accountDatas.agency.value,
                company: this.accountDatas.agency.label,
                department: this.accountDatas.departments,
              })
            } else {
              sendDatas = Object.assign({}, this.accountDatas, {
                userId: this.accountDatas.id,
                agencyId: this.accountDatas.agency.value,
                company: this.accountDatas.agency.label,
                department: this.accountDatas.departments,
              })
            }
          };
          this.$http.post('/v1/admin/user/create',
            sendDatas
          ).then((res) => {
            if (res.data.status == 200) {
              this.$Message.success('用户修改成功');
              this.$router.push('/wrap/system/account')
              // this.hasAdd = true;
            }
          })
        }
      })
    },


    //验证用户名
    checkUsername() {
      if (this.accountDatas.username && this.isAdd) {
        let _this = this;
        this.$refs.accountAddFromHeader.validateField('username', function(valid) {
          if (valid == '') {
            _this.$http.post('/v1/admin/user/verify-username', {
              username: _this.accountDatas.username,
            }).then((res) => {
              if (res.data.status == 1006) {
                _this.accountDatas.username = '';
                _this.usernameStatus = res.data.message;
              } else if (res.data.status == 200) {
                _this.$Message.success('该用户名可用')
              }
            })
          }
        })

      }
    },

    //返回机构名
    setCompany(item) {
      this.accountDatas.company = item.label;
    },

  },
  mounted() {
    this.checkIsAdd();

  },
  beforeDestroy() {
    window.clearInterval(this.coverTimer);
    window.sessionStorage.setItem('user-big-cover', '');
    window.sessionStorage.setItem('user-small-cover', '');
  },

  computed: {
    'computedCompanyList': function() {
      let newCompanyList = [];
      this.companyList.forEach((item, index) => {
        newCompanyList.push({
          value: item.id,
          label: item.name
        })
      });
      return newCompanyList;
    },
  }
}

</script>
<style lang="less">
.account-edit-common {
  .form-container {
    width: 700px;
    /*    margin: 0 auto; */
  }
  .role-btn {
    display: inline-block;
    background-color: #eee;
    padding: 0px 20px;
    margin-bottom: 10px;
    border-radius: 4px;
  }
  .ivu-form-item-error {
    .v-select .dropdown-toggle {
      border: 1px solid red;
    }
  }
  .ivu-select-visible {
    .v-select .dropdown-toggle {
      border-color: #57a3f3;
      outline: 0;
      box-shadow: 0 0 0 2px rgba(45, 140, 240, .2);
    }
  }
  .v-select input[type=search] {
    height: 30px;
  }
  .v-select .dropdown-toggle {
    overflow: hidden;
    height: 32px;
    .form-control {
      float: left;
    }
    .open-indicator {
      bottom: 3px;
    }
    .selected-tag {
      text-overflow: ellipsis;
      white-space: nowrap;
      overflow: hidden;
      max-width: 90%;
      margin: 2px 1px 0 2px;
      border: none;
      background-color: #fff;
      float: left;
    }
  }
  .v-select li>a {
    padding: 3px 5px;
  }

  .without-user-cover {
    height: 120px;
    border: 1px solid #dddee1;
    color: #bbb;
    font-size: 16px;
    border-radius: 4px;
    line-height: 120px;
    text-align: center;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    overflow: hidden;
  }
}

</style>
