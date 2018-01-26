<template>
  <div class="bg-system-container">
    <BgHeader></BgHeader>
    <div class="main">
      <div class="container">
        <div class="router-main" ref="containerMainArea">
          <div class="router-container">
            <transition name="router-fade" mode="out-in">
              <router-view></router-view>
            </transition>
          </div>
          <BgFooter></BgFooter>
        </div>
      </div>
      <div class='sidemenu'>
        <div class="sidemenu-title">
          导航菜单
        </div>
        <BgSideMenu :menu-list="sideMenu"></BgSideMenu>
      </div>
    </div>
  </div>
</template>
<script>
import BgHeader from "@/components/common/BgHeader"
import BgSideMenu from "@/components/common/BgSideMenu"
import BgFooter from "@/components/common/BgFooter"
export default {
  data() {
    return {
      sideMenuBaseData: [{
        label: '系统管理',
        icon: 'gear-a',
        path: '/wrap/system',
        children: [{
          label: '账号管理',
          icon: 'ios-person',
          path: '/wrap/system/account'
        }, {
          label: '角色管理',
          icon: 'ios-paper',
          path: '/wrap/system/role'
        }, {
          label: '机构管理',
          icon: 'university',
          path: '/wrap/system/academy'
        }]
      }, {
        label: '内容管理',
        icon: 'cube',
        path: '/wrap/content',
        children: [{
          label: '新闻管理',
          icon: 'ios-navigate',
          path: '/wrap/content/news'
        }]
      }, {
        label: '活动管理',
        icon: 'clipboard',
        path: '/wrap/activity',
        children: [{
          label: '我的活动',
          icon: 'pricetag',
          path: '/wrap/activity/myactivity'
        }]
      }, {
        label: '数据管理',
        icon: 'stats-bars',
        path: '/wrap/data',
        children: [{
          label: '流量管理',
          icon: 'radio-waves',
          path: '/wrap/data/view'
        }, {
          label: '活动数据',
          icon: 'ios-pulse-strong',
          path: '/wrap/data/activity'
        }, {
          label: '发起者列表',
          icon: 'person-stalker',
          path: '/wrap/data/publisher'
        }, {
          label: '作品数据',
          icon: 'android-options',
          path: '/wrap/data/activityworks'
        }]
      }],

      sideMenu: (window.sessionStorage.getItem('bg_system_menu') != undefined && window.sessionStorage.getItem('bg_system_menu') != '') ? JSON.parse(window.sessionStorage.getItem('bg_system_menu')) : [],

    }
  },
  components: {
    BgHeader,
    BgSideMenu,
    BgFooter
  },
  methods: {
    getSideMenuById() {
      this.$http.post('/v1/admin/permission/get-sider-bar', {
        userId: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id
      }).then((res) => {
        if (res.data.status == 200) {
          this.sideMenu.push({
            label: '首页',
            icon: 'ios-home',
            path: '/wrap/index',
            children: []
          });
          let menuData = res.data.data;
          let firstMenu = menuData.first.join(',');
          let secondMenu = menuData.second.join(',');

          this.sideMenuBaseData.forEach((item, index) => {
            let menuItem = {};
            if (firstMenu.indexOf(item.label) != -1) {

              menuItem.label = item.label;
              menuItem.icon = item.icon;
              menuItem.path = item.path;
              menuItem.children = [];


              item.children.forEach((ele, i) => {
                if (secondMenu.indexOf(ele.label) != -1) {
                  menuItem.children.push({
                    label: ele.label,
                    icon: ele.icon,
                    path: ele.path
                  })
                }
                if (window.sessionStorage.getItem('isSuper') == '9844f81e1408f6ecb932137d33bed7cfdcf518a3' && ele.label == '机构管理') {
                  menuItem.children.push({
                    label: ele.label,
                    icon: ele.icon,
                    path: ele.path
                  })
                }

              })

              this.sideMenu.push(menuItem);
            }
          })

          window.sessionStorage.setItem('bg_system_menu', JSON.stringify(this.sideMenu))
        }
      })
    }
  },
  mounted() {
    if (this.sideMenu.length == 0) {
      this.getSideMenuById();
    }

  },
  beforeRouteLeave(to, from, next) {
    window.sessionStorage.setItem('bg_user_info', JSON.stringify(Object.assign({}, JSON.parse(window.sessionStorage.getItem('bg_user_info')), {
      access_token: '',
      password: '',
      id: '',
      agency: '',
      agencyId: '',
      realName: ''
    })));
    window.sessionStorage.removeItem('bg_user_permission');
    window.sessionStorage.removeItem('isSuper')
    next();
  },
  watch: {
    '$route': function(val, oldVal) {
      if (val.fullPath != oldVal.fullPath) {
        this.$refs.containerMainArea.scrollTop = 0;
      }
    }
  }

}

