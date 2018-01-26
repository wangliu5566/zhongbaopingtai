import Require from './require.js'

export default [{
  path: 'index',
  name: '首页',
  component: Require.Index,
}, {
  path: 'system',
  name: '系统管理',
  redirect: 'account',
  component: Require.CommonRouter,
  children: [{
    path: 'account',
    component: Require.Account,
    children: [{
      path: '',
      name: '账号管理',
      component: Require.AccountMain
    }, {
      path: 'add',
      name: '新建账号',
      component: Require.AccountAdd
    }, {
      path: 'edit',
      name: '编辑账号',
      component: Require.AccountEdit
    }]
  }, {
    path: 'role',
    component: Require.Role,
    children: [{
      path: '',
      name: '角色管理',
      component: Require.RoleMain
    }]
  }, {
    path: 'academy',
    component: Require.Academy,
    children: [{
      path: '',
      name: '机构管理',
      component: Require.AcademyMain
    }]
  }]
}, {
  path: 'content',
  name: '内容管理',
  redirect: 'news',
  component: Require.CommonRouter,
  children: [{
    path: 'news',
    component: Require.News,
    children: [{
      path: '',
      name: '新闻管理',
      component: Require.NewsMain
    }, {
      path: 'add',
      name: '新建新闻',
      component: Require.NewsAdd
    }, {
      path: 'edit',
      name: '编辑新闻',
      component: Require.NewsEdit
    }]
  }]
}, {
  path: 'activity',
  name: '活动管理',
  redirect: 'myactivity',
  component: Require.CommonRouter,
  children: [{
    path: 'myactivity',
    component: Require.Activity,
    children: [{
      path: '',
      name: '我的活动',
      component: Require.ActivityMain
    }, {
      path: 'audit',
      name: '审核活动',
      component: Require.ActivityAudit
    }, {
      path: 'preview',
      name: '查看活动',
      component: Require.ActivityPreview
    }, {
      path: 'edit',
      name: '编辑活动',
      component: Require.ActivityEdit
    }, {
      path: 'add',
      name: '新建活动',
      component: Require.ActivityAdd
    }, {
      path: 'check',
      name: '作品审核',
      component: Require.ActivityCheck
    }, , {
      path: 'awards',
      name: '奖项管理',
      component: Require.ActivityAwards
    }]
  }]
}, {
  path: 'data',
  name: '数据管理',
  component: Require.CommonRouter,
  children: [{
    path: 'view',
    name: '流量管理',
    redirect: '/wrap/data/view/date',
    component: Require.ManageView,
    children: [{
      path: 'date',
      name: '日数据',
      component: Require.ManageViewDate
    }, {
      path: 'week',
      name: '周数据',
      component: Require.ManageViewWeek
    }, {
      path: 'month',
      name: '月数据',
      component: Require.ManageViewMonth
    }]
  }, {
    path: 'activity',
    name: '活动数据',
    redirect: '/wrap/data/activity/status',
    component: Require.ManageActivity,
    children: [{
      path: 'status',
      name: '活动状态',
      component: Require.ManageActivityStatus,
    }, {
      path: 'rate',
      name: '活动参与率',
      component: Require.ManageActivityRate,
    }, {
      path: 'trend',
      name: '活动趋势',
      component: Require.ManageActivityTrend,
    },{
      path: 'analysis',
      name: '活动分析',
      component: Require.ManageActivityAnalysis
    }]
  }, {
    path: 'publisher',
    component: Require.ManagePublisher,
    children: [{
      path: '',
      name: '发起者列表',
      component: Require.ManagePublisherMain
    }, {
      path: 'list',
      name: '活动列表',
      component: Require.ManagePublisherList
    }]
  }, {
    path: 'activityworks',
    component: Require.ActivityWorks,
    redirect: '/wrap/data/activityworks/worksanalysis',
    children: [{
      path:'worksanalysis',
      name:'作品分析',
      component: Require.ActivityWorksAnalysis
    },{
      path:'worksclick',
      name:'作品点击量',
      component: Require.ActivityWorksClick
    }]
  }]
}]
