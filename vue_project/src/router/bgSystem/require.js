export default {
  Index: resolve => require.ensure([], () => resolve(require('@/components/bgSystem/Index')), 'bgSystem002'),
  CommonRouter:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/CommonRouter')), 'bgSystem001'),

  Account:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/Account')), 'bgSystem003'),
  AccountMain:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/AccountMain')), 'bgSystem003'),
  AccountAdd:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/AccountAdd')), 'bgSystem004'),
  AccountEdit:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/AccountEdit')), 'bgSystem005'),

  Role:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/Role')), 'bgSystem006'),
  RoleMain:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/RoleMain')), 'bgSystem006'),

  News:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/News')), 'bgSystem007'),
  NewsMain:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/NewsMain')), 'bgSystem007'),
  NewsAdd:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/NewsAdd')), 'bgSystem008'),
  NewsEdit:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/NewsEdit')), 'bgSystem009'),

  Activity:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/Activity')), 'bgSystem010'),
  ActivityMain:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityMain')), 'bgSystem010'),
  ActivityAudit:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityAudit')), 'bgSystem011'),
  ActivityPreview:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityPreview')), 'bgSystem012'),
  ActivityEdit:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityEdit')), 'bgSystem013'),
  ActivityAdd:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityAdd')), 'bgSystem014'),
  ActivityCheck:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityCheckWorks')), 'bgSystem015'),
  ActivityAwards:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityAwardsManage')), 'bgSystem016'),

  Academy:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/Academy')), 'bgSystem025'),
  AcademyMain:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/AcademyMain')), 'bgSystem025'),


  ManageView:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManageView')), 'bgSystem017'),
  ManageViewDate:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManageViewDate')), 'bgSystem017'),
  ManageViewWeek:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManageViewWeek')), 'bgSystem018'),
  ManageViewMonth:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManageViewMonth')), 'bgSystem019'),

  ManageActivity:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManageActivity')), 'bgSystem020'),
  ManageActivityStatus:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManageActivityStatus')), 'bgSystem020'),
  ManageActivityRate:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManageActivityRate')), 'bgSystem021'),
  ManageActivityTrend:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManageActivityTrend')), 'bgSystem022'),
  ManageActivityAnalysis:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManageActivityAnalysis')), 'bgSystem025'),

  ActivityWorks:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityWorks')), 'bgSystem026'),
  ActivityWorksAnalysis:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityWorksAnalysis')), 'bgSystem026'),
  ActivityWorksClick:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ActivityWorksClick')), 'bgSystem027'),


  ManagePublisher:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManagePublisher')), 'bgSystem023'),
  ManagePublisherMain:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManagePublisherMain')), 'bgSystem023'),
  ManagePublisherList:resolve => require.ensure([], () => resolve(require('@/components/bgSystem/ManagePublisherList')), 'bgSystem024'),
}