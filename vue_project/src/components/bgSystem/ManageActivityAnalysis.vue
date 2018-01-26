<template>
  <div>
    <Row>
      <Spin fix v-show="loadingData">
        <Icon type="load-c" size=18 class="demo-spin-icon-load"></Icon>
        <div>数据加载中...</div>
      </Spin>
      <Row class="pt10 pb10" v-show="!loadingData">
        <Table :columns="accountColumns" :data="accountDatas" highlight-row></Table>
      </Row>
    </Row>
    <Row class="tr pt5 pb10">
      <Page :total="tp" :current='cp' :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
    </Row>
    <!-- 修改管理员modal -->
    <Modal v-model="showRankingModel" :mask-closable="false" width="1000" title="查看排名">
      <div class="edit-model-body">
        <Row>
          <Col span="7">
          <Spin fix v-show="loadingData1">
            <Icon type="load-c" size=18 class="demo-spin-icon-load"></Icon>
            <div>数据加载中...</div>
          </Spin>
          <Table :columns="accountColumns1" :data="accountDatas1" highlight-row></Table>
          <div class="tr pt5 pb10">
            <Page :total="tp1" :current='cp1' :page-size="ep1" size="small" show-total @on-change="currentPageChange1"></Page>
          </div>
          </Col>
          <Col span="1">
            &nbsp;
          </Col>
          <Col span="16">
            <Spin fix v-show="loadingData2">
            <Icon type="load-c" size=18 class="demo-spin-icon-load"></Icon>
            <div>数据加载中...</div>
          </Spin>
          <Table :columns="accountColumns2" :data="accountDatas2" highlight-row></Table>
          <div class="tr pt5 pb10">
            <Page :total="tp2" :current='cp2' :page-size="ep2" size="small" show-total @on-change="currentPageChange2"></Page>
          </div>
          </Col>
        </Row>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="showRankingModel=false">返回</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
export default {
  data() {
    return {

      loadingData: false,
      loadingData1: false,
      loadingData2:false,
      //分页信息
      cp: 1,
      ep: 10,
      tp: 0,

      cp1: 1,
      ep1: 10,
      tp1: 0,

      cp2: 1,
      ep2: 10,
      tp2: 0,

      //模态框
      showRankingModel: false,

      //表格数据
      accountDatas: [],
      accountDatas1: [],
      accountDatas2: [],

      //表格配置
      accountColumns: [{
        title: '活动名称',
        key: 'name'
      }, {
        title: '参加用户数',
        key: 'signCount'
      }, {
        title: '新增注册用户数',
        key: 'registerCount'
      }, {
        title: '提交作品数',
        key: 'auditCount',
      }, {
        title: '审核通过作品数',
        key: 'worksCount',
      }, {
        title: '总点赞量',
        key: 'hits',
      }, {
        title: '总分享量',
        key: 'share',
      }, {
        title: '总点击量',
        key: 'click',
      }, {
        title: '操作',
        key: 'action',
        render: (h, params) => {
          return h('Button', {
            props: {
              type: 'primary'
            },
            on: {
              click: () => {
                this.showRankingList(params.row, params.index)
              },
            }
          }, '查看排名')
        }
      }],

      accountColumns1:[{
        title:'学校名称',
        key:'name'
      },{
        title:'参赛人数',
        key:'number',
        width:100,
      },{
        title:'操作',
        key:'action',
        width:90,
        render:(h,params)=>{
          return h('Button',{
            props:{
              type:'primary',
              size:'small'
            },
            on:{
              click:()=>{
                this.getSchoolRanking(params.row,params.index);
              }
            }
          },'查看详情')
        }
      }],
      accountColumns2:[{
        title:'排名',
        key:'rank',
      },{
        title:'用户名',
        key:'username'
      },{
        title:'学号',
        key:'studentId'
      },{
        title:'电话号码',
        key:'tel'
      },{
        title:'点赞数',
        key:'hits'
      },{
        title:'分享数',
        key:'share'
      }]
    }
  },
  methods: {
    getDataList() {
      this.$http.get('/v1/admin/data/active', {
        params: {
          uid: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id,
          page: this.cp,
          pageSize: this.ep,
        }
      }).then((res) => {
        this.loadingData = false;
        this.tp = parseInt(res.data.data.total);
        this.accountDatas = res.data.data.rows;
      })
    },

    //分页方法
    currentPageChange(value) {
      this.cp = value;
      this.getDataList();
    },
    currentPageChange1(value) {
      this.cp1 = value;
      this.showRankingList();
    },
    currentPageChange2(value) {
      this.cp2 = value;
      this.getSchoolRanking();
    },
    eachPageChange(value) {
      this.ep = value;
      this.getDataList();
    },
    showRankingList(row, index) {
      this.showRankingModel = true;
      this.loadingData1 = true;
      this.$http.get('/v1/admin/data/school-list', {
          params: {
            activityId: row.id,
            pageSize: this.ep1,
            page: 1
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.accountDatas1 = res.data.data.rows;
            this.loadingData1 = false;
            this.tp1 = parseInt(res.data.data.total);
            
          }
        })
    },
    getSchoolRanking(row,index){
      this.loadingData2 = true;
      this.$http.get('/v1/admin/data/join-user',{
        params:{
          name:row.name,
          activityId:row.activityId,
          pageSize:this.ep2,
          page:this.cp2
        }
      })
      .then((res)=>{
        this.accountDatas2 = res.data.data.rows;
        this.tp2 = parseInt(res.data.data.total)
        this.loadingData2 = false;
      })
    }
  },
  created() {
    this.getDataList();
  },
  watch:{
    showRankingModel:function(val,oldVal){
      if (!val && oldVal) {
        this.accountDatas2 = [];
      }
    }
  }
}

</script>
<style lang="less">


</style>
