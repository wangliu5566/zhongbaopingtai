<template>
  <div class="activity-awards-container">
    <Row>
      <Col span="3">
      <!-- <Button type="primary" icon="chevron-left" @click="gobackList">返回列表</Button> -->
      <!-- <Button type="primary">设置奖项</Button> -->
      </Col>
      <Col span="5">
      <span class="pt5">颁奖状态：</span>
      <Select v-model="filter.status" style="width: 60%;" @on-change="getListData(true)">
        <Option value="0">全部</Option>
        <Option value="1">未颁奖</Option>
        <Option value="2">已颁奖</Option>
      </Select>
      </Col>
      <Col span="5">
      <span class="pt5">作品类型：</span>
      <Select v-model="filter.type" style="width: 60%;" @on-change="getListData(true)">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in types" :label="item.type" :value="item.type" :key="item.type+index"></Option>
      </Select>
      </Col>
      <Col span="5">
      <span class="pt5">排序：</span>
      <Select v-model="filter.sortType" style="width: 60%;" @on-change="sortTypeChange">
        <Option value="hits">点赞数</Option>
        <Option value="share">分享数</Option>
      </Select>
      </Col>
      <Col span="9">
      <Input v-model="searchKey" icon="ios-search-strong" placeholder="搜索作品 / 作者" @on-click="searchInfo"></Input>
      </Col>
    </Row>
    <Row class="pt10 pb10">
      <Table :columns="accountColumns" :data="accountDatas" highlight-row></Table>
    </Row>
    <Row class="tr pt5 pb10">
      <Page :total="tp" :current='cp' :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
    </Row>
    <!-- 查看作品modal -->
    <Modal v-model="previewWorksModal" :mask-closable="false" width="600" title="查看作品">
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
    <!-- 设置奖项modal -->
    <Modal v-model="setActivityAwardsModal" :mask-closable="false" width="500" title="设置奖项">
      <div class="exam-model-body">
        <div>
          <Row class="pb20">
            <p class="awards-title">奖项类别：<span>{{awardsDatas.type}}</span></p>
          </Row>
          <template v-for="(item,index) in awardsDatas.awards">
            <Row class="pb10">
              <Col span='8' offset="4">
              <Button type="ghost" long @click="sureSetPrize(item)" v-if="item.isset==0">{{item.prizeName}}</Button>
              <Button type="primary" long @click="quitSetPrize(item)" v-if="item.isset!=0">{{item.prizeName}}</Button>
              </Col>
              <Col span='8' class="awards-num"><span>{{item.winners}}</span> / {{item.totalPeople}}</Col>
            </Row>
          </template>
          <Row class="pt20" style="margin-left:20px;">
            <span>提示：<Button type="primary" style="margin-right:10px;">&times;&times;奖</Button>类型的为该作品目前<span style="color:red;"> 已获得 </span>的奖项</span>
          </Row>
          <!--           <Row>
            <Col span='8' offset="4">
            <Button type="ghost" long>不设奖</Button>
            </Col>
          </Row> -->
        </div>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="setActivityAwardsModal=false">返回</Button>
        <!-- <Button type="primary">确认设置</Button> -->
      </div>
    </Modal>
  </div>
