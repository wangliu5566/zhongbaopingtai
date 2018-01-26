<template>
  <div class="view-main">
    <Row class="pt10">
      <Col span="18">
      <Radio-group v-model="defaultStatus" type="button" size="large" @on-change="statusChange">
        <Radio label="today">今日</Radio>
        <Radio label="yesterday">昨日</Radio>
        <Radio label="week">最近7天</Radio>
        <Radio label="month">最近30天</Radio>
      </Radio-group>
      </Col>
    </Row>
    <div class="activity-works-header">
      <h3>作品分析</h3>
      <Row class="tc pt10 pb10">
        <Radio-group v-model="defaultType" @on-change="getStatusData">
          <Radio label="1"><span class="view-type">数量</span></Radio>
          <Radio label="2"><span class="view-type">容量</span></Radio>
        </Radio-group>
      </Row>
      <Row>
        <Col span="6" class="tr" style="font-size: 16px;">
        <span>作品总数：</span>
        <span style="font-size: 18px;color:#2d8cf0;font-weight: bold;">{{total}}</span>
        </Col>
        <Col span="18">
        <div class="activity-show-echart" ref="activityWorksShowPie"></div>
        </Col>
      </Row>
    </div>
    <Table :columns="accountColumns" :data="accountDatas" highlight-row></Table>
    </Row>
  </div>
</template>
<script>
// 引入 ECharts 主模块
var echarts = require('echarts/lib/echarts')
// 引入饼图
require('echarts/lib/chart/pie')
// 引入提示框和标题组件
require('echarts/lib/component/tooltip')
require('echarts/lib/component/title')
require('echarts/lib/component/legend')
require('echarts/lib/component/toolbox')
export default {
  data() {
    return {
      defaultStatus: 'today',
      defaultType: '1',
      total: '0',

      //饼图配置
      optionPie: {
        title: {
          text: '',
          x: 'center'
        },
        tooltip: {
          trigger: 'item',
          formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
          orient: 'vertical',
          left: 'right',
          data: ['图片型作品', '视频型作品', '其他作品']
        },
        series: [{
          name: '作品分析',
          type: 'pie',
          radius: '55%',
          center: ['50%', '60%'],
          data: [
            { value: '', name: '图片型作品' },
            { value: '', name: '视频型作品' },
            { value: '', name: '其他作品' },
          ],
          itemStyle: {
            emphasis: {
              shadowBlur: 10,
              shadowOffsetX: 0,
              shadowColor: 'rgba(0, 0, 0, 0.5)'
            }
          }
        }]
      },
      //表格数据
      accountDatas:[],
      //表格配置
      accountColumns:[{
        title: '类型',
        key: 'title'
      }, {
        title: '图片',
        key: 'image'
      }, {
        title: '视频',
        key: 'video'
      }, {
        title: '其他',
        key: 'other'
      }, {
        title: '汇总',
        key: 'total'
      }]
    }
  },
  methods: {
    statusChange() {
    	this.getStatusData();
    },
    renderPie() {
      var myEcharts = echarts.init(this.$refs.activityWorksShowPie);
      myEcharts.setOption(this.optionPie);
      this.getStatusData();
    },
    getStatusData() {
      var myEcharts = echarts.getInstanceByDom(this.$refs.activityWorksShowPie);
      myEcharts.showLoading();
      this.$http.get('/v1/admin/analysis/works', {
        params: {
          type: this.defaultType,
          uid: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id,
          gran: this.defaultStatus,
        }
      }).then((res) => {
        let chartDatas = res.data.data.point;
        this.total = chartDatas.worksCount;
        this.accountDatas = res.data.data.table;
        myEcharts.hideLoading();
        myEcharts.setOption({
          series: {
            data: [
              { value: chartDatas.imagePoint, name: '图片型作品' },
              { value: chartDatas.videoPoint, name: '视频型作品' },
              { value: chartDatas.otherPoint, name: '其他作品' },
            ]
          }
        })
      })
    },
  },
  mounted() {
    this.renderPie();
  }
}

</script>
<style lang="less">


</style>
