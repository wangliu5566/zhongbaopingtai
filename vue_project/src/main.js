// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router/index.js'

Vue.config.productionTip = false;

import util from './util'
Vue.use(util);

//引入axios
import axios from './http.js'
Vue.prototype.$http = axios;

import iView from 'iview';
import 'iview/dist/styles/iview.css'; // 使用 CSS
Vue.use(iView);

import vSelect from 'vue-select'
Vue.component('v-select', vSelect)

//引入轮播插件
import VueAwesomeSwiper from 'vue-awesome-swiper'
Vue.use(VueAwesomeSwiper)


import VueCookie from 'vue-cookie'
Vue.use(VueCookie);


// 引入视频播放器
import VueVideoPlayer from 'vue-video-player'
Vue.use(VueVideoPlayer)

/* eslint-disable no-new */



new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
