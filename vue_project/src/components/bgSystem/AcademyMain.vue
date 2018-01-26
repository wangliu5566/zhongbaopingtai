<template>
  <div class="academy-main">
    <Row>
      <Col span='8'>
      <Button type="primary" @click="addNewAcademy">
        <Icon type="plus-round"></Icon> 新增机构</Button>
      </Button>
      <Button type="error" @click="deleteAcademyByBatches">
        <Icon type="close-round"></Icon> 批量删除</Button>
      </Col>
      <Col span="8" class="pr5">
      <Select v-model="filter.region" style="width: 60%;" @on-change="regionChange" class="pull-right">
        <Option value="0">全部</Option>
        <Option v-for="(item,index) in regionList" :label="item" :value="item" :key="item+index"></Option>
      </Select>
      <span class="pt5 pull-right">地区：</span>
      </Col>
      <Col span="8">
      <Input v-model="filter.searchKey" icon="ios-search-strong" placeholder="搜索机构名称" @on-click="searchInfo"></Input>
      </Col>
    </Row>
    <Row class="pt10 pb10">
      <Spin fix v-show="loadingData">
        <Icon type="load-c" size=18 class="demo-spin-icon-load"></Icon>
        <div>数据加载中...</div>
      </Spin>
      <Table v-show="!loadingData" :columns="accountColumns" :data="accountDatas" @on-select="tableSelectCurrent" @on-select-all='tableSelectAll' @on-select-cancel="tableCancelCurrent" @on-selection-change="tableChange" highlight-row>
      </Table>
    </Row>
    <Row class="tr pt5 pb10">
      <Page :total="tp" :current='cp' :page-size="ep" size="small" show-total show-sizer show-elevator :page-size-opts='[10,20,30]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
    </Row>
    <!-- 新增机构modal -->
    <Modal v-model="addNewAcademyModel" :mask-closable="false" width="600" title="新增机构">
      <div class="editAcademy-model-body">
        <Form ref="addNewAcademyForm" :model="addNewAcademyDatas" :label-width="80" :rules="addNewAcademyRules">
          <FormItem label="名称" prop="name">
            <Input v-model="addNewAcademyDatas.name" placeholder="请输入机构名称"></Input>
          </FormItem>
          <FormItem label="部门" prop='depart'>
            <Input v-model="addNewAcademyDatas.depart" placeholder="请输入部门"></Input>
          </FormItem>
          <FormItem label="所属省市" prop='city'>
            <Cascader :data="regionListDatas" v-model="addNewAcademyDatas.city" placeholder="请选择省市" :load-data="loadRegionData"></Cascader>
          </FormItem>
          <FormItem label="销售" prop="fromUserName">
            <Input v-model="addNewAcademyDatas.fromUserName" placeholder="请输入销售"></Input>
          </FormItem>
          <FormItem label="有效时间" required>
            <Row>
              <Col span="11">
              <FormItem prop="startTime">
                <DatePicker type="date" placeholder="选择开始时间" v-model="addNewAcademyDatas.startTime" :editable="false"></DatePicker>
              </FormItem>
              </Col>
              <Col span="2" style="text-align: center">-</Col>
              <Col span="11">
              <FormItem prop="endTime">
                <DatePicker type="date" placeholder="选择结束时间" v-model="addNewAcademyDatas.endTime" :editable="false"></DatePicker>
              </FormItem>
              </Col>
            </Row>
          </FormItem>
          <FormItem label="学生数" prop="userNum">
            <Input v-model="addNewAcademyDatas.userNum" placeholder="请输入学生数"></Input>
          </FormItem>
          <FormItem label="IP段" required>
            <div v-for="(item,index) in addNewAcademyDatas.ips" key='index' class="edit-ip-row">
              <Row>
                <Col span="11">
                <FormItem :prop="'ips.' + index + '.startIp'" :rules="ipCheckRules">
                  <Input v-model="item.startIp" placeholder="请输入开始IP"></Input>
                </FormItem>
                </Col>
                <Col span="1" style="text-align: center">-</Col>
                <Col span="11">
                <FormItem :prop="'ips.' + index + '.endIp'" :rules="ipCheckRules">
                  <Input v-model="item.endIp" placeholder="请输入结束IP"></Input>
                </FormItem>
                </Col>
                <Col span='1'>
                <Icon color='red' style='margin-left: 10px;cursor:pointer;' type="close-round" @click.native="deleteNowIps(index)"></Icon>
                </Col>
              </Row>
            </div>
            <Row class="pt20">
              <Button type="info" long @click="addNewIp('add')">新增IP段</Button>
            </Row>
          </FormItem>
        </Form>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="addNewAcademyModel=false">返回</Button>
        <Button type="primary" @click="submitAddNewAcademy('addNewAcademyForm')">确认新增</Button>
      </div>
    </Modal>
    <!-- 修改机构modal -->
    <Modal v-model="editAcademyModel" :mask-closable="false" width="600" title="修改机构">
      <div class="editAcademy-model-body">
        <Form ref="editAcademyForm" :model="editAcademyDatas" :label-width="80" :rules="addNewAcademyRules">
          <FormItem label="名称" prop="name">
            <Input v-model="editAcademyDatas.name" placeholder="请输入机构名称"></Input>
          </FormItem>
          <FormItem label="部门" prop='depart'>
            <Input v-model="editAcademyDatas.depart" placeholder="请输入部门"></Input>
          </FormItem>
          <FormItem label="所属省市" prop='city'>
            <Cascader :data="regionListDatas" v-model="editAcademyDatas.city" placeholder="请选择省市" :load-data="loadRegionData"></Cascader>
          </FormItem>
          <FormItem label="销售" prop="fromUserName">
            <Input v-model="editAcademyDatas.fromUserName" placeholder="请输入销售"></Input>
          </FormItem>
          <FormItem label="有效时间" required>
            <Row>
              <Col span="11">
              <FormItem prop="startTime">
                <DatePicker type="date" placeholder="选择开始时间" v-model="editAcademyDatas.startTime" :editable="false"></DatePicker>
              </FormItem>
              </Col>
              <Col span="2" style="text-align: center">-</Col>
              <Col span="11">
              <FormItem prop="endTime">
                <DatePicker type="date" placeholder="选择结束时间" v-model="editAcademyDatas.endTime" :editable="false"></DatePicker>
              </FormItem>
              </Col>
            </Row>
          </FormItem>
          <FormItem label="学生数" prop="userNum">
            <Input v-model="editAcademyDatas.userNum" placeholder="请输入学生数"></Input>
          </FormItem>
          <FormItem label="IP段" required>
            <div v-for="(item,index) in editAcademyDatas.ips" key='index' class="edit-ip-row">
              <Row>
                <Col span="11">
                <FormItem :prop="'ips.' + index + '.startIp'" :rules="ipCheckRules">
                  <Input v-model="item.startIp" placeholder="请输入开始IP"></Input>
                </FormItem>
                </Col>
                <Col span="1" style="text-align: center">-</Col>
                <Col span="11">
                <FormItem :prop="'ips.' + index + '.endIp'" :rules="ipCheckRules">
                  <Input v-model="item.endIp" placeholder="请输入结束IP"></Input>
                </FormItem>
                </Col>
                <Icon color='red' style='margin-left: 10px;cursor:pointer;' type="close-round" @click.native="deleteNowEditIps(index)"></Icon>
                </Col>
              </Row>
            </div>
            <Row class="pt20">
              <Button type="info" long @click="addNewIp('edit')">新增IP段</Button>
            </Row>
          </FormItem>
        </Form>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="editAcademyModel=false">返回</Button>
        <Button type="primary" @click="submitEditAcademy('editAcademyForm')">确认修改</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
