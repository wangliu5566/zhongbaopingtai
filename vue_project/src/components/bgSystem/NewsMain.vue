<template>
  <div>
    <Row>
      <Col span="8">
      <Button type="primary" @click="addNewNews" v-if="permissionList.indexOf('news/news-add')!=-1?true:false">
        <Icon type="plus-round"></Icon> 添加</Button>
      <Button type="error" @click="deleteNews" v-if="permissionList.indexOf('news/news-delete')!=-1?true:false">
        <Icon type="close-round"></Icon> 批量删除</Button>
      &nbsp;
      </Col>
      <Col span="8" class="pr5">
      <Select v-model="event" style="width: 60%;" class="pull-right" @on-change="eventChange">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in eventList" :label="item.name" :value="item.id" :key="item.id+index"></Option>
      </Select>
      <span class="pull-right pt5">所属活动：</span>
      </Col>
      <Col span="8">
      <Input v-model="searchKey" icon="ios-search-strong" placeholder="搜索文章标题" @on-click="searchInfo"></Input>
      </Col>
    </Row>
    <Row>
      <Spin fix v-show="loadingData">
        <Icon type="load-c" size=18 class="demo-spin-icon-load"></Icon>
        <div>数据加载中...</div>
      </Spin>
      <Row class="pt10 pb10" v-show="!loadingData">
        <Table :columns="accountColumns" :data="accountDatas" @on-select="tableSelectCurrent" @on-select-all='tableSelectAll' @on-select-cancel="tableCancelCurrent" @on-selection-change="tableChange" highlight-row></Table>
      </Row>
    </Row>
    <Row class="tr pt5 pb10">
      <Page :total="tp" :current='cp' :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
    </Row>
    <Modal v-model="previewNewsModal" width="1260" title="新闻预览">
      <div class="editAcademy-model-body">
        <news-preview :newsData="newsPreviewData"></news-preview>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="previewNewsModal=false">返回</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
