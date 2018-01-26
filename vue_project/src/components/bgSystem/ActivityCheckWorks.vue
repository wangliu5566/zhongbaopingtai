<template>
  <div class="activity-check-container">
    <Row>
      <Col span="5">
      <!-- <Button type="primary" icon="chevron-left" @click="gobackList">返回列表</Button> -->
      <Button type="success" @click="checkAllWorksState('1')">通过</Button>
      <Button type="error" @click="checkAllWorksState('3')">未通过</Button>
      </Col>
      <Col span="7" class="tc">
      <span class="activity-title">&nbsp;<span  v-show="title">活动名称：{{title}}</span></span>
      </Col>
      <Col span="5" offset='2'>
      <span class="pt5">作品类型：</span>
      <Select v-model="filter.type" style="width: 60%;" @on-change="getListData(true)">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in type" :label="item.type" :value="item.type" :key="item.type+index"></Option>
      </Select>
      </Col>
      <Col span="5">
      <span class="pt5">审核状态：</span>
      <Select v-model="filter.auditStatus" style="width: 60%;" @on-change="getListData(true)">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in auditStatus" :label="item.name" :value="item.id" :key="item.id+index"></Option>
      </Select>
      </Col>
    </Row>
    <Row class="pt10 pb10">
      <Table :columns="checkColumns" :data="checkDatas" @on-select="tableSelectCurrent" @on-select-all='tableSelectAll' @on-select-cancel="tableCancelCurrent" @on-selection-change="tableChange" highlight-row></Table>
    </Row>
    <Row class="tr pt5 pb10">
      <Page :total="tp" :current='cp' :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
    </Row>
    <!-- 审核作品modal -->
    <Modal v-model="checkWorksModal" class-name="check-works-modal" :mask-closable="false" width="700" title="审核作品">
      <div class="exam-model-body">
        <div style="min-height: 300px;">
          <Spin fix v-show="JSON.stringify(worksDatas) == '{}'" style="margin-top: 100px;">
            <Icon type="load-a" size='40' class="demo-spin-icon-load" style="margin-bottom: 20px;"></Icon>
            <div style="font-size: 16px;">后台正在解压文件，请您稍作等待，或先查看其它作品！</div>
          </Spin>
          <div v-show="JSON.stringify(worksDatas) != '{}'">
            <ActivityCheckWorksCommon :works-datas="worksDatas"></ActivityCheckWorksCommon>
            <Row style="padding:10px;">
              <Col span="6" v-for="(item,index) in worksDatas.workFiles" :key="index">
              <div class="check-works-list" @click.stop="previewWorks(item)">
                <img :src="baseUrl+item.detail" v-if="item.type==1" alt="作品" style="height:100%;width:100%;">
                <img src="/static/img/视频.png" v-if="item.type==2" alt="作品" style="height:100%;width:100%;">
                <img :src="baseUrl+item.pdfImage" v-if="item.type==3" alt="作品" style="height:100%;width:100%;">
                <span class="work-file-sign">{{item.type==1?'图片':item.type==2?'视频':'其它'}}</span>
                <Button v-if='item.type==1||item.type==3' type="primary" class="set-cover-btn" @click.stop="setWorkCover(item)">设为该作品封面</Button>
              </div>
              </Col>
            </Row>
          </div>
        </div>
      </div>
      <div slot="footer">
        <div v-show="JSON.stringify(worksDatas) != '{}'">
          <Button type="error" icon="close-round" @click="checkWorksState('3')" :disabled="worksDatas.status == 3">未通过</Button>
          <Button type="primary" icon='checkmark-round' @click="checkWorksState('1')">通过</Button>
        </div>
      </div>
    </Modal>
    <!-- 查看作品modal -->
    <Modal v-model="previewWorksModal" class-name="check-works-modal" :mask-closable="false" width="700" title="查看作品">
      <div class="exam-model-body">
        <div>
          <ActivityCheckWorksCommon :works-datas="worksDatas"></ActivityCheckWorksCommon>
          <Icon type="ios-download" size='20' style="margin: 20px 5px 0 20px;" color="#2d8cf0"></Icon><a style="font-size: 16px;" :href="baseUrl+worksDatas.workSite">点击下载该作品压缩包</a>
        </div>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="previewWorksModal=false">返回</Button>
      </div>
    </Modal>
    <Modal v-model="previewWorksModal2" :mask-closable="false" width="980" title="查看单个作品">
      <div class="exam-model-body" style="text-align: center;">
        <img style="max-width: 100%;height:auto;" v-if="previewSingleWorkData.detail&&previewSingleWorkData.type==1" :src="baseUrl+previewSingleWorkData.detail" alt="">
        <my-video v-if="previewSingleWorkData.detail&&previewSingleWorkData.type==2" :datas="baseUrl+previewSingleWorkData.detail"></my-video>
        <div style="position:relative;" v-if="previewSingleWorkData.detail&&previewSingleWorkData.type==3">
          <div style="position:absolute;z-index:99999;">
            <Button type="primary" :disabled="nowPage==1" @click="previewPrevPage">上一页</Button>
            <Button type="primary" :disabled="nowPage==totalPage" @click="previewNextPage">下一页</Button>
            <span style="font-size: 14px;margin-left: 10px;">当前第 {{nowPage}}页，共 {{totalPage}} 页</span>
          </div>
          <vuePdf :src="baseUrl+previewSingleWorkData.detail" :page="nowPage" @numPages="getTotalPage"></vuePdf>
          <div style="position:absolute;z-index:99999;right:0;bottom:0;">
            <Button type="primary" :disabled="nowPage==1" @click="previewPrevPage">上一页</Button>
            <Button type="primary" :disabled="nowPage==totalPage" @click="previewNextPage">下一页</Button>
            <span style="font-size: 14px;margin-left: 10px;">当前第 {{nowPage}}页，共 {{totalPage}} 页</span>
          </div>
        </div>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="previewWorksModal2=false">返回</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
