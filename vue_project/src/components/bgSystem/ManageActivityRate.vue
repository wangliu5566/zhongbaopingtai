<template>
  <div class="bg-activity-rate">
    <div class="activity-main">
      <Row>
        <div class="activity-status-header">
          <h3>活动参与分析</h3>
          <Row class="tc pt10 pb10">
            <Radio-group v-model="defaultType" @on-change="getRateDatas(true)">
              <Radio label="0"><span class="view-type">汇总</span></Radio>
              <Radio label="1"><span class="view-type">大赛</span></Radio>
              <Radio label="2"><span class="view-type">问卷</span></Radio>
            </Radio-group>
          </Row>
          <Row>
            <div class="activity-show-bar" ref="activityShowBar"></div>
          </Row>
        </div>
        <Radio-group v-model="defaultTypePoint" type="button" size="large" @on-change="getListData(true)">
          <Radio label="0-15">15%以下</Radio>
          <Radio label="15-30">15%~30%</Radio>
          <Radio label="30-45">30%~45%</Radio>
          <Radio label="45-60">45%~60%</Radio>
          <Radio label="60-75">60%~75%</Radio>
          <Radio label="75-90">75%~90%</Radio>
          <Radio label="90-">90%以上</Radio>
        </Radio-group>
        <Row class="pb10 pt10">
          选择活动类型：
          <Select v-model="defaultType1" style="width:200px" @on-change='getListData(true)'>
            <Option value="0">不限</Option>
            <Option value="1">大赛</Option>
            <Option value="2">问卷</Option>
          </Select>
        </Row>
        <Row class="pt10 pb10">
          <Table :columns="accountColumnsBar" :data="accountDatasBar" highlight-row></Table>
        </Row>
        <Row class="tr pt10 pb20">
          <Page :total="tp" :current='cp' :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
        </Row>
      </Row>
    </div>
  </div>
</template>
<script>
// 引入 ECharts 主模块
var echarts = require('echarts/lib/echarts')
// 引入柱状图
require('echarts/lib/chart/bar')
// 引入提示框和标题组件
require('echarts/lib/component/tooltip')
require('echarts/lib/component/title')
require('echarts/lib/component/legend')
require('echarts/lib/component/toolbox')
export default {
  data() {
    return {
      defaultType: '0',
      defaultTypePoint: '0-15',
      defaultType1: '0',

      //分页
      tp: 0,
      cp: 1,
      ep: 10,

      //柱状图配置
      optionBar: {
        // color: ['#3398DB'],
        tooltip: {
          trigger: 'axis',
        },
        grid: {
          left: '3%',
          right: '10%',
          bottom: '3%',
          containLabel: true
        },
        xAxis: [{
          type: 'category',
          data: ['15%以下', '15%~30%', '30%~45%', '45%~60%', '60%~75%', '75%~90%', '90%以上'],
          axisTick: {
            alignWithLabel: true
          },
          name: '百分比'
        }],
        yAxis: [{
          type: 'value',
          name: '活动数量'
        }],
        series: [{
          name: '活动数量',
          type: 'bar',
          barWidth: '20%',
          itemStyle: {
            normal: {
              // color: '#ff0000',
            },
          },
          data: []
        }]
      },

      //表格配置
      accountColumnsBar: [{
        title: '活动名称',
        key: 'name'
      }, {
        title: '活动类型',
        render: (h, params) => {
          if (params.row.type == 1) {
            return h('span', '大赛')
          } else if (params.row.type == 2) {
            return h('span', '问卷')
          }
        }
      }, {
        title: '发起机构',
        key: 'companyName'
      }, {
        title: '总人数',
        key: 'number',
        sortable: true,
      }, {
        title: '作品数量',
        key: 'worksCount',
        sortable: true,
      }, {
        title: '参与率',
        sortable: true,
        render: (h, params) => {
          return h('span', params.row.point + '%')
        }
      }],

      //表格数据
      accountDatasBar: [],
    }
  },
  methods: {
    renderBar() {
      var myEcharts = echarts.init(this.$refs.activityShowBar);
      myEcharts.setOption(this.optionBar);
      this.getRateDatas();
    },
    getRateDatas() {
      var myEcharts = echarts.getInstanceByDom(this.$refs.activityShowBar);
      myEcharts.showLoading();
      this.$http.get('/v1/admin/data/participate', {
        params: {
          type: this.defaultType,
          uid:JSON.parse(window.sessionStorage.getItem('bg_user_info')).id
        }
      }).then((res) => {
        myEcharts.hideLoading();
        myEcharts.setOption({
          series: [{
            name: '活动数量',
            data: res.data.data.yAlias,
          }]
        })
      })
    },
    getListData(isSetCp=false) {
      if (isSetCp) {
        this.cp = 1;
      }
      this.$http.get('/v1/admin/data/list', {
        params: {
          point: this.defaultTypePoint,
          type: this.defaultType1,
          page: this.cp,
          pageSize: this.ep,
          uid:JSON.parse(window.sessionStorage.getItem('bg_user_info')).id
        }
      }).then((res) => {
        this.accountDatasBar = res.data.data.rows;
        this.tp = parseInt(res.data.data.total);
      })
    },
    currentPageChange(value) {
      this.cp = value;
      this.getListData();
    },
    eachPageChange(value) {
      this.ep = value;
      this.getListData();
    }
  },
  mounted() {
    this.renderBar();
    this.getListData();
  },
}

</script>
<style lang="less">
.activity-show-bar {
  width: 100%;
  height: 500px;
}

.bg-activity-rate{
  .ivu-radio-group-button .ivu-radio-wrapper{
    padding: 0 24px;
  }
}

</style>