</script>
<style lang="less">
.bg-system-container {
  background-color: #e9ecf3;
  min-width: 1180px;
  height: 100%;
  width: 100%;
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
  .ivu-spin-text {
    padding: 30px 0;
  }
  .ivu-select-single .ivu-select-selection .ivu-select-placeholder,
  .ivu-select-single .ivu-select-selection .ivu-select-selected-value {
    font-size: 14px;
  }
  .main {
    background: #e9ecf3;
    width: 100%;
    overflow-y: auto;
    height: 100%;
    position: absolute;
    top: 0;
    z-index: 0;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    -ms-box-sizing: border-box;
    box-sizing: border-box;
    border-top: 80px solid #e9ecf3;
    overflow: hidden;

    .sidemenu {
      height: 100%;
      background-color: #fff;
      width: 230px;
      border-radius: 6px 6px 0 0;
      float: left;
      margin-left: 10px;

      .ivu-menu-vertical.ivu-menu-light:after {
        background: none;
      }
      .ivu-menu-dark.ivu-menu-vertical .ivu-menu-submenu .ivu-menu-item-active,
      .ivu-menu-dark.ivu-menu-vertical .ivu-menu-submenu .ivu-menu-item-active:hover {
        color: #39f;
        border-right: 2px solid #39f;
        background-color: #313540;
      }

      .ivu-menu-vertical .ivu-menu-submenu .ivu-menu-item {
        padding-left: 34px;
      }

      .ivu-menu-light.ivu-menu-vertical .ivu-menu-item-active:not(.ivu-menu-submenu) {
        color: #5c9acf;
        border-color: #5c9acf;
      }

      .el-menu {
        background-color: #1F2D3D;
      }

      .side-menu-main {
        width: 100%;
        text-align: center;
      }

      .sidemenu-title {
        padding: 20px 0 10px 20px;
        border-bottom: 1px solid #eee;
        color: #5c9acf;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 20px;
      }
    }

    .container {
      height: 100%;
      width: 100%;
      margin-left: -250px;
      float: right;
      background-color: #e9ecf3;

      .router-main {
        height: 100%;
        margin-left: 250px;
        background-color: #fff;
        overflow-y: auto;
      }
      .router-container {
        min-height: 100%;
        margin-bottom: -42px;
        padding-bottom: 42px;
        padding: 20px 20px 42px;
        font-size: 14px!important;

        .ivu-table {
          font-size: 14px;
        }
      }
    }
  }
  .system-title {
    font-size: 18px;
    color: #5c9acf;
    font-weight: bold;
    overflow: hidden;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;

    i,
    a {
      float: left;
      cursor: pointer;
    }
    a {
      padding-left: 6px;
      color: #5c9acf;
      &:hover {
        color: #2d8cf0;
      }
    }
    span {
      padding-left: 6px;
    }
    .pointer {
      font-size: 14px;
      padding-top: 3px;
      display: inline-block;
    }
    .next-title {
      font-size: 16px;
      color: #999;
      padding-top: 2px;
    }
  }
  .ivu-form-item-content {
    font-size: 14px;
  }
}

.check-works-modal {
  .ivu-form-item {
    margin-bottom: 8px;
  }
}






/* .ivu-tree-title {
  font-size: 14px;
}

.ivu-tree ul{
  font-size: 14px;
} */

</style>
