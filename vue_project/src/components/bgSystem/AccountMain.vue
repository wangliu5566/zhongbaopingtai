<template>
  <div class="account-main">
    <Row>
      <Col span="8">
      <Button type="primary" @click="addNewAccount" v-if="permissionList.indexOf('user/create')!=-1?true:false">
        <Icon type="plus-round"></Icon> 添加</Button>
      <Button type="error" @click="deleteUser" v-if="permissionList.indexOf('user/delete')!=-1?true:false">
        <Icon type="close-round"></Icon> 批量删除</Button>
      &nbsp;
      </Col>
      <Col span="8" class="pr5">
      <Select v-model="company" placeholder='默认全部,支持检索' style="width: 60%;" class="pull-right" @on-change="companyChange" filterable remote :remote-method="remoteMethod" :loading="loading" clearable not-found-text="">
        <Option v-for="(item,index) in companyListFilter" :value="item.name" :key="item.id+index">{{item.name}}</Option>
      </Select>
      <span class="pull-right pt5">所属机构：</span>
      </Col>
      <Col span="8">
      <Input v-model="searchKey" icon="ios-search-strong" placeholder="搜索用户名" @on-click="searchInfo"></Input>
      </Col>
    </Row>
    <Row>
      <Spin fix v-show="loadingData">
        <Icon type="load-c" size=18 class="demo-spin-icon-load"></Icon>
        <div>数据加载中...</div>
      </Spin>
      <Row v-show="!loadingData" class="pt10 pb10">
        <Table :columns="accountColumns" :data="accountDatas" @on-select="tableSelectCurrent" @on-select-all='tableSelectAll' @on-select-cancel="tableCancelCurrent" @on-selection-change="tableChange" highlight-row></Table>
      </Row>
    </Row>
    <Row class="tr pt5 pb10">
      <Page :total="tp" :current='cp' :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
    </Row>
  </div>
