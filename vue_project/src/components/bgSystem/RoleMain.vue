<template>
  <div>
    <Row>
      <Col span="16">
      <Button type="primary" @click="addNewRole" v-if="permissionList.indexOf('permission/add-permissions-for-role')!=-1?true:false">
        <Icon type="plus-round"></Icon> 添加</Button>
      &nbsp;
      </Col>
      <Col span="8">
      <Input v-model="searchKey" icon="ios-search-strong" placeholder="搜索角色名称" @on-click="searchInfo"></Input>
      </Col>
    </Row>
    <Row class="pt10 pb10">
      <Spin fix v-show="loadingData">
        <Icon type="load-c" size=18 class="demo-spin-icon-load"></Icon>
        <div>数据加载中...</div>
      </Spin>
      <Table v-show="!loadingData" :columns="accountColumns" :data="accountDatas" highlight-row>
      </Table>
    </Row>
    <!-- 新增角色modal -->
    <Modal v-model="addNewRoleModal" :mask-closable="false" width="600" title="新增角色">
      <div class="exam-model-body">
        <div>
          <Form :model="addNewRoleDatas" :label-width="120" ref="addNewRoleForm" :rules="addNewRoleFormFormRules">
            <Form-item label="角色名称：" prop="role" required>
              <Input v-model="addNewRoleDatas.role" placeholder="请输入角色名称"></Input>
            </Form-item>
            <Form-item label="角色简介：" prop="description">
              <Input type="textarea" :autosize="{minRows: 4,maxRows: 6}" v-model="addNewRoleDatas.description" placeholder="请输入角色简介"></Input>
            </Form-item>
            <Form-item label="权限设置：" prop="permission" required>
              <!-- <div style="border-bottom: 1px solid #e9e9e9;padding-bottom:4px;margin-bottom:4px;">
                <Checkbox :indeterminate="indeterminate" :value="checkAll" @click.prevent.native="handleCheckAll">全选</Checkbox>
              </div>
              <Checkbox-group v-model="addNewRoleDatas.permission" @on-change="checkAllGroupChange">
                <Checkbox :label="item.name" v-for="(item,index) in limitList" :key="index">
                  <span>{{item.description}}</span>
                </Checkbox>
              </Checkbox-group> -->
              <!-- <ivu-tree :data="treeData" show-checkbox @on-check-change="treeCheckChange" ref="permissionTree"></ivu-tree> -->
              <Tree :data="treeData" show-checkbox @on-check-change="treeCheckChange" ref="permissionTree"></Tree>
            </Form-item>
          </Form>
        </div>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="addNewRoleModal=false">取消</Button>
        <Button type="primary" @click="handleAddNewRole('addNewRoleForm')">确认新增</Button>
      </div>
    </Modal>
    <!-- 编辑角色modal -->
    <Modal v-model="editRoleModal" :mask-closable="false" width="600" title="编辑角色">
      <div class="exam-model-body">
        <div>
          <Form :model="editRoleDatas" :label-width="120" ref="editRoleForm" :rules="editRoleFormFormRules">
            <Form-item label="角色名称：" prop="role" required>
              <Input v-model="editRoleDatas.role" placeholder="请输入角色名称" disabled readonly></Input>
            </Form-item>
            <Form-item label="角色简介：" prop="description">
              <Input type="textarea" :autosize="{minRows: 4,maxRows: 6}" v-model="editRoleDatas.description" placeholder="请输入角色简介"></Input>
            </Form-item>
            <Form-item label="权限设置：" prop="permission">
              <!-- <ivu-tree :data="treeData" show-checkbox @on-check-change="treeCheckChange1" ref="permissionTree1"></ivu-tree> -->
              <Tree :data="treeData" show-checkbox @on-check-change="treeCheckChange1" ref="permissionTree1"></Tree>
            </Form-item>
          </Form>
        </div>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="editRoleModal=false">取消</Button>
        <Button type="primary" @click="handleEditRole('editRoleForm')">确认修改</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
