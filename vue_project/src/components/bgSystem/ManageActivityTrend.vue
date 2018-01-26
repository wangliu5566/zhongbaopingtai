<template>
  <div>
    <div class="activity-main">
      <Row>
        <div class="activity-status-header">
          <h3>活动趋势分析</h3>
          <Row>
            <div class="activity-show-line" ref="activityShowLine"></div>
          </Row>
        </div>
        <Row>
          <Table :columns="accountColumnsBar" :data="accountDatasBar" highlight-row></Table>
        </Row>
      </Row>
    </div>
  </div>
</template>
<script>
// 引入 ECharts 主模块
var echarts = require('echarts/lib/echarts')
// 引入折线图
require('echarts/lib/chart/line')
// 引入提示框和标题组件
require('echarts/lib/component/tooltip')
require('echarts/lib/component/title')
require('echarts/lib/component/legend')
require('echarts/lib/component/toolbox')
export default {
  data() {
    return {
      defaultType1: '汇总',
      //echarts数据
      optionLine: {
        title: {
          text: '',
        },
        tooltip: {
          trigger: 'axis',
          padding: 10,
          axisPointer: {
            type: 'cross',
            label: {
              backgroundColor: '#6a7985',
              precision: 0,
            }
          }
        },
        legend: {
          data: ['大赛', '问卷', "总计"]
        },
        toolbox: {
          // feature: {
          //   saveAsImage: {}
          // },
          // right: 20,
        },
        grid: {
          left: '3%',
          right: '12%',
          bottom: '3%',
          containLabel: true
        },
        xAxis: [{
          type: 'category',
          name: '月份',
          nameGap: '10',
          boundaryGap: false,
          data: []
        }],
        yAxis: [{
          type: 'value',
          name: '活动数量',
        }],
        series: [{
          name: '大赛',
          type: 'line',
          smooth: true,
          showAllSymbol: true,
          itemStyle: {
            normal: {
              color: '#0099ff',
            },
          },
          data: []
        }, {
          name: '问卷',
          type: 'line',
          smooth: true,
          showAllSymbol: true,
          itemStyle: {
            normal: {
              color: '#ff0000',
            },
          },
          data: []
        }, {
          name: '总计',
          type: 'line',
          smooth: true,
          showAllSymbol: true,
          itemStyle: {
            normal: {
              color: '#33cc99',
            },
          },
          data: []
        }]
      },
      //表格配置
      accountColumnsBar: [{
        title: '月份',
        key: 'date'
      },{
        title: '大赛',
        key: 'active',
        sortable: true,
      },{
        title: '问卷',
        key: 'question',
        sortable: true,
      },{
        title:'总计',
        sortable: true,
        render:(h,params)=>{
          return h('span',params.row.active+params.row.question)
        }
      }],


      //表格数据
      accountDatasBar: [],
    }
  },
  methods: {
    renderLine() {
      let myEcharts = echarts.init(this.$refs.activityShowLine);
      myEcharts.setOption(this.optionLine);
      this.getTrendData();
    },
    getTrendData() {
      let myEcharts = echarts.getInstanceByDom(this.$refs.activityShowLine);
      myEcharts.showLoading();
      this.$http.get('/v1/admin/data/trend',{
        params:{
          uid:JSON.parse(window.sessionStorage.getItem('bg_user_info')).id
        }
      }).then((res) => {
        myEcharts.hideLoading();
        myEcharts.setOption({
          xAxis: {
            data: res.data.data.chart.month
          },
          series: [{
            name: '大赛',
            data: res.data.data.chart.active
          }, {
            name: '问卷',
            data: res.data.data.chart.question
          }, {
            name: '总计',
            data: res.data.data.chart.total
          }]
        });
        this.accountDatasBar = res.data.data.table;
      })
    }
  },
  mounted() {
    this.renderLine();
  }
}

</script>
<style lang="less">


</style>