</template>
<script>
export default {
  data() {
    const filters = window.sessionStorage.getItem('bg_user_filter');
    return {
      company: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).company ? JSON.parse(filters).company : '',
      searchKey: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).searchKey ? JSON.parse(filters).searchKey : '',
      companyId: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).companyId ? JSON.parse(filters).companyId : '',

      loading: false,
      companyListFilter: [],

      loadingData: false,

      permissionList: window.sessionStorage.getItem('bg_user_permission'),

      //分页
      tp: 0,
      cp: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).cp ? parseInt(JSON.parse(filters).cp) : 1,
      ep: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).ep ? parseInt(JSON.parse(filters).ep) : 10,

      //表格选中的项
      selectItem: [],

      companyList: [],

      //表格配置
      accountColumns: [{
        type: 'selection',
        width: 60,
        align: 'center'
      }, {
        title: '用户名',
        key: 'username'
      }, {
        title: '真实姓名',
        key: 'realName'
      }, {
        title: '机构',
        key: 'agency'
      }, {
        title: '部门',
        key: 'department'
      }, {
        title: '角色',
        key: 'item_name'
      }, {
        title: '注册时间',
        key: 'signTime',
        sortable: true,
        sortType: 'desc'
      }, {
        title: '操作',
        key: 'action',
        width: 260,
        align: 'center',
        render: (h, params) => {
          return h('div', [
            h('Button', {
              props: {
                type: 'primary',
                disabled: this.permissionList.indexOf('user/create') != -1 ? false : true
              },
              on: {
                click: () => {
                  this.editInfo(params.row.id, params.index)
                }
              }
            }, '编辑'),
            h('Button', {
              props: {
                type: 'error',
                disabled: this.permissionList.indexOf('user/delete') != -1 ? false : true
              },
              on: {
                click: () => {
                  this.removeUser(params.row.id, params.index)
                }
              }
            }, '删除'),
            h('Button', {
              props: {
                type: 'warning',
                disabled: this.permissionList.indexOf('user/reset-password') != -1 ? false : true
              },
              on: {
                click: () => {
                  this.resetPsd(params.row.id, params.index)
                }
              }
            }, '重置密码')
          ]);
        }
      }],

      //表格数据
      accountDatas: [],

      //新增账号数据


    }
  },
  methods: {

    getListData(isSetCp = false) {
      if (isSetCp) {
        this.cp = 1;
      }

      if (this.company == '') {
        this.companyId = 0;
      } else {
        this.companyList.forEach((item, index) => {
          if (item.name == this.company) {
            this.companyId = item.id;
          }
        })
      }

      this.loadingData = true;

      this.$http.get('/v1/admin/user/list', {
        params: {
          agencyId: this.companyId,
          keyword: this.searchKey,
          pageSize: this.ep,
          page: this.cp
        }
      }).then((res) => {
        this.tp = parseInt(res.data.data.total);
        this.accountDatas = res.data.data.rows;
        this.loadingData = false;
      })
    },

    //获取机构列表
    getCompanyList() {
      this.$http.get('/v1/admin/user/agency-list')
        .then((res) => {
          this.companyList = res.data.data.data;
          this.companyList.unshift({
            id:'0',
            name:'全部'
          })
        })
    },

    //新增账户
    addNewAccount() {
      this.$router.push({
        path: '/wrap/system/account/add'
      })
    },
    //搜索关键字
    searchInfo() {
      if (this.searchKey) {
        this.cp = 1;
        this.updateFilter({ 'searchKey': this.searchKey });
        this.getListData();
      } else {
        this.$Message.warning('搜索关键字不能为空')
      }
    },

    //编辑
    editInfo(id, index) {
      this.$router.push({
        path: '/wrap/system/account/edit',
        query: {
          id: id
        }
      })
    },

    //删除单个
    removeUser(id, index) {
      let userIds = [id];
      this.$Modal.confirm({
        title: '确认操作',
        content: '<h3>该操作将删除用户，请确认执行此操作</h3>',
        onOk: () => {
          this.deleteUserById(userIds);
        },
      });

    },

    //批量删除
    deleteUser() {
      let userIds = [];
      if (this.selectItem.length != 0) {
        this.$Modal.confirm({
          title: '确认操作',
          content: '<h3>该操作将删除用户，请确认执行此操作</h3>',
          onOk: () => {
            this.selectItem.forEach((item, index) => {
              userIds.push(item.id)
            })
            this.deleteUserById(userIds);
          },
        });
      } else {
        this.$Message.warning('请至少勾选一个用户后再试！')
      }
    },

    //删除用户
    deleteUserById(userIds) {
      console.log(1111)
      this.$http.post('/v1/admin/user/delete-user', {
        userId: userIds
      }).then((res) => {
        if (res.data.status == 200) {
          this.$Message.success('用户删除成功');
          this.getListData();
        }
      })
    },

    companyChange() {
      if (this.company == '') {
        this.companyId = 0;
      } else {
        this.companyList.forEach((item, index) => {
          if (item.name == this.company) {
            this.companyId = item.id;
          }
        })
      }
      this.updateFilter({ 'company': this.company, 'companyId': this.companyId })
      this.getListData(true)
    },

    currentPageChange(value) {
      this.cp = value;
      this.updateFilter({'cp':this.cp});
      this.getListData();
    },

    eachPageChange(value) {
      this.ep = value;
      this.updateFilter({'ep':this.ep});
      this.getListData();
    },

    //表格单选/单选
    tableSelectCurrent(selection) {
      this.selectItem = selection;
    },
    tableSelectAll(selection) {
      this.selectItem = selection;
    },
    tableCancelCurrent(selection, row) {
      this.selectItem = selection;
    },
    tableChange(selection) {
      this.selectItem = selection;
    },


    //重置密码
    resetPsd(id, index) {
      this.$Modal.confirm({
        title: '确认操作',
        content: '<h3>该操作将重置用户密码，请确认执行此操作</h3>',
        onOk: () => {
          this.$http.post('/v1/admin/user/reset-password', {
            userId: id
          }).then((res) => {
            if (res.data.status == 200) {
              this.$Message.success({
                content: '用户密码重置成功，新密码为 123456 ',
                duration: 6
              })
            }
          })
        },
      });
    },


    //筛选机构
    remoteMethod(query) {
      if (query !== '') {
        this.loading = true;
        this.companyListFilter = this.companyList.filter(item => item.name.indexOf(query) > -1);
        this.loading = false;
      } else {
        this.companyListFilter = [{
          id:'0',
          name:'全部'
        }];
      }
    },

  },
  beforeRouteLeave(to, from, next) {
    if (to.fullPath.indexOf('/account/add') != -1 || to.fullPath.indexOf('/account/edit') != -1) {
      window.sessionStorage.setItem('bg_user_filter', JSON.stringify({
        company: this.company,
        searchKey: this.searchKey,
        cp: this.cp,
        ep: this.ep,
        companyId: this.companyId,
      }))
    } else {
      window.sessionStorage.setItem('bg_user_filter', '')
    }
    next();
  },
  mounted() {
    this.getCompanyList();
    this.getListData();
  },
  watch: {
    'searchKey': function(val, oldVal) {
      if (!val && oldVal) {
        this.cp = 1;
        this.updateFilter({ 'searchKey': '' })
        this.getListData();
      }
    },
  }
}

</script>
<style lang="less">


</style>
