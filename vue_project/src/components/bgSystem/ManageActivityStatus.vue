<template>
  <div>
    <div class="activity-main">
      <Row class="pt10">
        <Col span="18">
        <Radio-group v-model="defaultStatus" type="button" size="large" @on-change="statusChange">
          <Radio label="0">今日</Radio>
          <Radio label="-1">昨日</Radio>
          <Radio label="-6">最近7天</Radio>
          <Radio label="-29">最近30天</Radio>
        </Radio-group>
        </Col>
        <Col span="6">
        <Select v-model="region" style="width: 70%;" @on-change="regionChange" class="pull-right">
          <Option value="0">全部</Option>
          <Option v-for="(item,index) in regionList" :label="item" :value="item" :key="item+index"></Option>
        </Select>
        <span class="pull-right">地区：</span>
        </Col>
      </Row>
      <div class="activity-status-header">
        <h3>活动状态分析</h3>
        <Row class="tc pt10 pb10">
          <Radio-group v-model="defaultType" @on-change="getStatusData">
            <Radio label="0"><span class="view-type">汇总</span></Radio>
            <Radio label="1"><span class="view-type">大赛</span></Radio>
            <Radio label="2"><span class="view-type">问卷</span></Radio>
          </Radio-group>
        </Row>
        <Row>
          <Col span="6" class="tr" style="font-size: 16px;">
          <span v-if="defaultType==0">活动总数：</span>
          <span v-if="defaultType==1">大赛类活动数：</span>
          <span v-if="defaultType==2">问卷类活动数：</span>
          <span style="font-size: 18px;color:#2d8cf0;font-weight: bold;">{{total}}</span>
          </Col>
          <Col span="18">
          <div class="activity-show-echart" ref="activityShowPie"></div>
          </Col>
        </Row>
      </div>
      <Table :columns="accountColumns" :data="accountDatas" highlight-row></Table>
      </Row>
    </div>
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
      defaultType: '0',
      defaultStatus: '0',
      region: '0',
      total: '0',
      regionList: ['北京', '天津', '河北', '山西', '内蒙古', '辽宁', '吉林', '黑龙江', '上海', '江苏', '浙江', '安徽', '福建', '江西', '山东', '河南', '湖北', '湖南', '广东', '广西', '海南', '重庆', '四川', '贵州', '云南', '西藏', '陕西', '甘肃', '青海', '宁夏', '新疆', '香港', '澳门', '台湾'],
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
          data: ['未开始', '进行中', '已结束']
        },
        series: [{
          name: '活动状态',
          type: 'pie',
          radius: '55%',
          center: ['50%', '60%'],
          data: [
            { value: '', name: '未开始' },
            { value: '', name: '进行中' },
            { value: '', name: '已结束' },
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

      //表格配置
      accountColumns: [{
        title: '活动类型',
        key: 'title'
      }, {
        title: '未开始',
        key: 'init'
      }, {
        title: '进行中',
        key: 'started'
      }, {
        title: '已结束',
        key: 'end'
      }, {
        title: '注册用户数',
        key: 'user'
      }, {
        title: '分享数',
        key: 'share'
      }],
      //表格数据
      accountDatas: [],
    }
  },
  methods: {
    renderPie() {
      var myEcharts = echarts.init(this.$refs.activityShowPie);
      myEcharts.setOption(this.optionPie);
      this.getStatusData();
    },
    getStatusData() {
      var myEcharts = echarts.getInstanceByDom(this.$refs.activityShowPie);
      myEcharts.showLoading();
      let gran = '';
      switch (this.defaultStatus) {
        case '0':
          gran = 'hour'
          break;
        case '-1':
          gran = 'hour'
          break;
        case '-6':
          gran = 'day'
          break;
        case '-29':
          gran = 'day'
          break;
        default:
          break;
      }
      this.$http.get('/v1/admin/data/active-point', {
        params: {
          type: this.defaultType,
          uid: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id,
          days: this.defaultStatus,
          gran: gran,
          area: this.region
        }
      }).then((res) => {
        let chartDatas = res.data.data.point;
        this.total = chartDatas.total;
        this.accountDatas = res.data.data.count;
        myEcharts.hideLoading();
        myEcharts.setOption({
          series: {
            data: [
              { value: chartDatas.init, name: '未开始' },
              { value: chartDatas.started, name: '进行中' },
              { value: chartDatas.end, name: '已结束' },
            ]
          }
        })
      })
    },
    // getListData() {
    //   this.$http.get('/v1/admin/data/activity', {
    //     params: {
    //       uid: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id
    //     }
    //   }).then((res) => {
    //     this.accountDatas = res.data.data;
    //   })
    // },

    regionChange() {
      this.getStatusData();
    },

    statusChange() {
      this.getStatusData();
    },
  },
  mounted() {
    // this.getListData();
    this.renderPie();
  }
}

</script>
<style lang="less">


</style>
