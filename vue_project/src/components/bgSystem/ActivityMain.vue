<template>
  <div>
    <Row v-if="isSuper=='9844f81e1408f6ecb932137d33bed7cfdcf518a3'">
      <Col span="5">
      <span class="pt5">活动类型：</span>
      <Select v-model="filter.eventType" style="width: 60%;" @on-change="eventTypeChange">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in eventTypes" :label="item.name" :value="item.id" :key="item.id+index"></Option>
      </Select>
      </Col>
      <Col span="5">
      <span class="pt5">活动状态：</span>
      <Select v-model="filter.eventStatus" style="width: 60%;" @on-change="eventStatusChange">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in eventStatus" :label="item.name" :value="item.id" :key="item.id+index"></Option>
      </Select>
      </Col>
      <Col span="5">
      <span class="pt5">审核状态：</span>
      <Select v-model="filter.auditStatus" style="width: 60%;" @on-change="auditStatusChange">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in auditStatus" :label="item.name" :value="item.id" :key="item.id+index"></Option>
      </Select>
      </Col>
      <Col span="5">
      <span class="pt5">发起机构：</span>
      <Select v-model="filter.company" placeholder='默认全部,支持检索' style="width: 60%;" @on-change="companyChange" filterable remote :remote-method="remoteMethod" :loading="loading" not-found-text="" clearable>
        <Option v-for="(item,index) in companyListFilter" :value="item.name" :key="item.id+index">{{item.name}}</Option>
      </Select>
      </Col>
      <Col span="4" class="tr fwb" style="font-size: 16px;">
      <span class="dib pt5">活动数：</span>
      <span class="dib pt5 pr5" style="color:#2d8cf0;font-size: 18px;">{{tp}}</span>
      </Col>
    </Row>
    <Row>
      <Spin fix v-show="loadingData1">
        <Icon type="load-c" size=18 class="demo-spin-icon-load"></Icon>
        <div>数据加载中...</div>
      </Spin>
      <Row class="pt10 pb10" v-if="isSuper=='9844f81e1408f6ecb932137d33bed7cfdcf518a3'" v-show="!loadingData1">
        <Table :columns="accountColumns" :data="accountDatas" highlight-row></Table>
      </Row>
    </Row>
    <Row v-if="isSuper!='9844f81e1408f6ecb932137d33bed7cfdcf518a3'">
      <Col span="6">
      <Button type="primary" @click="addNewActivity" v-if="permissionList.indexOf('activity/add')!=-1">创建</Button>
      &nbsp;
      </Col>
      <Col span="6">
      <span class="pt5">活动类型：</span>
      <Select v-model="filter.eventType" style="width: 60%;" @on-change="eventTypeChange">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in eventTypes" :label="item.name" :value="item.id" :key="item.id+index"></Option>
      </Select>
      </Col>
      <Col span="6">
      <span class="pt5">活动状态：</span>
      <Select v-model="filter.eventStatus" style="width: 60%;" @on-change="eventStatusChange">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in eventStatus" :label="item.name" :value="item.id" :key="item.id+index"></Option>
        <Option value="4">未发布</Option>
      </Select>
      </Col>
      <Col span="6">
      <span class="pt5">审核状态：</span>
      <Select v-model="filter.auditStatus" style="width: 60%;" @on-change="auditStatusChange">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in auditStatus" :label="item.name" :value="item.id" :key="item.id+index"></Option>
      </Select>
      </Col>
    </Row>
    <Row>
      <Spin fix v-show="loadingData">
        <Icon type="load-c" size=18 class="demo-spin-icon-load"></Icon>
        <div>数据加载中...</div>
      </Spin>
      <Row class="pt10 pb10" v-if="isSuper!='9844f81e1408f6ecb932137d33bed7cfdcf518a3'" v-show="!loadingData">
        <Table :columns="accountColumns1" :data="accountDatas" highlight-row></Table>
      </Row>
    </Row>
    <Row class="tr pt5 pb10">
      <Page :total="tp" :current="cp" :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
    </Row>
  </div>
