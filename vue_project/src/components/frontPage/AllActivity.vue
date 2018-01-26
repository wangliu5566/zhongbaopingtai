<template>
  <div class="pc-activity" v-if="this.terminal=='pc'">
    <!-- 轮播 -->
    <div class="pc-slider">
      <slider></slider>
    </div>
    <div class="select-div">
      <Form class="form1" :model="formItem" :label-width="103" inline>
        <Form-item label="活动类型：">
          <Select v-model="formItem.type" placeholder="请选择">
            <Option value="0">不限</Option>
            <Option value="1">大赛活动</Option>
            <Option value="2">问答活动</Option>
          </Select>
        </Form-item>
        <Form-item label="活动状态：" style="margin-left: 20px;">
          <Select v-model="formItem.status" placeholder="请选择">
            <Option value="0">不限</Option>
            <Option value="1">即将开始的活动</Option>
            <Option value="2">正在进行的活动</Option>
            <Option value="3">已结束的活动</Option>
          </Select>
        </Form-item>
      </Form>
    </div>
    <div class="contents">
      <div class="head">
        <h3><i class="pre"></i>活动列表<i class="next"></i></h3>
        <p>List of activites</p>
      </div>
      <div class="rows">
        <div class="div-box" v-for="(item,index) in activities" :key="index" @click="goPath(item.id,item.type,item.status)">
          <div class="img-wrap">
            <img :src="baseUrl+item.smallImage" alt="">
          </div>
          <p class="title esp">{{item.name}}</p>
        </div>
      </div>
      <div class="page">
        <Page :total="totalCount" :current="parseInt(page)" :page-size="pageSize" show-total @on-change="change"></Page>
      </div>
    </div>
  </div>
  <!-- 手机端 -->
  <div class="phone-activity" v-else>
    <!-- 轮播 -->
    <div class="phone-slider">
      <swiper></swiper>
    </div>
    <div class="select-activity">
      <p style="border-bottom: 1px solid #c0c0c0" @click="modal2=true">活动类型：
        <span class="select-more">
					{{formItem.type==0?'不限':formItem.type==1?"大赛活动":'问答活动'}}
					<i class="phone-more"></i>
				</span>
      </p>
      <p @click="modal1=true">活动状态：
        <span class="select-more">
					{{formItem.status==0?'不限':formItem.status==2?"正在进行的活动":formItem.status==1?'即将开始的活动':'已结束的活动'}}
					<i class="phone-more"></i>
				</span>
      </p>
    </div>
    <div class="kong"></div>
    <div class="phone-content">
      <p class="hr"></p>
      <div class="title">
        <span>活动列表</span>
        <span v-if="totalCount>8" class="look-more" @click="showMore()">换一换
					<i class="phone-icon"></i>
				</span>
      </div>
      <div class="show-img">
        <div class="div-box" v-for="(item,index) in activities" :key="index" @click="goPath(item.id,item.type,item.status)">
          <img :src="baseUrl+item.smallImage" alt="">
          <p class="esp myFloat">{{item.name}}</p>
        </div>
      </div>
    </div>
    <Modal v-model="modal1" style="width: 100%;" :mask-closable="false" id="show-list">
      <p slot="header" class='btns'>
        <span @click="modal1=false">取消</span>
        <span style="float: right;" @click="selectDatas(1)">确定</span>
      </p>
      <ul class="select-ul">
        <li @click="getSelectStatus(0)"> 不限 <span :class="formItem.status==0?'show-ok':''"></span></li>
        <li @click="getSelectStatus(1)"> 即将开始的活动 <span :class="formItem.status==1?'show-ok':''"></span></li>
        <li @click="getSelectStatus(2)"> 正在进行的活动 <span :class="formItem.status==2?'show-ok':''"></span></li>
        <li @click="getSelectStatus(3)"> 已结束的活动 <span :class="formItem.status==3?'show-ok':''"></span></li>
      </ul>
    </Modal>
    <Modal v-model="modal2" style="width: 100%;" :mask-closable="false" id="show-list1">
      <p slot="header" class='btns'>
        <span @click="modal2=false">取消</span>
        <span style="float: right;" @click="selectDatas(2)">确定</span>
      </p>
      <ul class="select-ul">
        <li @click="getSelectType(0)">不限 <span :class="formItem.type==0?'show-ok':''"></span></li>
        <li @click="getSelectType(1)">大赛活动 <span :class="formItem.type==1?'show-ok':''"></span></li>
        <li @click="getSelectType(2)">问答活动 <span :class="formItem.type==2?'show-ok':''"></span></li>
      </ul>
    </Modal>
  </div>
