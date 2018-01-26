<template>
  <div class="account-add">
    <AccountEditCommon :account-datas="newAccountDatas" :account-rules='newAccountDatasRules' :role-list='roleList' :company-list="companyList"></AccountEditCommon>
  </div>
</template>
<script>
import AccountEditCommon from '@/components/bgSystem/AccountEditCommon'
export default {
  data() {
    const CheckUsername = (role, value, callback) => {
      let usernameReg = /^([a-zA-Z0-9-_])+$/;
      if (value === '') {
        callback(new Error('请输入用户名'))
      } else if (!usernameReg.test(value)) {
        callback(new Error('由数字、字母、“_”和“-”组成'))
      } else {
        callback();
      }
    };
    const CheckPsd = (role, value, callback) => {
      if (value === '') {
        callback(new Error('请再次输入密码'))
      } else if (value !== this.newAccountDatas.password) {
        callback(new Error('两次输入的密码不一致'))
      } else {
        callback();
      }
    };
    const CheckAgency = (role, value, callback) => {
      if (value === '' || (value.id == '' && value.name == '')) {
        callback(new Error('请选择机构'))
      } else {
        callback();
      }
    };
    const CheckRealName = (role, value, callback) => {
      let realNameReg = /^[\u4E00-\u9FA5]{2,4}(?:·[\u4E00-\u9FA5]{2,5})*$/;
      if (value === '') {
        callback(new Error('请输入真实姓名'))
      } else if (!realNameReg.test(value)) {
        callback(new Error('真实姓名格式不正确'))
      } else {
        callback()
      }
    };
    const CheckPassword = (role, value, callback) => {
      if (value === '') {
        callback(new Error('密码不能为空'))
      } else {
        callback();
      }
    };
    return {
      //新增数据
      newAccountDatas: {
        username: '',
        password: '',
        realName: '',
        checkPsd: '',
        agency: '',
        agencyId: '',
        company: '',
        departments: '产品部',
        department: '',
        role: '',
        bigImage: '',
        smallImage: '',
      },
      newAccountDatasRules: {
        username: [{
          // required: true,
          // message: '请输入用户名',
          validator: CheckUsername,
          trigger: 'blur'
        }, {
          min: 2,
          max: 10,
          message: '用户名长度在2-10位之间',
          trigger: 'blur'
        }],
        password: [{
          validator: CheckPassword,
          trigger: 'blur'
        }, {
          min: 6,
          max: 16,
          message: '密码长度在6-16位之间',
          trigger: 'blur',
        }],
        realName: [{
          required: true,
          validator: CheckRealName,
          trigger: 'blur'
        }, {
          min: 2,
          message: '长度不得少于2位',
          trigger: 'blur',
        }],
        checkPsd: [{
          validator: CheckPsd,
          trigger: 'blur',
        }],
        agency: [{
          validator: CheckAgency,
          // required: true,
          // message: '请选择机构名称',
          trigger: 'change'
        }],
        departments: [{
          required: true,
          message: '请选择部门',
          trigger: 'change'
        }],
        role: [{
          required: true,
          message: '请选择角色',
          trigger: 'change'
        }],



      },

      roleList: [],
      companyList: []
    }
  },
  methods: {
    //获取角色列表
    getRoleList() {
      this.$http.get('/v1/admin/permission/get-all-roles',{
        params:{}
      })
        .then((res) => {
          this.roleList = res.data.data;
          this.newAccountDatas.role = res.data.data[0].name;
        })
    },
    //获取公司列表
    getCompanyList() {
      this.$http.get('/v1/admin/user/agency-list',{
        params:{}
      })
        .then((res) => {
          this.companyList = res.data.data.data;
          // this.newAccountDatas.agency = this.companyList[0];
        })
    },

  },
  beforeRouteLeave(to, from, next) {
    window.sessionStorage.setItem('user-big-cover', '');
    window.sessionStorage.setItem('user-small-cover', '');
    next();
  },
  components: {
    AccountEditCommon
  },
  mounted() {
    this.getCompanyList();
    this.getRoleList();
  }
}

</script>
<style lang="less">
.account-add {}

</style>