</template>
<script>
export default {
  data() {
    const filters = window.sessionStorage.getItem('bg_user_filter');
    return {
      //筛选条件
      filter: {
        eventType: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).eventType ? JSON.parse(filters).eventType : '0',
        auditStatus: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).auditStatus ? JSON.parse(filters).auditStatus : '0',
        eventStatus: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).eventStatus ? JSON.parse(filters).eventStatus : '0',
        company: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).company ? JSON.parse(filters).company : '',
      },

      permissionList: window.sessionStorage.getItem('bg_user_permission'),



      isSuper: window.sessionStorage.getItem('isSuper'),

      loading: false,
      companyListFilter: [],

      loadingData: false,
      loadingData1: false,

      //分页
      tp: 0,
      cp: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).cp ? parseInt(JSON.parse(filters).cp) : 1,
      ep: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).ep ? parseInt(JSON.parse(filters).ep) : 10,

      userInfo: JSON.parse(window.sessionStorage.getItem('bg_user_info')),

      //活动类型
      eventTypes: [{
        id: '1',
        name: '大赛'
      }, {
        id: '2',
        name: '问卷'
      }],

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

      //机构
      companyList: [],

      //超管
      //表格配置
      accountColumns: [
        // {
        //   type: 'selection',
        //   width: 60,
        //   align: 'center'
        // }, 
        {
          title: '活动类型',
          render: (h, params) => {
            if (params.row.type == 1) {
              return h('span', '大赛')
            } else if (params.row.type == 2) {
              return h('span', '问卷')
            }
          }
        }, {
          title: '申请人',
          key: 'userName'
        }, {
          title: '发起机构',
          key: 'companyName'
        }, {
          title: '活动名称',
          key: 'name'
        }, {
          title: '申请时间',
          key: 'applicationDate',
          sortable: true,
          sortType: 'desc'
        }, {
          title: '活动状态',
          render: (h, params) => {
            if (params.row.status == 1) {
              return h('span', {}, '未开始')
            } else if (params.row.status == 2) {
              return h('span', {}, '进行中')
            } else if (params.row.status == 3) {
              return h('span', {}, '已结束')
            }
          }
        }, {
          title: '审核状态',
          render: (h, params) => {
            if (params.row.auditState == 1) {
              return h('span', '已通过')
            } else if (params.row.auditState == 2) {
              return h('span', '未审核')
            } else if (params.row.auditState == 3) {
              return h('span', '未通过')
            }
          }
        }, {
          title: '操作',
          key: 'action',
          width: 160,
          align: 'center',
          render: (h, params) => {
            if (params.row.status == 1) {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'primary',
                  },
                  on: {
                    click: () => {
                      this.auditActivity(params.row, params.index)
                    }
                  }
                }, '审核'),
                h('Button', {
                  props: {
                    type: params.row.isDeleted == 0 ? 'error' : 'info',
                  },
                  on: {
                    click: () => {
                      this.publishActivity(params.row, params.index)
                    }
                  }
                }, params.row.isDeleted == 0 ? '下架' : '上架'),
              ])
            } else {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'ghost',
                  },
                  on: {
                    click: () => {
                      this.previewActivity(params.row, params.index)
                    }
                  }
                }, '查看'),
                h('Button', {
                  props: {
                    type: params.row.isDeleted == 0 ? 'error' : 'info',
                  },
                  on: {
                    click: () => {
                      this.removeActivity(params.row, params.index)
                    }
                  }
                }, params.row.isDeleted == 0 ? '下架' : '上架'),

              ]);
            }
          }
        }
      ],


      //表格数据
      accountDatas: [],

      //任务管理员
      //表格配置
      accountColumns1: [{
        title: '活动类型',
        render: (h, params) => {
          if (params.row.type == 1) {
            return h('span', '大赛')
          } else if (params.row.type == 2) {
            return h('span', '问卷')
          }
        }
      }, {
        title: '活动名称',
        key: 'name'
      }, {
        title: '申请时间',
        key: 'applicationDate',
        sortable: true,
        sortType: 'desc'
      }, {
        title: '活动状态',
        // key: 'eventStatus',
        render: (h, params) => {
          if (params.row.status == 1) {
            return h('span', '未开始')
          } else if (params.row.status == 2) {
            return h('span', '进行中')
          } else if (params.row.status == 3) {
            return h('span', '已结束')
          }
        }
      }, {
        title: '审核状态',
        // key: 'auditStatus',
        render: (h, params) => {
          if (params.row.auditState == 1) {
            return h('span', '已通过')
          } else if (params.row.auditState == 2) {
            return h('span', '未审核')
          } else if (params.row.auditState == 3) {
            return h('span', '未通过')
          } else if (params.row.auditState == 4) {
            return h('span', '未发布')
          }
        }
      }, {
        title: '操作',
        key: 'action',
        width: 275,
        render: (h, params) => {
          if (params.row.auditState == 1) {
            if (params.row.type == 2) {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'ghost',
                  },
                  on: {
                    click: () => {
                      this.previewActivity(params.row, params.index)
                    }
                  }
                }, '查看'),
                h('Button', {
                  props: {
                    type: 'primary',
                  },
                  on: {
                    click: () => {
                      this.checkActivityStatistics(params.row, params.index)
                    }
                  }
                }, '统计'),
              ])
            } else {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'ghost',
                  },
                  on: {
                    click: () => {
                      this.previewActivity(params.row, params.index)
                    }
                  }
                }, '查看'),

                h('Button', {
                  props: {
                    type: 'info',
                    disabled: this.permissionList.indexOf('works/audit') != -1 && params.row.status != 3 ? false : true
                  },
                  on: {
                    click: () => {
                      this.checkActivity(params.row.id, params.index)
                    }
                  }
                }, '作品审核'),

                h('Button', {
                  props: {
                    type: 'success',
                    disabled: this.permissionList.indexOf('works/prize-list') != -1 && params.row.status != 3 ? false : true
                  },
                  on: {
                    click: () => {
                      this.setActivity(params.row.id, params.index)
                    }
                  }
                }, '奖项管理'),
              ])
            }
          } else {
            return h('div', [
              h('Button', {
                props: {
                  type: 'primary',
                },
                on: {
                  click: () => {
                    this.editActivity(params.row, params.index)
                  }
                }
              }, '编辑'),
              h('Button', {
                props: {
                  type: 'error',
                },
                on: {
                  click: () => {
                    this.removeActivity(params.row, params.index)
                  }
                }
              }, '删除')
            ]);
          }
        }
      }],

    }
  },
  methods: {

    //筛选学校
    remoteMethod(query) {
      if (query !== '') {
        this.loading = true;
        this.companyListFilter = this.companyList.filter(item => item.name.indexOf(query) > -1);
        this.loading = false;
      } else {
        this.companyListFilter = [{
          id: '0',
          name: '全部'
        }];
      }
    },
    // 审核
    auditActivity(datas, index) {
      this.$router.push({
        path: '/wrap/activity/myactivity/audit',
        query: {
          id: datas.id,
          type: datas.type
        }
      })
    },
    //删除活动
    removeActivity(datas, index) {
      this.$Modal.confirm({
        title: '操作确认',
        content: '<h3>该操作将删除该活动，请确认</h3>',
        onOk: () => {
          this.$http.post('/v1/admin/activity/activity-delete', {
            activityId: datas.id
          }).then((res) => {
            if (res.data.status == 200) {
              this.getListData(false);
              this.$Message.success({
                content: '删除操作成功',
                duration: 4
              });

            }
          })
        },
      });
    },
    //预览
    previewActivity(datas, index) {
      console.log(datas, index)
      this.$router.push({
        path: '/wrap/activity/myactivity/preview',
        query: {
          id: datas.id,
          type: datas.type
        }
      })
    },
    //编辑
    editActivity(datas, index) {
      this.$router.push({
        path: '/wrap/activity/myactivity/edit',
        query: {
          id: datas.id,
          type: datas.type
        }
      })
    },
    //新建
    addNewActivity() {
      this.$router.push({
        path: '/wrap/activity/myactivity/add',
        query: {

        }
      })
    },
    //审核
    checkActivity(id, index) {
      this.$router.push({
        path: '/wrap/activity/myactivity/check',
        query: {
          id: id
        }
      })
    },
    //设置奖项
    setActivity(id, index) {
      this.$router.push({
        path: '/wrap/activity/myactivity/awards',
        query: {
          id: id
        }
      })
    },

    //上下架活动
    publishActivity(row, index) {
      if (row.isDeleted == 0) {
        //下架活动
        this.$Modal.confirm({
          title: '操作确认',
          content: '<h3>该操作将下架该活动，请确认</h3>',
          onOk: () => {
            this.$http.post('/v1/admin/activity/update-state', {
                activityId: row.id,
                state: 1
              })
              .then((res) => {
                if (res.data.status == 200) {
                  this.$Message.success('操作成功');
                  this.accountDatas[index].isDeleted = 1;
                }
              })
          },
        });

      } else {
        this.$Modal.confirm({
          title: '操作确认',
          content: '<h3>该操作将上架该活动，请确认</h3>',
          onOk: () => {
            this.$http.post('/v1/admin/activity/update-state', {
                activityId: row.id,
                state: 0
              })
              .then((res) => {
                if (res.data.status == 200) {
                  this.$Message.success('操作成功');
                  this.accountDatas[index].isDeleted = 0;
                }
              })
          },
        });
        //上架活动

      }
    },

    //获取列表数据
    getListData(isSetCp = false) {
      if (isSetCp) {
        this.cp = 1;
      }
      if (this.isSuper == '9844f81e1408f6ecb932137d33bed7cfdcf518a3') {
        this.loadingData1 = true;
      } else {
        this.loadingData = true;
      }

      this.$http.post('/v1/admin/activity/activity-index', {
        page: this.cp,
        pageSize: this.ep,
        condition: {
          type: this.filter.eventType,
          auditState: this.filter.auditStatus,
          status: this.filter.eventStatus,
          companyName: this.filter.company == "全部" ? 0 : this.filter.company
        },
        userId: this.userInfo.id
      }).then((res) => {
        if (this.isSuper == '9844f81e1408f6ecb932137d33bed7cfdcf518a3') {
          this.loadingData1 = false;
        } else {
          this.loadingData = false;
        }
        this.tp = parseInt(res.data.data.total);
        this.accountDatas = res.data.data.rows;
      })
    },

    currentPageChange(value) {
      this.cp = value;
      this.updateFilter({ 'cp': this.cp });
      this.getListData();
    },

    eachPageChange(value) {
      this.ep = value;
      this.updateFilter({ 'ep': this.ep })
      this.getListData();
    },

    eventTypeChange() {
      this.updateFilter({ 'eventType': this.filter.eventType })
      this.getListData(true)
    },

    eventStatusChange() {
      this.updateFilter({ 'eventStatus': this.filter.eventStatus })
      this.getListData(true)
    },

    auditStatusChange() {
      this.updateFilter({ 'auditStatus': this.filter.auditStatus })
      this.getListData(true)
    },

    companyChange() {
      this.updateFilter({ 'company': this.filter.company })
      this.getListData(true)
    },

    //获取机构列表
    getCompanyList() {
      this.$http.get('/v1/admin/user/agency-list')
        .then((res) => {
          this.companyList = res.data.data.data;
          this.companyList.unshift({
            id: '0',
            name: '全部'
          })
          console.log(res.data.data.data)
        })
    },

    //问卷统计
    checkActivityStatistics(row, index) {
      var newWin = window.open('loading');
      this.$http.get('/v1/admin/activity/get-info-admin', {
        params: {
          type: row.type,
          activityId: row.id
        }
      }).then((res) => {
        if (res.data.status == 200) {
          let detailArr = res.data.data.activity.questionAnswerDetail.split('/');
          let detailId = detailArr[4].split('?')[0];
          let userInfo = JSON.parse(window.sessionStorage.getItem('bg_user_info'));
          let username = userInfo.username;
          let password = userInfo.password;

          // 重定向到目标页面  
          newWin.location.href = 'http://answer.jqweike.com/index.php/admin/authentication/sa/login?user=' + username + '&password=' + password + '&survey=' + detailId;
        }
      })
    }
  },
  beforeRouteLeave(to, from, next) {
    if (to.fullPath.indexOf('/myactivity/preview') != -1 || to.fullPath.indexOf('/myactivity/audit') != -1 || to.fullPath.indexOf('/myactivity/edit') != -1 || to.fullPath.indexOf('/myactivity/awards') != -1 || to.fullPath.indexOf('/myactivity/add') != -1 || to.fullPath.indexOf('/myactivity/check') != -1) {
      window.sessionStorage.setItem('bg_user_filter', JSON.stringify({
        eventType: this.filter.eventType,
        auditStatus: this.filter.auditStatus,
        eventStatus: this.filter.eventStatus,
        company: this.filter.company,
        searchKey: this.searchKey,
        cp: this.cp,
        ep: this.ep,
      }))
    } else {
      window.sessionStorage.setItem('bg_user_filter', '')
    }
    next();
  },
  mounted() {
    this.getListData();
    this.getCompanyList();
  }
}

</script>
<style lang="less">


</style>
