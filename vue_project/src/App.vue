<template>
  <div id="app" class="app-container">
    <transition name="router-fade" mode="out-in">
      <router-view></router-view>
    </transition>
  </div>
</template>
<script>
export default {
  name: 'app'
}

</script>
<style lang='less'>
.router-fade-enter-active,
.router-fade-leave-active {
  transition: opacity .3s;
}

.router-fade-enter,
.router-fade-leave-active {
  opacity: 0;
}

html,
body {
  width: 100%;
  height: 100%;
}

.app-container {
  height: 100%;
  width: 100%;
  font-family: "微软雅黑", 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}






/* 去掉模态框footer上边框 */

.ivu-modal-footer {
  border-top: none!important;
  padding: 10px 20px 20px!important;
}

.ivu-form .ivu-form-item-label {
  font-weight: bold;
  font-size: 14px!important;
}

.ivu-btn+.ivu-btn {
  margin-left: 10px!important;
}

.ivu-input {
  font-size: 14px;
}

.ivu-radio-wrapper {
  font-size: 14px!important;
}


textarea.ivu-input {
  font-size: 14px!important;
}







/* 修改iview模态框关闭按钮样式 */

.ivu-modal-header {
  border-bottom: none!important;
}

.ivu-modal-header p,
.ivu-modal-header-inner {
  font-size: 16px!important;
}

.ivu-modal-close {
  top: -6px!important;
  right: -40px!important;
}

.ivu-modal-close .ivu-icon-ios-close-empty {
  display: block;
  width: 30px;
  height: 30px;
  -webkit-transition: -webkit-transform 0.18s!important;
  transition: -webkit-transform 0.18s!important;
  transition: transform 0.18s!important;
  transition: transform 0.18s, -webkit-transform 0.18s!important;
  -webkit-transform: rotate(0);
  -ms-transform: rotate(0);
  transform: rotate(0);
}

.ivu-modal-close .ivu-icon-ios-close-empty::before,
.ivu-modal-close .ivu-icon-ios-close-empty::after {
  -webkit-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.23);
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.23);
  content: '';
  position: absolute;
  top: 12px;
  left: 4px;
  width: 20px;
  height: 3px;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
  background-color: #fff;
}

.ivu-modal-close .ivu-icon-ios-close-empty::after {
  -webkit-transform: rotate(-45deg);
  -ms-transform: rotate(-45deg);
  transform: rotate(-45deg);
}

.ivu-modal-close .ivu-icon-ios-close-empty:hover {
  -webkit-transform: rotate(90deg);
  -ms-transform: rotate(90deg);
  transform: rotate(90deg);
}

.ivu-input-icon {
  cursor: pointer;
  font-size: 20px;
  &:hover {
    color: #2d8cf0;
  }
}

.ivu-modal-confirm-footer {
  .ivu-btn-text {
    border: 1px solid #dddee1!important;
  }
}

.ivu-input-wrapper {
  textarea {
    resize: none;
  }
}

.ivu-tree ul {
  font-size: 14px!important;
}

.ivu-tree-arrow {
  margin-right: 5px;
}

.ivu-message .ivu-icon{
  font-size: 18px;
}

</style>
