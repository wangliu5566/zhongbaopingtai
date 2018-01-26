import Vue from 'vue'
import Router from 'vue-router'

import BgSystem from './bgSystem/'

const BgSystemWrap = resolve => require.ensure([], () => resolve(require('@/components/bgSystem/Wrap')), 'bgSystem002')
const BgSystemLogin = resolve => require.ensure([], () => resolve(require('@/components/common/BgLogin')), 'bgSystem001')

Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [{
      path: '/',
      component: resolve => require(['@/components/frontPage/FrontPage.vue'], resolve),
      redirect: '/frontPage',
    },{
      path: '/frontPage',
      component: resolve => require(['@/components/frontPage/FrontPage.vue'], resolve),
      children: [{
          path: '',
          component: resolve => require(['@/components/frontPage/Indexs.vue'], resolve),
          redirect: '/frontPage/index',
        }, {
          path: 'index',
          component: resolve => require(['@/components/frontPage/Indexs.vue'], resolve)
        }, {
          path: 'apply',
          component: resolve => require(['@/components/frontPage/Apply.vue'], resolve)
        }, {
          path: 'question',
          component: resolve => require(['@/components/frontPage/Question.vue'], resolve)
        }, {
          path: 'initQuestion',
          component: resolve => require(['@/components/frontPage/InitQuestion.vue'], resolve)
        }, {
          path: 'news',
          component: resolve => require(['@/components/frontPage/NewPage.vue'], resolve)
        }, {
          path: 'detail',
          component: resolve => require(['@/components/frontPage/Detail.vue'], resolve),
        },{
          path: 'initDetail',
          component: resolve => require(['@/components/frontPage/InitDetail.vue'], resolve),
        }, {
          path: 'gameList',
          component: resolve => require(['@/components/frontPage/GameList.vue'], resolve),
        }, {
          path: 'production',
          component: resolve => require(['@/components/frontPage/Production.vue'], resolve),
        }, {
          path: 'allActivity',
          component: resolve => require(['@/components/frontPage/AllActivity.vue'], resolve),
        },{
          path: 'user',
          component: resolve => require(['@/components/frontPage/UserAgreement.vue'], resolve),
        },{
          path: 'person',
          component: resolve => require(['@/components/frontPage/Person.vue'], resolve),
          redirect: '/frontPage/person/myactivities',
          children: [{
                path: 'myactivities',
                component: resolve => require(['@/components/frontPage/person/Myactivities.vue'], resolve)
              }, {
                path: 'mymatch',
                component: resolve => require(['@/components/frontPage/person/Mymatch.vue'], resolve),
                redirect: '/frontPage/person/mymatch/matchPass',
                children:[{
                    path: 'matchPass',
                    component: resolve => require(['@/components/frontPage/person/MatchPass.vue'], resolve),
                },{
                    path: 'matchNotPass',
                    component: resolve => require(['@/components/frontPage/person/MatchNotPass.vue'], resolve),
                }]
              },{
                path: 'personalData',
                component: resolve => require(['@/components/frontPage/person/PersonalData.vue'], resolve)
              },{
                path: 'setPsd',
                component: resolve => require(['@/components/frontPage/person/SetPsd.vue'], resolve)
              }]
          }]
        }, {
          path: '/phonePerson',
          component: resolve => require(['@/components/frontPage/person/PhonePerson.vue'], resolve),
        },   {
          path: '/phoneActivities',
          component: resolve => require(['@/components/frontPage/person/PhoneActivities.vue'], resolve),
        }, {
          path: '/phoneMatch',
          component: resolve => require(['@/components/frontPage/person/PhoneMatch.vue'], resolve),
          redirect: '/phoneMatch/matchPass',
                children:[{
                    path: 'matchPass',
                    component: resolve => require(['@/components/frontPage/person/PhoneMatchPass.vue'], resolve),
                },{
                    path: 'matchNotPass',
                    component: resolve => require(['@/components/frontPage/person/PhoneMatchNotPass.vue'], resolve),
                }]
        },{
          path: '/phoneData',
          component: resolve => require(['@/components/frontPage/person/PhoneData.vue'], resolve),
        },{
          path: '/mail',
          component: resolve => require(['@/components/frontPage/person/Mail.vue'], resolve),
        },{
          path: '/phone',
          component: resolve => require(['@/components/frontPage/person/Phone.vue'], resolve),
        },{
          path: '/address',
          component: resolve => require(['@/components/frontPage/person/Address.vue'], resolve),
        },{
          path: '/sex',
          component: resolve => require(['@/components/frontPage/person/Sex.vue'], resolve),
        },{
          path: '/psd',
          component: resolve => require(['@/components/frontPage/person/PhonePsd.vue'], resolve),
        },{
          path: '/wrap',
          component: BgSystemWrap,
          redirect: '/wrap/index',
          beforeEnter: (to, from, next) => {
            let userInfo = JSON.parse(window.sessionStorage.getItem('bg_user_info'));
            if (userInfo && userInfo.id && userInfo.id != 'undefined' && userInfo.realName && userInfo.realName != 'undefined' && userInfo.access_token && userInfo.access_token != 'undefined') {
              next()
            } else {
              next({ path: '/login' })
            }
          },
          children: [
              ...BgSystem
            ]
          }, {
            path: '/login',
            component: BgSystemLogin,
          }],
          scrollBehavior(to, from, savedPosition) {
            return { x: 0, y: 0 }
          }
    })
