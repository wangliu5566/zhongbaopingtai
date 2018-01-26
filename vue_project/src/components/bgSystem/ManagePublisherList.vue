<template>
  <div>
    <Row>
      <Col span="6">
      <Button type="primary" icon="chevron-left" @click="goBackPublisher">返回列表</Button>
      </Col>
      <Col span="6" offset="6">
      <span class="pt5">活动类型：</span>
      <Select v-model="filter.eventType" style="width: 60%;" @on-change="getListData">
        <Option value="0">不限</Option>
        <Option v-for="(item,index) in eventTypes" :label="item.name" :value="item.id" :key="item.id+index"></Option>
      </Select>
      </Col>
      <Col span="6">
      <span class="pt5">活动状态：</span>
      <Select v-model="filter.eventStatus" style="width: 60%;" @on-change="getListData">
        <Option value="0">不限</Option>
        <Option v-for="(item,index) in eventStatus" :label="item.name" :value="item.id" :key="item.id+index"></Option>
      </Select>
      </Col>
    </Row>
    <Row class="pb20 pt20">
      <Table :columns="accountColumns" :data="accountDatas" highlight-row></Table>
    </Row>
    <Row class="tr pt5 pb20">
      <Row class="tr pt10 pb20">
        <Page :total="tp" :current='cp' :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
      </Row>
    </Row>
  </div>
</template>
<script>
export default {
  data() {
    return {
      //分页
      tp: 0,
      cp: 1,
      ep: 10,
      //筛选条件
      filter: {
        eventType: '0',
        eventStatus: '0',
      },
      //活动类型
      eventTypes: [{
        id: '1',
        name: '大赛'
      }, {
        id: '2',
        name: '问卷'
      }],
      //活动状态
      eventStatus: [{
        id: '1',
        name: '未开始'
      }, {
        id: '2',
        name: '进行中'
      }, {
        id: '3',
        name: '已结束'
      }],
      //表格配置
      accountColumns: [{
        title: '活动名称',
        key: 'name'
      }, {
        title: '活动类型',
        render:(h,params)=>{
          if (params.row.type==1) {
            return h('span','大赛')
          }else if(params.row.type==2) {
            return h('span','问卷')
          } 
        }
      }, {
        title: '活动状态',
        render:(h,params)=>{
          if (params.row.status==1) {
            return h('span','未开始')
          }else if(params.row.status==2) {
            return h('span','进行中')
          }else if(params.row.status==3) {
            return h('span','已结束')
          }
        }
      }, {
        title: '参与人数',
        key: 'number',
        sortable: true,
      }, {
        title: '参与率',
        sortable: true,
        render:(h,params)=>{
          return h('span',params.row.point+'%')
        }
      }],

      //表格数据
      accountDatas: [],
    }
  },
  
  methods: {
    goBackPublisher() {
      this.$router.push({
        path: '/wrap/data/publisher',
        query: {

        }
      })
    },
    getListData(){
      this.$http.get('/v1/admin/initiator/user-active-list',{
        params:{
          userId:this.$route.query.id,
          type:this.filter.eventType,
          status:this.filter.eventStatus,
          page:this.cp,
          pageSize:this.ep
        }
      }).then((res)=>{
        this.accountDatas = res.data.data.rows;
        this.tp = parseInt(res.data.data.total);
      })
    },
    currentPageChange(value){
      this.cp = value;
      this.getListData();
    },
    eachPageChange(value){
      this.ep = value;
      this.getListData();
    }
  },
  mounted(){
    this.getListData();
  }
}

</script>
<style lang="less">


</style>
