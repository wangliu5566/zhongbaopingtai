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
    <Row>
      <div class="activity-works-header">
        <h3>作品点击量</h3>
        <p class="pt10">总点击量：<span style="font-size: 18px;color: rgb(45, 140, 240);font-weight: bold;">{{totalClick}}</span></p>
        <Row>
          <div class="activity-click-line" ref="activityClickShowLine"></div>
        </Row>
      </div>
      <Row>
        <Table :columns="accountColumns" :data="accountDatas" highlight-row></Table>
      </Row>
    </Row>
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
      defaultStatus: 'today',
      totalClick: '0',

      //echarts配置
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
          data: ['视频', '图片', "其它"]
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
          name: '时间/日期',
          nameGap: '10',
          boundaryGap: false,
          data: []
        }],
        yAxis: [{
          type: 'value',
          name: '点击量',
        }],
        series: [{
          name: '视频',
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
          name: '图片',
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
          name: '其它',
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

      //表格数据
      accountDatas: [],

      //表格配置
      accountColumns: [{
        title: '时间/日期',
        key: 'time'
      }, {
        title: '视频',
        key: 'video',
        sortable: true,
      }, {
        title: '图片',
        key: 'image',
        sortable: true,
      }, {
        title: '其他',
        key: 'other',
        sortable: true,
      }, {
        title: '总计',
        sortable: true,
        key: 'total'
      }],

    }
  },
  methods: {
    statusChange() {
      this.getClickData();
    },
    //渲染折线图
    renderLine() {
      let myEcharts = echarts.init(this.$refs.activityClickShowLine);
      myEcharts.setOption(this.optionLine);
      this.getClickData();
    },

    //获取点击量数据
    getClickData() {
      let myEcharts = echarts.getInstanceByDom(this.$refs.activityClickShowLine);
      myEcharts.showLoading();
      this.$http.get('/v1/admin/analysis/click', {
        params: {
          gran: this.defaultStatus,
          uid: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id,
        }
      }).then((res) => {
        if (res.data.status == 200) {
          this.totalClick = res.data.data.total;
          myEcharts.hideLoading();
          myEcharts.setOption({
            xAxis: {
              data: res.data.data.rows.x
            },
            series: [{
              name: '视频',
              data: res.data.data.rows.yVideo
            }, {
              name: '图片',
              data: res.data.data.rows.yImage
            }, {
              name: '其它',
              data: res.data.data.rows.yOther
            }]
          });
          this.accountDatas = res.data.data.table;
        }
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