</template>
<script>
import ActivityCheckWorksCommon from "@/components/bgSystem/ActivityCheckWorksCommon"
export default {
  data() {
    return {
      // 查看作品
      previewWorksModal: false,
      setActivityAwardsModal: false,
      baseUrl: baseUrl,

      permissionList: window.sessionStorage.getItem('bg_user_permission'),

      //分页
      tp: 0,
      cp: 1,
      ep: 10,

      //查看作品数据
      worksDatas: {},

      //奖项数据
      awardsDatas: {
        type: '',
        workId: '',
        awards: []
      },

      //筛选条件
      filter: {
        status: '0',
        type: '0',
        sortType: 'hits'
      },
      searchKey: '',
      // 作品类型
      types: [],

      //表格配置
      accountColumns: [],
      //表格数据
      accountDatas: [],
    }
  },
  methods: {
    //获取作品分类
    getActivityType() {
      this.$http.get('/v1/admin/works/work-type', {
        params: {
          activeId: this.$route.query.id
        }
      }).then((res) => {
        this.types = res.data.data;
      })
    },
    searchInfo() {
      if (this.searchKey != '') {
        this.cp = 1;
        this.getListData();
      } else {
        this.$Message.warning('搜索关键字不能为空')
      }
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
    //获取奖项
    setActivityAwards(item, index) {
      this.awardsDatas.type = item.type;
      this.awardsDatas.workId = item.id;
      this.$http.get('/v1/admin/works/active-prize', {
        params: {
          workId: item.id,
          activeId: this.$route.query.id
        }
      }).then((res) => {
        if (res.data.status == 200) {
          this.awardsDatas.awards = res.data.data;
          this.setActivityAwardsModal = true;
        }
      })
    },

    //确认设置奖项
    sureSetPrize(item) {
      if (item.totalPeople > item.winners) {
        this.$http.post('/v1/admin/works/set-prize', {
          workId: this.awardsDatas.workId,
          prizeId: item.id
        }).then((res) => {
          if (res.data.status == 200) {
            this.$Message.success({
              content: '奖项设置成功',
              duration: 4
            })
            this.$http.get('/v1/admin/works/active-prize', {
              params: {
                workId: this.awardsDatas.workId,
                activeId: this.$route.query.id
              }
            }).then((res) => {
              if (res.data.status == 200) {
                this.awardsDatas.awards = res.data.data;
              }
            })
          } else if (res.data.status == 1005) {
            this.$Message.error({
              content: '该奖项名额已满',
              duration: 4
            })
          }
        })
      } else {
        this.$Message.error({
          content: '该奖项名额已满',
          duration: 4
        })
      }

    },

    //取消奖项
    quitSetPrize(item) {
      this.$http.post('/v1/admin/works/set-prize', {
        workId: this.awardsDatas.workId,
        prizeId: item.id
      }).then((res) => {
        if (res.data.status == 200) {
          this.$Message.success({
            content: '奖项取消成功',
            duration: 4
          })
          this.$http.get('/v1/admin/works/active-prize', {
            params: {
              workId: this.awardsDatas.workId,
              activeId: this.$route.query.id
            }
          }).then((res) => {
            if (res.data.status == 200) {
              this.awardsDatas.awards = res.data.data;
            }
          })
        }
      })
    },

    //分页
    currentPageChange(value) {
      this.cp = value;
      this.getListData();
    },
    eachPageChange(value) {
      this.ep = value;
      this.getListData();
    },

    getListData(isSetCp = false) {
      if (isSetCp) {
        this.cp = 1;
      }
      this.$http.get('/v1/admin/works/prize-list', {
        params: {
          activeId: this.$route.query.id,
          keyword: this.searchKey,
          prize: this.filter.status,
          type: this.filter.type,
          sort: this.filter.sortType,
          page: this.cp,
          pageSize: this.ep
        }
      }).then((res) => {
        this.tp = parseInt(res.data.data.total);
        this.accountDatas = res.data.data.rows;
      })
    },
    setTableColumns() {
      if (this.filter.sortType == "hits") {
        this.accountColumns = [
          // {
          //   type: 'selection',
          //   width: 60,
          //   align: 'center'
          // }, 
          {
            title: '作者姓名',
            key: 'username'
          }, {
            title: '作品名称',
            key: 'workName'
          }, {
            title: '作品类型',
            key: 'type'
          }, {
            title: '点赞数',
            key: 'hits',
            sortable: true,
            sortType: 'desc'
          }, {
            title: '获奖状态',
            // key: 'eventStatus',
            render: (h, params) => {
              if (params.row.prize != '') {
                return h('span', params.row.prize)
              } else {
                return h('span', '未获奖')
              }
            }
          }, {
            title: '操作',
            key: 'action',
            width: 200,
            align: 'center',
            render: (h, params) => {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'primary',
                    disabled:this.permissionList.indexOf('works/set-prize')!=-1?false:true
                  },
                  on: {
                    click: () => {
                      this.setActivityAwards(params.row, params.index)
                    }
                  }
                }, '设置奖项'),
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
              ])
            }
          }
        ]
      } else if (this.filter.sortType == "share") {
        this.accountColumns = [
          // {
          //   type: 'selection',
          //   width: 60,
          //   align: 'center'
          // }, 
          {
            title: '作者姓名',
            key: 'username'
          }, {
            title: '作品名称',
            key: 'workName'
          }, {
            title: '作品类型',
            key: 'type'
          }, {
            title: '分享数',
            key: 'share',
            sortable: true,
            sortType: 'desc'
          }, {
            title: '获奖状态',
            render: (h, params) => {
              if (params.row.prize != '') {
                return h('span', params.row.prize)
              } else {
                return h('span', '未获奖')
              }
            }
          }, {
            title: '操作',
            key: 'action',
            width: 200,
            align: 'center',
            render: (h, params) => {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'primary',
                    disabled:this.permissionList.indexOf('works/set-prize')!=-1?false:true
                  },
                  on: {
                    click: () => {
                      this.setActivityAwards(params.row, params.index)
                    }
                  }
                }, '设置奖项'),
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
              ])
            }
          }
        ]
      }
    },
    sortTypeChange() {
      this.setTableColumns();
      this.getListData(true);

    },


  },
  components: {
    ActivityCheckWorksCommon
  },
  mounted() {
    this.getListData();
    this.setTableColumns();
    this.getActivityType();
  },
  watch: {
    'searchKey': function(val, oldVal) {
      if (!val && oldVal) {
        this.cp = 1;
        this.getListData();
      }
    },
    'setActivityAwardsModal': function(val, oldVal) {
      if (!val) {
        this.awardsDatas = {
            type: '',
            workId: '',
            awards: []
          },
          this.getListData();
      }
    }
  }
}

</script>
<style lang="less">
.awards-title {
  text-align: center;
  font-size: 16px;

  span {
    color: #5c9acf;
    font-weight: bold;
    font-size: 18px;
  }
}

.awards-num {
  font-size: 16px;
  line-height: 32px;
  padding-left: 40px;
  span {
    color: #5c9acf;
  }
}

.activity-awards-container {}

</style>