</template>
<script>
import slider from "../common/Slider.vue"
import swiper from "../common/Swiper.vue"
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      activities: [],
      totalCount: 0,
      page: 1,
      pageSize: 8,
      formItem: {
        status: "0", // 查询活动状态 不限传0 正在进行值2 已结束3
        type: '0', //查询活动类型 不限传0 大赛传1 问答传2
      },
      activities: [],
      modal1: false, //活动状态
      modal2: false, //活动类型
      showType: 1,
    }
  },
  components: {
    slider,
    swiper
  },
  mounted() {
    this.terminal = IsPC() ? 'pc' : 'phone'
    this.page = sessionStorage.page ? sessionStorage.page : 1;
    this.getDatas(this.page, this.pageSize, this.formItem.status, this.formItem.type)
  },
  methods: {
    getDatas(page, length, status, type) {
      this.$http.get("/v1/frontend/index/get-activity", {
          params: {
            page: page,
            length: length,
            status: status, //查询活动状态 所有传0 正在进行值2 已结束传3
            type: type, //查询活动类型 所有传0 大赛传1 问答传2
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.activities = res.data.data.activities;
            this.totalCount = parseInt(res.data.data.activityCount);
          }
        })
    },
    //分页切换当前页
    change(page) {
      this.page = page;
      sessionStorage.page = page;
      this.getDatas(this.page, this.pageSize, this.formItem.status, this.formItem.type)
    },
    //手机端换一换
    showMore() {
      var totalPage = this.totalCount % 8 == 0 ? this.totalCount / 8 : this.totalCount / 8 + 1
      this.page++;
      if (this.page > totalPage) {
        this.page = 1;
      }
      sessionStorage.page = this.page;
      this.getDatas(this.page, this.pageSize, this.formItem.status, this.formItem.type)
    },
    goPath(id, type, status) {
      if (type == 1) { //活动详情
        if (status == 1) {
          this.$router.push({ path: "/frontPage/initDetail", query: { activityId: id, type: type, status: status } })
        } else {
          this.$router.push({ path: "/frontPage/detail", query: { activityId: id, type: type, status: status } })
        }
      } else if (type == 2) { //跳转到新闻报道页面
        if (status == 1) {
          this.$router.push({ path: "/frontPage/initQuestion", query: { questionId: id, type: type, status: status } })
        } else {
          this.$router.push({ path: "/frontPage/question", query: { questionId: id, type: type, status: status } })
        }
      }
    },
    getSelectStatus(index) {
      this.formItem.status = index;
    },
    getSelectType(index) {
      this.formItem.type = index;
    },
    //模态框请求数据
    selectDatas(index) {
      this.page = 1;
      this.getDatas(this.page, this.pageSize, this.formItem.status, this.formItem.type)
      if (index == 1) {
        this.modal1 = false;
      } else {
        this.modal2 = false;
      }
    }
  },
  watch: {
    formItem: {　　　　　　　　　 //注意：当观察的数据为对象或数组时，curVal和oldVal是相等的，因为这两个形参指向的是同一个数据对象
      　　　　　　　　　　
      handler(curVal, oldVal) {　　　　　　　　　　　　
        this.formItem = curVal;
        this.page = 1;
        sessionStorage.page = this.page;
        if (this.terminal == 'pc') {
          this.getDatas(this.page, this.pageSize, this.formItem.status, this.formItem.type)
        }　　　　　　　　　　
      },
      　　　　　　　　　　deep: true　　　　　　　　
    }
  }
}

</script>
<style lang="less">
.phone-activity {

  .phone-slider {
    height: 1.75rem;
    overflow: hidden;
  }

  .select-activity {
    width: 100%;
    background-color: #fff;
    height: 1.06rem;
    padding: 0 2%
  }

  .select-activity p {
    height: 0.53rem;
    line-height: 0.53rem;
    font-size: 0.15rem;
  }

  .kong {
    width: 100%;
    height: 0.1rem;
    background-color: #eeeeee;
  }

  .phone-content {
    width: 94%;
    margin: 0 auto;
    padding-bottom: 15px;
  }

  .hr {
    width: 5%;
    height: 0.04rem;
    margin-top: 0.2rem;
    background-color: #000;
  }

  .title {
    font-size: 0.16rem;
    font-weight: 700;
    line-height: 0.36rem;
    color: #080808;
    margin-bottom: 0.1rem;
  }

  .look-more {
    float: right;
    color: #999;
    font-size: 0.14rem;
    font-weight: 400;
    position: relative;
    padding-right: 9%;
  }
  .phone-icon {
    display: inline-block;
    width: 30%;
    height: 0.18rem;
    background: url('../../../static/img/circle.png') no-repeat;
    -webkit-background-size: 0.15rem;
    background-size: 0.15rem;
    position: absolute;
    top: 0.09rem;
    right: 0;
  }
  .select-more {
    float: right;
    font-size: 0.14rem;
    font-weight: 400;
    position: relative;
    padding-right: 25px;
    overflow: hidden;
  }
  .phone-more {
    display: inline-block;
    width: 20px;
    height: 0.18rem;
    background: url('../../../static/img/right2.png') no-repeat;
    -webkit-background-size: 0.1rem;
    background-size: 0.09rem;
    position: absolute;
    top: 0.18rem;
    right: 0;
  }
  .show-img {
    width: 100%;
    overflow: hidden;
  }
  .div-box {
    float: left;
    width: 48%;
    height: 1.25rem;
    margin-bottom: 4%;
    position: relative;
    border: 1px solid #c0c0c0;
  }
  .div-box img {
    width: 100%;
    height: 100%;
  }
  .div-box:nth-child(2n) {
    margin-left: 4%;
  }
  .myFloat {
    background-color: #000;
    opacity: 0.6;
    color: white;
    width: 100%;
    overflow: hidden;
    position: absolute;
    bottom: 0;
    left: 0;
    padding: 0 3%;
  }
}

