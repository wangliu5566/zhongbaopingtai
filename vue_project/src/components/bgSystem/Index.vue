<template>
  <div class="bg-system-index">
    <div class="system-title">
      <Icon type="ios-home" size="24"></Icon><span>首页</span>
    </div>
    <Row class="pt10 pb10">
      <Row type="flex" justify="space-between">
        <Col span="7">
        <div class="dashboard-stat blue">
          <Icon type="android-people" size="120" color="#fff" style="opacity:0.6;"></Icon>
        </div>
        </Col>
        <Col span="7">
        <div class="dashboard-stat red1">
          <Icon type="android-contact" size="120" color="#fff" style="opacity:0.6;"></Icon>
        </div>
        </Col>
        <Col span="7">
        <div class="dashboard-stat green">
          <Icon type="ios-paper" size="120" color="#fff" style="opacity:0.6;"></Icon>
        </div>
        </Col>
      </Row>
    </Row>
  </div>
</template>
<script>
export default {
  data() {
    return {
    }
  },
  beforeRouteLeave(to, from, next) {
    window.sessionStorage.setItem('bg_user_filter', '')
    next();
  },
}

</script>
<style lang="less">
.bg-system-index {
  .dashboard-stat {
    display: block;
    padding: 20px 10px;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    border: none;
    outline: none;
  }
  .blue {
    background-color: #3598dc;
  }
  .red1 {
    background-color: #e7505a;
  }
  .green {
    background-color: #32c5d2;
  }
}

</style>