import NewsPreview from "@/components/bgSystem/NewsPreview"
export default {
  components: {
    NewsPreview
  },
  data() {
    const filters = window.sessionStorage.getItem('bg_user_filter');
    return {
      searchKey: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).searchKey ? JSON.parse(filters).searchKey : '',

      //所属活动
      event: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).event ? JSON.parse(filters).event : '0',

      eventList: [],

      previewNewsModal: false,

      loadingData: false,

      permissionList: window.sessionStorage.getItem('bg_user_permission'),

      //表格选中的项
      selectItem: [],

      //分页
      tp: 0,
      cp: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).cp ? parseInt(JSON.parse(filters).cp) : 1,
      ep: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).ep ? parseInt(JSON.parse(filters).ep) : 10,

      //表格配置
      accountColumns: [{
        type: 'selection',
        width: 60,
        align: 'center'
      }, {
        title: '标题',
        key: 'title'
      }, {
        title: '发布者',
        key: 'username'
      }, {
        title: '所属活动',
        key: 'name'
      }, {
        title: '发布时间',
        key: 'publishTime',
        sortable: true,
        sortType: 'desc'
      }, {
        title: '操作',
        key: 'action',
        width: 240,
        align: 'center',
        render: (h, params) => {
          return h('div', [
            h('Button', {
              props: {
                type: 'ghost',
              },
              on: {
                click: () => {
                  this.previewNews(params.row.id, params.index)
                }
              }
            }, '预览'),
            h('Button', {
              props: {
                type: 'primary',
                disabled: this.permissionList.indexOf('news/modify-news') != -1 ? false : true
              },
              on: {
                click: () => {
                  this.editNews(params.row.id, params.index)
                }
              }
            }, '编辑'),
            h('Button', {
              props: {
                type: 'error',
                disabled: this.permissionList.indexOf('news/news-delete') != -1 ? false : true
              },
              on: {
                click: () => {
                  this.removeNews(params.row.id, params.index)
                }
              }
            }, '删除')
          ]);
        }
      }],

      //表格数据
      accountDatas: [],

      //新闻预览
      newsPreviewData:{}

    }
  },
  methods: {
    searchInfo() {
      if (this.searchKey != '') {
        this.cp = 1;
        this.updateFilter({ 'searchKey': this.searchKey })
        this.getListData();
      } else {
        this.$Message.warning('搜索关键字不能为空')
      }
    },
    //新增新闻
    addNewNews() {
      this.$router.push({
        path: '/wrap/content/news/add',
        query: {

        }
      })
    },
    //编辑新闻
    editNews(id, index) {
      this.$router.push({
        path: '/wrap/content/news/edit',
        query: {
          id: id
        }
      })
    },
    //预览新闻
    previewNews(id, index) {
      this.$http.get("/v1/frontend/news/get-info", {
        params: {
          newsId: id
        }
      }).then((res)=>{
        this.newsPreviewData = res.data.data[0];
        this.newsPreviewData.publishTime = this.secondsToNormalDate(this.newsPreviewData.publishTime * 1000)
        this.previewNewsModal = true;
      })
    },
    //删除新闻
    removeNews(id, index) {
      let newsIds = [id];
      this.$Modal.confirm({
        title: '确认操作',
        content: '<h3>该操作将删除新闻，请确认执行此操作</h3>',
        onOk: () => {
          this.deleteNewsById(newsIds);
        },
      });
    },

    //批量删除新闻
    deleteNews() {
      let newsIds = [];
      if (this.selectItem.length != 0) {
        this.$Modal.confirm({
          title: '确认操作',
          content: '<h3>该操作将删除新闻，请确认执行此操作</h3>',
          onOk: () => {
            this.selectItem.forEach((item, index) => {
              newsIds.push(item.id)
            })
            this.deleteNewsById(newsIds);
          },
        });
      } else {
        this.$Message.warning('请至少勾选一条新闻后再试！')
      }
    },

    //删除新闻接口
    deleteNewsById(newsIds) {
      this.$http.post('/v1/admin/news/news-delete', {
        newsId: newsIds
      }).then((res) => {
        if (res.data.status == 200) {
          this.$Message.success('操作成功');
          this.getListData();
        }
      })
    },


    //获取所有活动
    getActivityList() {
      this.$http.get('/v1/admin/activity/get-all-activity', {
        params: {
          userId: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id,
          // auditState: 
        }
      }).then((res) => {
        if (res.data.data) {
          res.data.data.forEach(function(item, index) {
            item.id = item.id.toString();
          });
        }
        this.eventList = res.data.data;
      })
    },

    eventChange() {
      this.updateFilter({ 'event': this.event })
      this.getListData(true)
    },

    //分页
    currentPageChange(value) {
      this.cp = value;
      this.updateFilter({ 'cp': this.cp });
      this.getListData();
    },

    eachPageChange(value) {
      this.ep = value;
      this.updateFilter({ 'ep': this.ep });
      this.getListData();
    },

    // 获取新闻列表
    getListData(isSetCp = false) {
      if (isSetCp) {
        this.cp = 1;
      }
      this.loadingData = true;
      this.$http.post('/v1/admin/news/view-news', {
        page: this.cp,
        pageSize: this.ep,
        condition: {
          title: this.searchKey,
          activityId: this.event,
          userId: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id
        }
      }).then((res) => {
        this.loadingData = false;
        this.tp = parseInt(res.data.data.total);
        this.accountDatas = res.data.data.rows;
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


  },
  beforeRouteLeave(to, from, next) {
    if (to.fullPath.indexOf('/news/add') != -1 || to.fullPath.indexOf('/news/edit') != -1) {
      window.sessionStorage.setItem('bg_user_filter', JSON.stringify({
        event: this.event,
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
    this.getActivityList();
    this.getListData();
  },
  watch: {
    'searchKey': function(val, oldVal) {
      if (!val && oldVal) {
        this.cp = 1;
        this.updateFilter({ 'searchKey': '' })
        this.getListData();
      }
    }
  }
}

</script>
<style lang="less">


</style>