// import IvuTree from "iview/src/components/tree"
export default {
  // components:{
  //   IvuTree
  // },
  data() {
    const CheckPermission = (rule, value, callback) => {
      if (this.addNewRoleDatas.permission.length == 0) {
        callback(new Error('至少选择一条权限'));
      } else {
        callback();
      }
    };

    const CheckRole = (rule, value, callback) => {
      let roleReg = /^[\u4E00-\u9FA5]{1,10}$/;
      if (value === '') {
        callback(new Error('请输入角色名'))
      } else if (!roleReg
        .test(value)) {
        callback(new Error('角色名称只能是汉字，且长度小于10'))
      } else {
        callback()
      }
    };
    const CheckPermission1 = (rule, value, callback) => {
      if (!this.changePermission) {
        let data = this.$refs.permissionTree1.getCheckedNodes();
        if (data.length != 0) {
          let permissionArr = [];
          let secondPermissionArr = [];
          data.forEach((item, index) => {
            if (item.nodeKey === undefined) {
              permissionArr.push(item.value);
              secondPermissionArr.push(this.splitStr(item.value));
            }
            this.editRoleDatas.permission = permissionArr;
            this.editRoleDatas.secondPermission = Array.from(new Set(secondPermissionArr));
          })
        } else {
          this.editRoleDatas.permission = [];
          this.editRoleDatas.secondPermission = [];
        }
      }

      if (this.editRoleDatas.permission.length == 0) {
        callback(new Error('至少选择一条权限'));
      } else {
        callback();
      }

    };

    const filters = window.sessionStorage.getItem('bg_user_filter');
    return {
      searchKey: filters != null && filters != '' && JSON.parse(filters) != {} && JSON.parse(filters).searchKey ? JSON.parse(filters).searchKey : '',

      //权限数组
      limitList: [],

      baseData: [],

      loadingData: false,

      //编辑角色权限是否更改
      changePermission: false,

      permissionList: window.sessionStorage.getItem('bg_user_permission'),


      //模态框数据
      addNewRoleModal: false,
      editRoleModal: false,

      //新增角色数据
      addNewRoleDatas: {
        role: '',
        description: '',
        permission: [],
        secondPermission: [],
      },
      //权限是否全选
      indeterminate: false,
      checkAll: false,

      //修改角色数据
      editRoleDatas: {
        role: '',
        description: '',
        permission: [],
        secondPermission: []
      },

      //权限是否全选
      indeterminate1: false,
      checkAll1: false,

      addNewRoleFormFormRules: {
        role: [
          // {
          //   required: true,
          //   message: '请输入角色名称',
          //   trigger: 'blur'
          // },
          {
            validator: CheckRole,
            trigger: 'blur'
          }
        ],
        description: [{
          required: true,
          message: '请输入角色简介',
          trigger: 'blur'
        }],
        permission: [{
          validator: CheckPermission,
          trigger: 'change',
        }]
      },

      editRoleFormFormRules: {
        role: [{
          validator: CheckRole,
          trigger: 'blur'
        }],
        description: [{
          required: true,
          message: '请输入角色简介',
          trigger: 'blur'
        }],
        permission: [{
          validator: CheckPermission1,
          trigger: 'change',
        }]
      },

      //表格配置
      accountColumns: [{
        title: '角色名称',
        key: 'role',
        width: 150
      }, {
        title: '操作权限',
        key: 'permission'
      }, {
        title: '创建时间',
        key: 'created_at',
        sortable: true,
        width: 120
      }, {
        title: '更新时间',
        key: 'updated_at',
        sortable: true,
        width: 120
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
                disabled: this.permissionList.indexOf('permission/modify-roles-permission') != -1 ? false : true
              },
              on: {
                click: () => {
                  this.editRole(params.row.role, params.index)
                }
              }
            }, '编辑'),
            h('Button', {
              props: {
                type: 'error',
                disabled: params.row.role == '超级管理员' ? true : false,
              },
              on: {
                click: () => {
                  this.removeRole(params.row.role, params.index)
                }
              }
            }, '删除')
          ]);
        }
      }],

      //表格数据
      accountDatas: [],

      //tree数据
      treeData: [],

    }
  },
  methods: {
    //搜索关键字
    searchInfo() {
      if (this.searchKey != '') {
        this.cp = 1;
        this.updateFilter({'searchKey':this.searchKey});
        this.getListData();
      } else {
        this.$Message.warning('搜索关键字不能为空')
      }
    },
    //新增角色
    addNewRole() {
      this.getPermission();
    },

    //编辑角色
    editRole(role, index) {
      this.$http.post('/v1/admin/permission/permission-list', {
        role: role
      }).then((res) => {
        if (res.data.status == 200) {
          this.treeData = res.data.data.permission;
          this.editRoleDatas.role = res.data.data.role;
          this.editRoleDatas.description = res.data.data.description;
          this.editRoleModal = true;
        }
      })
    },
    removeRole(role, index) {
      this.$Modal.confirm({
        title: '操作提示',
        content: '<h3>此操作将删除该角色，确认操作？</h3>',
        onOk: () => {
          this.$http.post('/v1/admin/permission/delete-role', {
              role: role
            })
            .then((res) => {
              if (res.data.status == 200) {
                this.$Message.success({
                  content: '角色删除成功',
                  duration: 2
                })
                this.getListData();
              }
            })
        },
      });

    },

    //确认新增角色
    handleAddNewRole(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$http.post('/v1/admin/permission/add-permissions-for-role', {
            role: this.addNewRoleDatas.role,
            description: this.addNewRoleDatas.description,
            permission: this.addNewRoleDatas.permission.join(','),
            secondPermission: this.addNewRoleDatas.secondPermission.join(','),
            timeout: 10000
          }).then((res) => {
            if (res.data.status == 200) {
              this.addNewRoleModal = false;
              this.$Message.success("角色新增成功")
              this.getListData();
            }
          })
        }
      })
    },

    treeCheckChange(data) {
      if (data.length != 0) {
        let permissionArr = [];
        let secondPermissionArr = [];
        data.forEach((item, index) => {
          if (item.nodeKey === undefined) {
            permissionArr.push(item.value);
            secondPermissionArr.push(this.splitStr(item.value));
          }
          this.addNewRoleDatas.permission = permissionArr;
          this.addNewRoleDatas.secondPermission = Array.from(new Set(secondPermissionArr));
        })
      } else {
        this.addNewRoleDatas.permission = [];
        this.addNewRoleDatas.secondPermission = [];
      }
    },

    treeCheckChange1(data) {
      this.changePermission = true;
      if (data.length != 0) {
        let permissionArr = [];
        let secondPermissionArr = [];
        data.forEach((item, index) => {
          if (item.nodeKey === undefined) {
            permissionArr.push(item.value);
            secondPermissionArr.push(this.splitStr(item.value));
          }
          this.editRoleDatas.permission = permissionArr;
          this.editRoleDatas.secondPermission = Array.from(new Set(secondPermissionArr));
        })
      } else {
        this.editRoleDatas.permission = [];
        this.editRoleDatas.secondPermission = [];
      }
    },

    //取/之前的字符串
    splitStr(str) {
      return str.split('\/')[0];
    },

    //确认修改角色
    handleEditRole(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$http.post('/v1/admin/permission/add-permissions-for-role', {
            role: this.editRoleDatas.role,
            description: this.editRoleDatas.description,
            permission: this.editRoleDatas.permission.slice().join(','),
            secondPermission: this.editRoleDatas.secondPermission.slice().join(','),
            timeout: 10000
          }).then((res) => {
            if (res.data.status == 200) {
              this.editRoleModal = false;
              this.$Message.success("角色编辑成功")
              this.getListData();
            }
          })
        }
      })
    },

    getListData() {
      this.loadingData = true;
      this.$http.post('/v1/admin/permission/get-permission-and-role-list', {
        desc: 0,
        role: this.searchKey
      }).then((res) => {
        this.loadingData = false;
        this.accountDatas = res.data.data;
      })
    },

    //获取所有权限
    getPermission() {
      this.$http.post('/v1/admin/permission/permission-list')
        .then((res) => {
          this.treeData = res.data.data.permission;
          this.addNewRoleModal = true;
        })
    },
  },
  beforeRouteLeave(to, from, next) {
    window.sessionStorage.setItem('bg_user_filter', '')
    next();
  },
  watch: {
    // 'addNewRoleModal': function(val, oldVal) {
    //   if (!val) {
    //     // this.$refs.addNewRoleForm.resetFields();
    //     // this.treeData = [];
    //   }
    // },
    // 'editRoleModal': function(val, oldVal) {
    //   if (!val) {
    //     // this.$refs.editRoleForm.resetFields();
    //     this.editRoleDatas = {
    //       role: '',
    //       description: '',
    //       permission: [],
    //       secondPermission: [],
    //     }
    //     this.changePermission = false;
    //   }
    // },
    // 'searchKey': function(val, oldVal) {
    //   if (!val && oldVal) {
    //     this.updateFilter({'searchKey':''});
    //     this.getListData();
    //   }
    // }
  },
  mounted() {
    this.getListData();
  }
}

</script>
<style lang="less">


</style>