import ActivityCheckWorksCommon from "@/components/bgSystem/ActivityCheckWorksCommon"
import myVideo from "@/components/common/Videos"
import vuePdf from 'vue-pdf'
export default {
  data() {
    return {
      //模态框状态
      checkWorksModal: false,
      previewWorksModal: false,
      previewWorksModal2: false,

      baseUrl: baseUrl,

      permissionList: window.sessionStorage.getItem('bg_user_permission'),

      //分页
      tp: 0,
      cp: 1,
      ep: 10,

      //定义pdf分页信息
      nowPage: 1,
      totalPage: 1,

      previewSingleWorkData: {},

      //表格选中的项
      selectItem: [],

      //查看和审核单个作品数据
      worksDatas: {},

      //筛选数据
      filter: {
        type: '0',
        auditStatus: '0'
      },
      type: [],
      //审核状态
      auditStatus: [{
        id: '1',
        name: '已通过'
      }, {
        id: '2',
        name: '未审核'
      }, {
        id: '3',
        name: '未通过'
      }],

      //表格配置
      checkColumns: [{
          type: 'selection',
          width: 60,
          align: 'center'
        },
        {
          title: '用户',
          key: 'username'
        }, {
          title: '作品名称',
          key: 'workName'
        }, {
          title: '提交时间',
          key: 'date',
          sortable: true,
          sortType: 'desc'
        }, {
          title: '作品类型',
          key: 'type'
        }, {
          title: '审核状态',
          // key: 'auditStatus',
          render: (h, params) => {
            if (params.row.status == 1) {
              return h('span', {}, '已通过')
            } else if (params.row.status == 2) {
              return h('span', {}, '未审核')
            } else if (params.row.status == 3) {
              return h('span', {}, '未通过')
            }
          }
        }, {
          title: '操作',
          key: 'action',
          width: 100,
          align: 'center',
          render: (h, params) => {
            if (params.row.status == 2 || params.row.status == 3) {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'primary',
                    disabled: this.permissionList.indexOf('works/audit') != -1 ? false : true
                  },
                  on: {
                    click: () => {
                      this.checkActivity(params.row.id, params.index)
                    }
                  }
                }, '审核'),
              ])
            } else {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'ghost',
                  },
                  on: {
                    click: () => {
                      this.previewActivity(params.row.id, params.index)
                    }
                  }
                }, '查看')
              ]);
            }
          }
        }
      ],

      //表格数据
      checkDatas: [],
      title: '',
    }
  },
  components: {
    ActivityCheckWorksCommon,
    myVideo,
    vuePdf
  },
  methods: {
    //获取pdf总页数
    getTotalPage(totalPage) {
      if (totalPage != undefined) {
        this.totalPage = totalPage;
      } else {
        this.totalPage = 0;
      }

    },
    //pdf上一页
    previewPrevPage() {
      this.nowPage -= 1;
    },
    //pdf下一页
    previewNextPage() {
      this.nowPage += 1;
    },
    //返回列表
    gobackList() {
      this.$router.push({
        path: '/wrap/activity/myactivity',
        query: {

        }
      })
    },
    //审核作品
    checkActivity(id, index) {
      this.checkWorksModal = true;
      this.$http.get('/v1/admin/works/info', {
        params: {
          workId: id,
          status: 2
        },
        timeout: 999999999999
      }).then((res) => {

        this.worksDatas = res.data.data;
      })
    },
    //查看作品
    previewActivity(id, index) {
      this.$http.get('/v1/admin/works/info', {
        params: {
          workId: id,
          status: 1
        }
      }).then((res) => {
        this.previewWorksModal = true;
        this.worksDatas = res.data.data;
      })
    },

    //预览单个作品
    previewWorks(item) {
      this.previewSingleWorkData = item;
      this.previewWorksModal2 = true;
    },

    //设置封面
    setWorkCover(item) {
      console.log(item)
      let params = '';
      if (item.type != 3) {
        params = {
          workId: item.workId,
          coverPath: item.detail
        }
      } else {
        params = {
          workId: item.workId,
          coverPath: item.pdfImage
        }
      }
      this.$http.post('/v1/admin/works/upload-cover-image', params).then((res) => {
        if (res.data.status == 200) {
          this.$Message.success({
            content: '作品封面设置成功',
            duration: 3
          })
        }
      })
    },

    //获取表格数据
    getListData(isSetCp = false) {
      if (isSetCp) {
        this.cp = 1;
      }
      this.$http.get('/v1/admin/works/list', {
        params: {
          activeId: this.$route.query.id,
          type: this.filter.type,
          status: this.filter.auditStatus,
          page: this.cp,
          pageSize: this.ep
        }
      }).then((res) => {
        let datas = res.data.data.rows;
        datas.forEach((item, index) => {
          if (item.status == 1) {
            this.$set(item, '_disabled', true)
          } else {
            this.$set(item, '_disabled', false)
          }
        })
        this.checkDatas = datas;
        this.tp = parseInt(res.data.data.total);
        this.title = res.data.data.title;
      })
    },
    currentPageChange(value) {
      this.cp = value;
      this.getListData();
    },
    eachPageChange(value) {
      this.ep = value;
      this.getListData();
    },

    //作品审核
    checkWorksState(state) {
      this.$http.post('/v1/admin/works/audit', {
        workId: [this.worksDatas.id],
        status: state,
        activityId: this.$route.query.id
      }).then((res) => {
        if (res.data.status == 200) {
          this.$Message.success('操作成功！');
          this.checkWorksModal = false;
          this.getListData();
        }
      })
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

    //批量通过或者拒绝作品
    checkAllWorksState(state) {
      if (this.selectItem.length != 0) {
        let worksIdsArr = [];
        this.selectItem.forEach((item, index) => {
          worksIdsArr.push(item.id)
        })
        this.$http.post('/v1/admin/works/audit', {
          workId: worksIdsArr,
          status: state,
          activityId: this.$route.query.id
        }).then((res) => {
          if (res.data.status == 200) {
            this.$Message.success('操作成功！');
            this.getListData();
          } else {
            this.$Message.error(res.data.message);
          }
        })
      } else {
        this.$Message.warning({
          content: '请至少勾选一个作品后再试！',
          duration: 4
        })
      }

    },

    //获取作品分类
    getActivityType() {
      this.$http.get('/v1/admin/works/work-type', {
        params: {
          activeId: this.$route.query.id
        }
      }).then((res) => {
        this.type = res.data.data;
      })
    }
  },
  mounted() {
    this.getListData();
    this.getActivityType();
  },
  watch: {
    'checkWorksModal': function(val, oldVal) {
      if (!val) {
        this.worksDatas = {};
      }
    },
    'previewWorksModal2': function(val, oldVal) {
      if (!val) {
        this.previewSingleWorkData = {};
        this.nowPage = 1;
        this.currentPage = 1;
      }
    },
    'previewWorksModal': function(val, oldVal) {
      if (!val) {
        this.worksDatas = {};
      }
    }
  }
}

</script>
<style lang="less">
.activity-check-container {
  .activity-title {
    display: inline-block;
    width: 100%;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    font-size: 16px;
    font-weight: bold;
    line-height: 2;
  }
}

.check-works-list {
  height: 120px;
  margin-right: 5px;
  margin-bottom: 5px;
  border: 1px solid #dddee1;
  position: relative;
  cursor: pointer;
  border-radius: 4px;
  overflow: hidden;

  .set-cover-btn {
    position: absolute;
    top: 4px;
    left: 20px;
    display: none;
  }

  &:hover {
    border-color: #2d8cf0;
    .work-file-sign {
      color: #2d8cf0;
    }
    .set-cover-btn {
      display: block;
    }
  }
  .work-file-sign {
    padding: 2px 4px;
    /* background-color: # */
    position: absolute;
    right: 2px;
    bottom: 2px;
    background-color: #fff;
  }
}

.demo-spin-icon-load {
  animation: ani-demo-spin 1s linear infinite;
}

@keyframes ani-demo-spin {
  from {
    transform: rotate(0deg);
  }
  50% {
    transform: rotate(180deg);
  }
  to {
    transform: rotate(360deg);
  }
}

</style>