#show-list1 {

  .ivu-modal,
  .ivu-modal-content {
    margin: 0;
    height: 1.8rem;
    top: 0;
  }
  .ivu-modal-content {
    border-radius: 0;
  }
  .ivu-modal-wrap {
    position: fixed;
    overflow: auto;
    top: 73%;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1000;
    -webkit-overflow-scrolling: touch;
    outline: 0
  }
  .ivu-modal-footer,
  .ivu-modal-close {
    display: none;
  }

  .ivu-modal-header,
  .ivu-modal-body {
    padding: 0 2%;
  }
  .ivu-modal-header .btns {
    height: 0.38rem;
    font-size: 0.14rem;
    color: #1e76e2;
    font-weight: 400;
    line-height: 0.38rem;
  }
  .select-ul {
    width: 100%;
    overflow: hidden;
  }
  .select-ul li {
    height: 0.44rem;
    border-bottom: 1px solid #dbdbdb;
    line-height: 0.44rem;
    font-size: 0.15rem;
    color: #000;
  }
  .select-ul li:nth-child(3) {
    border-bottom: none;
  }
  .show-ok {
    float: right;
    display: inline-block;
    width: 10%;
    height: 100%;
    background: url('../../../static/img/ok.png') no-repeat 0 0.1rem;
    background-size: 0.2rem;
  }
}

#show-list {
  .ivu-modal,
  .ivu-modal-content {
    margin: 0;
    height: 2.3rem;
    top: 0;
  }
  .ivu-modal-content {
    border-radius: 0;
  }
  .ivu-modal-wrap {
    position: fixed;
    overflow: auto;
    top: 65.5%;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1000;
    -webkit-overflow-scrolling: touch;
    outline: 0
  }
  .ivu-modal-footer,
  .ivu-modal-close {
    display: none;
  }

  .ivu-modal-header,
  .ivu-modal-body {
    padding: 0 2%;
  }
  .ivu-modal-header .btns {
    height: 0.38rem;
    font-size: 0.14rem;
    color: #1e76e2;
    font-weight: 400;
    line-height: 0.38rem;
  }
  .select-ul {
    width: 100%;
    overflow: hidden;
  }
  .select-ul li {
    height: 0.44rem;
    border-bottom: 1px solid #dbdbdb;
    line-height: 0.44rem;
    font-size: 0.15rem;
    color: #000;
  }
  .show-ok {
    float: right;
    display: inline-block;
    width: 10%;
    height: 100%;
    background: url('../../../static/img/ok.png') no-repeat 0 0.1rem;
    background-size: 0.2rem;
  }
}


.pc-activity {
  width: 100%;
  background-color: #f2f2f2;


  .pc-slider {
    width: 100%;
  }

  .select-div {
    width: 100%;
    background-color: #fff;
    height: 130px;
  }
  .form1 {
    width: 1200px;
    margin: 0 auto;
    padding-top: 40px;
  }
  .ivu-select-selection {
    width: 260px;
    height: 36px;
    line-height: 36px;
  }

  .contents {
    width: 1200px;
    margin: 0 auto;
    overflow: hidden;
    padding-bottom: 20px;
  }

  .rows {
    width: 1200px;
    overflow: hidden;
  }
  .div-box {
    float: left;
    width: 266px;
    height: 250px;
    border: 1px solid #dcdcdc;
    margin: 0 45px 30px 0;
    cursor: pointer;
  }

  .div-box:hover {
    color: #f53051;
    img {
      opacity: .9;
      -webkit-transform: scale(1.3);
      -moz-transform: scale(1.3);
      -o-transform: scale(1.3);
      -ms-transform: scale(1.3);
      transform: scale(1.3);
    }
  }

  .rows .div-box:nth-child(4n) {
    margin-right: 0;
  }

  .img-wrap {
    width: 100%;
    height: 190px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      opacity: 1;
      -webkit-transition: all .4s ease-in-out;
      -moz-transition: all .4s ease-in-out;
      -o-transition: all .4s ease-in-out;
      -ms-transition: all .4s ease-in-out;
      transition: all .4s ease-in-out;
    }
  }

  .title {
    height: 60px;
    line-height: 60px;
    font-size: 17px;
    padding-left: 15px;
  }

  .title:hover {
    color: #f53051;
  }

  .ivu-btn-ghost {
    font-size: 14px;
  }

  .page {
    overflow: hidden;
    height: 50px;
    width: 100%;
  }

  .ivu-page {
    text-align: center;
  }

  .ivu-form-item-label {
    font-size: 18px;
    color: #333;
  }
  li.ivu-select-item,
  .ivu-select-selected-value {
    font-size: 16px!important;
    color: #333;
  }
}

</style>