export default {
  data() {
    const filters = window.sessionStorage.getItem('bg_user_filter');
    const CheckName = (rule, value, callback) => {
      let reg = /^[\u4E00-\u9FA5]{1,10}$/;
      if (!reg.test(value)) {
        callback(new Error('只能为中文'))
      } else {
        callback();
      }
    };
    const CheckDepartment = (rule, value, callback) => {
      let reg = /^[\u4E00-\u9FA5]{1,10}$/;
      if (!reg.test(value)) {
        callback(new Error('只能为中文'))
      } else {
        callback();
      }
    };
    const CheckSeller = (rule, value, callback) => {
      let reg = /^[\u4E00-\u9FA5]{2,4}(?:·[\u4E00-\u9FA5]{2,5})*$/;
      if (!reg.test(value)) {
        callback(new Error('输入销售者真实姓名'))
      } else {
        callback();
      }
    };
    const CheckIp = (rule, value, callback) => {
      let reg = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/
      if (!reg.test(value)) {
        callback(new Error('输入正确IP地址'))
      } else {
        callback();
      }
    };
    const CheckUserNum = (rule, value, callback) => {
      let reg = /^[0-9]*$/
      if (!reg.test(value)) {
        callback(new Error('只能为数字'))
      } else {
        callback();
      }
    };


    return {
      regionList: ['北京', '天津', '河北', '山西', '内蒙古', '辽宁', '吉林', '黑龙江', '上海', '江苏', '浙江', '安徽', '福建', '江西', '山东', '河南', '湖北', '湖南', '广东', '广西', '海南', '重庆', '四川', '贵州', '云南', '西藏', '陕西', '甘肃', '青海', '宁夏', '新疆', '香港', '澳门', '台湾'],

      filter: {
        region: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).region ? JSON.parse(filters).region : '0',
        searchKey: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).searchKey ? JSON.parse(filters).searchKey : '',
      },

      addNewAcademyModel: false,
      editAcademyModel: false,

      tp: 0,
      cp: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).cp ? parseInt(JSON.parse(filters).cp) : 1,
      ep: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).ep ? parseInt(JSON.parse(filters).ep) : 10,

      loadingData: false,

      ipCheckRules: [{ required: true, message: '该项不能为空', trigger: 'blur' }, {
        validator: CheckIp,
        trigger: 'blur'
      }],

      accountColumns: [{
        type: 'selection',
        width: 60,
        align: 'center',
      }, {
        title: '机构',
        key: 'name',
      }, {
        title: '省份',
        key: 'area',
      }, {
        title: '城市',
        key: 'city',
      }, {
        title: '用户数',
        key: 'userNum',
      }, {
        title: '操作',
        key: 'action',
        width: 160,
        align: 'center',
        render: (h, params) => {
          return h('div', [
            h('Button', {
              props: {
                type: 'primary',
              },
              on: {
                click: () => {
                  this.editAcademy(params.row, params.index)
                }
              }
            }, '编辑'),
            h('Button', {
              props: {
                type: 'error',
              },
              on: {
                click: () => {
                  this.deleteAcademy(params.row, params.index)
                }
              }
            }, '删除'),
          ]);
        }
      }],

      //表格数据
      accountDatas: [],

      selectItem: [],

      //新增数据
      addNewAcademyDatas: {
        name: '',
        fromUserName: '',
        depart: '',
        city: [],
        startTime: '',
        endTime: '',
        userNum: '',
        ips: [{
          startIp: '',
          endIp: '',
        }]
      },

      editAcademyDatas: {
        schoolId: '',
        name: '',
        fromUserName: '',
        depart: '',
        city: [],
        startTime: '',
        endTime: '',
        userNum: '',
        ips: [{
          startIp: '',
          endIp: '',
        }]
      },

      //验证规则
      addNewAcademyRules: {
        name: [{
          required: true,
          message: '该项不能为空',
          trigger: 'blur'
        }, {
          validator: CheckName,
          trigger: 'blur'
        }],
        depart: [{
          required: true,
          message: '该项不能为空',
          trigger: 'blur'
        }, {
          validator: CheckDepartment,
          trigger: 'blur'
        }],
        city: [{
          required: true,
          type: 'array',
          message: '该项不能为空',
          trigger: 'change'
        }],
        fromUserName: [{
          required: true,
          message: '该项不能为空',
          trigger: 'blur'
        }, {
          validator: CheckSeller,
          trigger: 'blur'
        }],
        startTime: [{
          required: true,
          type: 'date',
          message: '该项不能为空',
          trigger: 'change'
        }],
        endTime: [{
          required: true,
          type: 'date',
          message: '该项不能为空',
          trigger: 'change'
        }],
        userNum: [{
          required: true,
          message: '该项不能为空',
          trigger: 'blur'
        }, {
          validator: CheckUserNum,
          trigger: 'blur'
        }]
      },

      regionListDatas: [{
          value: '北京',
          label: '北京',
          children: [],
          loading: false
        },
        {
          value: '杭州',
          label: '杭州',
          children: [],
          loading: false
        }
      ],
    }
  },
  methods: {
    //获取机构列表
    getListData() {
      this.loadingData = true;
      this.$http.post('/v1/admin/activity/school-list', {
        area: this.filter.region,
        keyword: this.filter.searchKey,
        pageSize: this.ep,
        page: this.cp
      }).then((res) => {
        this.accountDatas = res.data.data.data.rows;
        this.tp = parseInt(res.data.data.data.total)
        this.loadingData = false;
      })
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

    regionChange() {
      this.cp = 1;
      this.updateFilter({ 'region': this.filter.region });
      this.getListData();
    },

    searchInfo() {
      if (this.filter.searchKey != '') {
        this.cp = 1;
        this.updateFilter({ 'searchKey': this.filter.searchKey });
        this.getListData();
      } else {
        this.$Message.warning('搜索关键字不能为空')
      }
    },

    //新建机构
    addNewAcademy() {
      this.addNewAcademyModel = true;
    },

    //确认新建
    submitAddNewAcademy(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$http.post('/v1/admin/school/create', {
            name: this.addNewAcademyDatas.name,
            depart: this.addNewAcademyDatas.depart,
            area: this.addNewAcademyDatas.city[0],
            city: this.addNewAcademyDatas.city[1],
            fromUserName: this.addNewAcademyDatas.fromUserName,
            startTime: this.dateToSeconds(this.addNewAcademyDatas.startTime),
            endTime: this.dateToSeconds(this.addNewAcademyDatas.endTime),
            ips: this.addNewAcademyDatas.ips,
            userNum: this.addNewAcademyDatas.userNum
          }).then((res) => {
            if (res.data.status == 200) {
              this.addNewAcademyModel = false;
              this.$Message.success('新增操作成功');
              this.getListData();
            }
          })
        }
      })
    },

    //获取省份信息
    loadProvinceData() {
      this.$http.get('/v1/admin/area/province', {
          params: {}
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.regionListDatas = res.data.data;
          }
        })
    },

    // 动态加载市
    loadRegionData(item, callback) {
      item.loading = true;
      this.$http.get('/v1/admin/area/city', {
          params: {
            code: item.id
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            item.children = res.data.data;
            item.loading = false;
            callback();
          }
        })
    },

    //新增IP段
    addNewIp(type) {
      if (type == 'add') {
        this.addNewAcademyDatas.ips.push({
          startIp: '',
          endIp: ''
        })
      } else if (type == 'edit') {
        this.editAcademyDatas.ips.push({
          startIp: '',
          endIp: ''
        })
      }

    },

    //编辑
    editAcademy(row, index) {
      this.$http.get('/v1/admin/school/info', {
          params: {
            schoolId: row.id,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            let newdata = res.data.data;
            this.editAcademyDatas = {
              name: newdata.name,
              fromUserName: newdata.fromUserName,
              city: [newdata.area, newdata.city],
              startTime: this.secondsToDate(newdata.startTime),
              endTime: this.secondsToDate(newdata.endTime),
              userNum: newdata.userNum,
              ips: newdata.ips,
              depart: newdata.depart,
              schoolId: newdata.id
            }
            this.editAcademyModel = true;
          }
        })
    },

    //确认修改
    submitEditAcademy(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$http.post('/v1/admin/school/modify', {
            schoolId: this.editAcademyDatas.schoolId,
            name: this.editAcademyDatas.name,
            depart: this.editAcademyDatas.depart,
            area: this.editAcademyDatas.city[0],
            city: this.editAcademyDatas.city[1],
            fromUserName: this.editAcademyDatas.fromUserName,
            startTime: this.dateToSeconds(this.editAcademyDatas.startTime),
            endTime: this.dateToSeconds(this.editAcademyDatas.endTime),
            ips: this.editAcademyDatas.ips,
            userNum: this.editAcademyDatas.userNum
          }).then((res) => {
            if (res.data.status == 200) {
              this.editAcademyModel = false;
              this.$Message.success('修改操作成功');
              this.getListData();
            }
          })
        }
      })
    },

    //删除机构
    deleteAcademy(row, index) {
      this.$Modal.confirm({
        title: '操作提示',
        content: '<h3>该操作将删除机构，请确认操作？</h3>',
        onOk: () => {
          this.$http.post('/v1/admin/school/remove', {
            schoolId: [row.id]
          }).then((res) => {
            if (res.data.status == 200) {
              this.$Message.success('删除操作成功');
              this.getListData();
            }
          })
        },
      });
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


    //批量删除机构
    deleteAcademyByBatches() {
      if (this.selectItem.length) {
        let selectedAcademyId = [];
        this.selectItem.forEach((item, index) => {
          selectedAcademyId.push(item.id)
        });
        this.$Modal.confirm({
          title: '操作提示',
          content: '<h3>该操作将批量删除机构，请确认操作？</h3>',
          onOk: () => {
            this.$http.post('/v1/admin/school/remove', {
              schoolId: selectedAcademyId
            }).then((res) => {
              if (res.data.status == 200) {
                this.$Message.success('批量删除操作成功');
                this.getListData();
              }
            })
          },
        });
      } else {
        this.$Message.warning('请至少勾选一个机构后再试！')
      }
    },

    //删除单行IP段
    deleteNowIps(index) {
      if (this.addNewAcademyDatas.ips.length == 1) {
        this.addNewAcademyDatas.ips.splice(index, 1);
        this.addNewAcademyDatas.ips.push({
          startIp: '',
          endIp: '',
        })
      } else {
        this.addNewAcademyDatas.ips.splice(index, 1)
      }
    },

    deleteNowEditIps(index) {
      if (this.editAcademyDatas.ips.length == 1) {
        this.editAcademyDatas.ips.splice(index, 1);
        this.editAcademyDatas.ips.push({
          startIp: '',
          endIp: '',
        })
      } else {
        this.editAcademyDatas.ips.splice(index, 1)
      }
    },
  },
  beforeRouteLeave(to, from, next) {
    window.sessionStorage.setItem('bg_user_filter', '')
    next();
  },
  mounted() {
    this.getListData();
    this.loadProvinceData();
  },
  watch: {
    'filter.searchKey': function(val, oldVal) {
      if (!val && oldVal) {
        this.cp = 1;
        this.updateFilter({ 'searchKey': '' })
        this.getListData();
      }
    },
    'addNewAcademyModel': function(val, oldVal) {
      if (!val && oldVal) {
        this.$refs.addNewAcademyForm.resetFields();
        this.addNewAcademyDatas = {
          name: '',
          fromUserName: '',
          depart: '',
          city: [],
          startTime: '',
          endTime: '',
          userNum: '',
          ips: [{
            startIp: '',
            endIp: '',
          }]
        };
      }
    },
  }
}

</script>
<style lang="less">
.academy-main {}

.edit-ip-row {
  margin-bottom: 2px;
}

.editAcademy-model-body {
  .ivu-date-picker {
    width: 100%;
  }
}

</style>
