<template>
  <div>
    <Row>
      <Col span="8" offset="16">
      <Input v-model="searchKey" icon="ios-search-strong" placeholder="搜索机构 / 用户名" @on-click="searchInfo"></Input>
      </Col>
    </Row>
    <Row class="pt20 pb20">
      <Table :columns="accountColumns" :data="accountDatas" highlight-row></Table>
    </Row>
    <Row class="tr pt5 pb10">
      <Row class="tr pt10 pb20">
        <Page :total="tp" :current='cp' :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
      </Row>
    </Row>
  </div>
</template>
<script>
export default {
  data() {
    const filters = window.sessionStorage.getItem('bg_user_filter');
    return {
      searchKey: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).searchKey ? JSON.parse(filters).searchKey : '',

      //分页
      tp: 0,
      cp: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).cp ? JSON.parse(filters).cp : 1,
      ep: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).ep ? JSON.parse(filters).ep : 10,

      permissionList: window.sessionStorage.getItem('bg_user_permission'),

      //表格配置
      accountColumns: [{
        title: '编号',
        key: 'userId'
      }, {
        title: '用户',
        key: 'userName'
      }, {
        title: '机构',
        key: 'companyName'
      }, {
        title: '发起活动总数',
        key: 'num',
        sortable: true,
      }, {
        title: '累计参与人数',
        key: 'total',
        sortable: true,
      }, {
        title: '平均参与率',
        sortable: true,
        // key: 'publishedtime',
        render: (h, params) => {
          return h('span', {

          }, params.row.point + '%')
        }
      }, {
        title: '操作',
        key: 'action',
        width: 100,
        align: 'center',
        render: (h, params) => {
          return h('div', [
            h('Button', {
              props: {
                type: 'ghost',
                disabled: this.permissionList.indexOf('initiator/user-active-list') != -1 ? false : true
              },
              on: {
                click: () => {
                  this.checkPublisher(params.row.userId, params.index)
                }
              }
            }, '查看'),
          ]);
        }
      }],

      //表格数据
      accountDatas: [],

    }
  },
  beforeRouteLeave(to, from, next) {
    console.log(to)
    if (to.fullPath.indexOf('/publisher/list') != -1) {
      window.sessionStorage.setItem('bg_user_filter', JSON.stringify({
        searchKey: this.searchKey,
        cp: this.cp,
        ep: this.ep,
      }))
    } else {
      window.sessionStorage.setItem('bg_user_filter', '')
    }
    next();
  },
  methods: {
    checkPublisher(id, index) {
      this.$router.push({
        path: '/wrap/data/publisher/list',
        query: {
          id: id
        }
      })
    },

    //获取列表数据
    getListData() {
      this.$http.get('/v1/admin/initiator/related-user', {
        params: {
          keyword: this.searchKey,
          page: this.cp,
          pageSize: this.ep
        }
      }).then((res) => {
        this.accountDatas = res.data.data.rows;
        this.tp = parseInt(res.data.data.total)
      })
    },

    currentPageChange(value) {
      this.cp = value;
      this.updateFilter({ 'cp': this.cp });
      this.getListData();
    },

    eachPageChange(value) {
      this.ep = value;
      this.updateFilter({ 'ep': this.ep });
      this.getListData();
    },


    searchInfo() {
      if (this.searchKey != '') {
        this.cp = 1;
        this.updateFilter({
          'searchKey': this.searchKey
        })
        this.getListData();
      } else {
        this.$Message.warning('搜索关键字不能为空')
      }
    },
  },
  mounted() {
    this.getListData();
  },
  watch: {
    'searchKey': function(val, oldVal) {
      if (!val && oldVal) {
        this.cp = 1;
        this.getListData();
      }
    }
  }
}

</script>
<style lang="less">


</style>
