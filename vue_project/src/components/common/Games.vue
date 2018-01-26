<template>
  <div class="games" v-if="terminal=='pc'" ref='games'>
    <div class="head">
      <h3><i class="pre"></i>参赛作品<i class="next"></i></h3>
      <p>Ongoing activities</p>
    </div>
    <div v-if="!endTime&&totalCount!=0" style="overflow: hidden;">
      <div class="rows">
        <div class="div-box" v-for="(item,index) in gameList" :key="index">
          <div class="img-wrap" @click="getDeatil(item.id)">
            <img :src="item.coverPath?baseUrl+item.coverPath:defaultImg">
            <div v-if="item.prizeNames!=0" v-for="innerItem in item.prizeNames">{{innerItem.prizeName}}</div>
          </div>
          <p class="title esp title2">{{item.workName}}</p>
          <p class="title esp small1">参赛者：{{item.username}}</p>
          <p class="title icon-praise">
            <span class="zan" @click="giveHit(item.id,1)" v-if="!item.isHits">{{item.hits}}</span>
            <span class="zan1" @click="giveHit(item.id,1)" v-else>{{item.hits}}</span>
          </p>
          <div id="share-box-activing" @click='getWorkId(item.id)'>{{item.share}}</div>
        </div>
      </div>
      <div style="overflow: hidden;height:50px;width:100%;margin-bottom: 20px;" v-if="totalCount>0">
        <Page :total="totalCount" :page-size="pageSize" show-total @on-change="change"></Page>
      </div>
    </div>
    <div v-if="!endTime&&totalCount==0">
      <p style="text-align:center;font-size:20px;line-height:50px;">新活动，争做第一人吧！</p>
    </div>
    <Row v-if="endTime">
      <Col span="18">
      <div class="rows1">
        <div class="div-box" v-for="(item,index) in gameList" :key="index">
          <div class="img-wrap" @click="getDeatil(item.id)">
            <img :src="item.coverPath?baseUrl+item.coverPath:defaultImg">
            <div v-if="item.prizeNames!=0" v-for="innerItem in item.prizeNames">{{innerItem.prizeName}}</div>
          </div>
          <p class="title esp title2">{{item.workName}}</p>
          <p class="title esp small1">参赛者：{{item.username}}</p>
          <p class="title icon-praise">
            <span class="zan" v-if="!item.isHits" @click="giveHit(item.id,1)">{{item.hits}}</span>
            <span class="zan1" v-else @click="giveHit(item.id,1)">{{item.hits}}</span>
          </p>
          <div id="share-box-activing" @click='getWorkId(item.id)'>{{item.share}}</div>
        </div>
      </div>
      <div style="overflow: hidden;height:50px;width:100%;" v-if="totalCount>0">
        <Page :total="totalCount" :page-size="pageSize" show-total @on-change="change"></Page>
      </div>
      <div v-if="endTime&&totalCount==0">
        <p style="text-align:center;font-size:20px;line-height:50px;">新活动，争做第一人吧！</p>
      </div>
      </Col>
      <Col span="6">
      <div class="ranking">
        <p class="ranking-title">
          &nbsp;&mdash;&mdash; &nbsp;最新排名&nbsp; &mdash;&mdash;
        </p>
        <ul class="ranking-li">
          <li v-if="rankList.length!=0" v-for="(item,index) in rankList" :key="item.username">
            <Row>
              <Col span="6">
              <i class="ranking-num">{{index+1}}</i>
              </Col>
              <Col span="18">
              <p class="esp username">{{item.username}}</p>
              <p class="esp user-detail">{{item.workName}}</p>
              </Col>
            </Row>
          </li>
        </ul>
        <p v-if="rankList.length==0" style="text-align:center;">暂无数据</p>
      </div>
      </Col>
    </Row>
    <!-- 作品详情模态框 -->
    <Modal v-model="modal1" width="806" id="workDetail" :mask-closable="false" :closable="false">
      <div class="close-modal">
        <img src="../../../static/img/叉.png" @click="defineGoOut">
      </div>
      <div class="show-wrap" v-if="showAll">
        <!-- 展示小图 -->
        <div class="show-pro" v-for="item in detailList" :key="item.id" @click='oneDetail(item)'>
          <img v-if="item.type==1" :src="baseUrl+item.introImage">
          <img v-if="item.type==2" src="../../../static/img/视频.png">
          <img v-if="item.type==3" :src="baseUrl+item.pdfImage">
        </div>
      </div>
      <div class="show-wrap1" v-else>
        <!--  展示大图 -->
        <div class="video-warp" v-if="itemObj.type==1">
          <sliderWork :imgList="imgArr" :selectWorkId="selectWorkId"></sliderWork>
        </div>
        <div class="video-warp" v-if="itemObj.type==2&&showVIdeo">
          <my-video :datas="baseUrl+itemObj.introImage"></my-video>
        </div>
        <div class="video-warp" v-if="itemObj.type==3">
          <div>
            <Button type="primary" :disabled="PDFPage==1" @click="previewPrevPage">上一页</Button>
            <Button type="primary" :disabled="PDFPage==PDFTotalPage" @click="previewNextPage">下一页</Button>
            <span style="font-size: 14px;margin-left: 10px;">当前第 {{PDFPage}}页，共 {{PDFTotalPage}} 页</span>
          </div>
          <vuePdf :src="baseUrl+itemObj.introImage" :page="PDFPage" @numPages="getPDFTotalPage"></vuePdf>
          <div style="height:40px;float:right;">
            <Button type="primary" :disabled="PDFPage==1" @click="previewPrevPage">上一页</Button>
            <Button type="primary" :disabled="PDFPage==PDFTotalPage" @click="previewNextPage">下一页</Button>
            <span style="font-size: 14px;margin-left: 10px;">当前第 {{PDFPage}}页，共 {{PDFTotalPage}} 页</span>
          </div>
        </div>
      </div>
      <p class="title">{{detailObj.workName}}
        <div id="share-box-modal" @click='getWorkId(detailObj.id)'>{{detailObj.share}}</div>
        <div class="zan" v-if="!detailObj.isHits" @click="giveHit(detailObj.id,2)">{{detailObj.hits}}</div>
        <div class="zan1" v-if="detailObj.isHits" @click="giveHit(detailObj.id,2)">{{detailObj.hits}}</div>
      </p>
      <p class="small1">参赛者：{{detailObj.username}}</p>
      <div slot="footer"></div>
    </Modal>
    <!-- 分享 -->
    <Modal v-model="modalShare" width="500" id="pcShare" :mask-closable="false">
      <pcShare></pcShare>
    </Modal>
  </div>
</template>
<script>
import myVideo from "../common/Videos.vue"
import sliderWork from "../common/sliderWork.vue"
import pcShare from "../common/PcShare.vue"
import vuePdf from 'vue-pdf'
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: "pc",
      endTime: false, //true是已结束，false是未结束
      active: 1,
      gameList: [],
      rankList: [],
      totalCount: 0,
      page: 1,
      pageSize: 8,
      modal1: false,
      detailList: [], //作品详情图片数组
      detailObj: {}, //作品详情
      showAll: true,
      itemObj: {},
      defaultImg: "../../../static/img/small（图片）.png",
      modalShare: false,
      imgArr: [], //作品详情图片数组
      pdfArr: [],
      selectWorkId: '',
      showVIdeo: true,
      PDFPage: 1,
      PDFTotalPage: 1,
    }
  },
  components: {
    myVideo,
    sliderWork,
    pcShare,
    vuePdf
  },
  mounted() {
    this.terminal = IsPC() ? 'pc' : 'phone';
    if (this.$route.query.activityId) {
      this.endTime = this.$route.query.status == 3 ? true : false; //3是已结束，2是正在进行，1是未进行
      this.activityId = this.$route.query.activityId
      if (this.terminal == 'pc') {
        this.pageSize = this.endTime ? 6 : 8;
      } else {
        this.pageSize = 4;
      }
      if (this.endTime) {
        this.getRankData()
        this.pageSize = 6;
      }
      this.getDatas(this.activityId, this.page, this.pageSize);
    } else {
      this.$router.go(-1);
    }

  },
  methods: {
    //获取pdf总页数
    getPDFTotalPage(totalPage) {
      if (totalPage != undefined) {
        this.PDFTotalPage = totalPage;
      } else {
        this.totalPage = 0;
      }
    },
    // 获取分享的id
    getWorkId(id) {
      if (sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null") {
        this.$Modal.confirm({
          title: '提示',
          content: '<h3>您还没有登录，立即去登录</h3>',
          onOk: () => {
            login(this.$route.fullPath)
          }
        });
      } else {
        sessionStorage.workId = id;
        sessionStorage.isFromDetail = 1; //分享详情页的作品,就用当前页面分享
        this.modalShare = true;
      }
    },
    //获取页面数据
    getDatas(id, page, length) {
      this.$http.get("/v1/frontend/works/lists", {
          params: {
            activityId: id,
            page: page,
            length: length,
            userId: sessionStorage.userId,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.gameList = res.data.data.lists;
            this.totalCount = res.data.data.count;
          }
        })
    },
    //获取排名数据
    getRankData() {
      this.$http.get("/v1/frontend/works/get-rank", {
          params: {
            activityId: this.activityId,
            length: 7,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.rankList = res.data.data;
          }
        })
    },
    //pdf上一页
    previewPrevPage() {
      this.PDFPage -= 1;
    },
    //pdf下一页
    previewNextPage() {
      this.PDFPage += 1;
    },
    //点赞
    giveHit(id, type) {
      if (sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null") {
        this.$Modal.confirm({
          title: '提示',
          content: '<h3>您还没有登录，立即去登录</h3>',
          onOk: () => {
            login(this.$route.fullPath)
          }
        });
      } else {
        this.$http.post("/v1/frontend/works/add-hits", {
            workId: id,
            userId: sessionStorage.userId,
          })
          .then((res) => {
            this.$Message.success({
              content: res.data.message,
              duration: 3,
              closable: true
            });
            if (res.data.status == 200) {
              if (type == 1) {
                this.getDatas(this.activityId, this.page, this.pageSize);
              } else if (type == 2) {
                this.getDatas(this.activityId, this.page, this.pageSize);
                this.getDeatil(id)
              }
            }
          })
      }
    },
    // 统计作品点击量
    putClickCount(id, type) {
      this.$http.post("/v1/frontend/works/record-click", {
          fileId: id,
          type: type,
        })
        .then((res) => {
          if (res.data.status == 200) {}
        })
    },
    //分页切换当前页
    change(page) {
      this.page = page;
      this.getDatas(this.activityId, this.page, this.pageSize);
    },
    //获取作品详情
    getDeatil(id) {
      this.modal1 = true;
      this.showAll = true;
      this.$http.get("/v1/frontend/works/get-info", {
          params: {
            workId: id,
            userId: sessionStorage.userId,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.detailList = res.data.data.files;
            var allArr = [];
            res.data.data.files.forEach(function(item, index) {
              if (item.type == 1) { //图片
                allArr.push(item)
              }
            })
            this.imgArr = allArr;
            this.detailObj = res.data.data.info;
          }
        })
    },
    //单击照片显示大图
    oneDetail(item) {
      this.showVIdeo = true;
      this.showAll = false;
      this.itemObj = item;
      this.selectWorkId = item.fileId;
      this.PDFPage = 1;
      this.putClickCount(this.itemObj.fileId, this.itemObj.type); //记录视频的点击量和首次点击的pdf,图片
    },
    //单击大图，回退到九宫格小图
    allDeatil() {
      this.showAll = true;
      this.itemObj = {};
    },
    defineGoOut() {
      if (this.showAll == true) {
        this.modal1 = false;
      } else {
        this.allDeatil()
      }
    }
  },
  watch: {
    modalShare: {　　　　
      handler(curVal, oldVal) {
        if (!curVal) {
          this.getDatas(this.activityId, this.page, this.pageSize);
        }
      }
    },
    modal1: {
      handler(curVal, oldVal) {
        if (!curVal) {
          this.showVIdeo = false;
        }　　　
      }
    },
  }
}

</script>
<style lang='less'>
/*参赛活动*/

.games {
  width: 1200px;
  margin: 0 auto;
  overflow: hidden;

  .rows {
    width: 1200px;
    overflow: hidden;
  }
  .rows1 {
    width: 900px;
    overflow: hidden;
  }


  .div-box {
    float: left;
    width: 280px;
    height: 300px;
    border: 1px solid #c9c9c9;
    margin: 0 26px 26px 0;
    cursor: pointer;
    background-color: #fff;
    position: relative;
  }
  .div-box:hover {
    .title2 {
      color: #f53051;
    }
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
  .rows1 .div-box:nth-child(3n) {
    margin-right: 0;
  }

  .img-wrap {
    width: 100%;
    height: 190px;
    overflow: hidden;
    position: relative;

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

    div {
      position: absolute;
      top: 0;
      left: 0;
      z-index: 189;
      width: 120px;
      height: 34px;
      background: url("../../../static/img/标签.png");
      text-align: center;
      line-height: 34px;
      color: white;
      font-size: 16px;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }
  }

  .img-wrap div:nth-child(2) {
    top: 8px;
  }
  .img-wrap div:nth-child(3) {
    top: 50px;
  }
  .img-wrap div:nth-child(4) {
    top: 92px;
  }
  .img-wrap div:nth-child(5) {
    top: 134px;
  }

  .title {
    line-height: 40px;
    font-size: 15px;
    padding: 0 15px;
  }
  .small1 {
    font-size: 14px;
    line-height: 22px;
  }

  .icon-praise {
    height: 40px;
  }

  #share-box-end {
    display: block;
    float: right;
    display: inline-block;
    width: 70px;
    height: 40px;
    margin-top: -37px;
    background: url('../../../static/img/转发.png') no-repeat 2px 10px;
    padding-left: 32px;
  }
  #share-box-activing {
    display: block;
    width: 70px;
    height: 40px;
    position: absolute;
    right: 0;
    bottom: 3px;
    line-height: 36px;
    padding-left: 32px;
    font-size: 15px;
    background: url('../../../static/img/转发.png') no-repeat 2px 10px;
  }
  .zan,
  .zan1 {
    display: inline-block;
    width: 100px;
    height: 40px;
    background: url('../../../static/img/赞.png') no-repeat 2px 13px;
    padding-left: 30px;
    line-height: 40px;
  }
  .zan1 {
    background: url('../../../static/img/赞(4).png') no-repeat 2px 13px;
  }

  .ivu-btn-ghost {
    border-color: #666;
    font-size: 14px;
  }

  .more-btn1 {
    width: 100px;
    margin-left: 0;
    font-size: 14px;
  }

  .describe {
    padding: 0 10px;
    padding-bottom: 10px;
    background-color: #fff;
  }

  .ranking {
    margin: 0 0 0 20px;
    width: 276px;
    height: 628px;
    overflow: hidden;
    background-color: #fff;
    padding: 10px 20px;
    transition: border linear .2s, box-shadow linear .5s;
    -moz-transition: border linear .2s, -moz-box-shadow linear .5s;
    -webkit-transition: border linear .2s, -webkit-box-shadow linear .5s;
    outline: none;
    border-color: #c0c0c0;
    box-shadow: 0 0 8px #c0c0c0;
    -moz-box-shadow: 0 0 8px #c0c0c0;
    -webkit-box-shadow: 0 0 8px #c0c0c0;


    .ranking-title {
      font-size: 20px;
      line-height: 48px;
      font-weight: 700;
      color: #080808;
    }

    .ranking-li {
      margin-top: 10px;
      overflow: hidden;
      width: 100%;
    }
    .ranking-li li {
      height: 72px;
      border-bottom: 1px dashed #666;
    }

    .username {
      color: #000;
      font-weight: 700;
      font-size: 16px;
      line-height: 20px;
      margin-top: 16px;
    }

    .user-detail {
      font-size: 14px;
      color: #666;
    }

    .ranking-num {
      display: inline-block;
      width: 40px;
      height: 40px;
      font-size: 26px;
      background-color: #fff;
      border-radius: 20px;
      line-height: 40px;
      font-weight: 700;
      margin-top: 15px;
      padding-left: 10px;
    }
    li:first-child i {
      background-color: #df3010;
      color: white;
    }
    li:nth-child(2) i {
      background-color: #f7472f;
      color: white;
    }
    li:nth-child(3) i {
      background-color: #f8851f;
      color: white;
    }
  }

  .ivu-page {
    margin: 15px 0;
    text-align: center;
  }
}


/*详情模态框*/

#workDetail {
  .ivu-modal-body {
    padding: 40px 37px 0 37px;
    position: relative;
  }
  .ivu-modal-footer {
    padding: 0;
  }
  .show-wrap,
  .show-wrap1,
  .video-warp {
    width: 100%;
    height: 100%;
    overflow: hidden;
  }
  .show-wrap1 img {
    width: 100%;
  }

  .show-pro:nth-child(3n) {
    margin: 0 0 25px 0;

    img {
      width: 100%;
      height: 100%;
    }
  }
  .show-pro {
    float: left;
    width: 227px;
    height: 150px;
    margin: 0 25px 25px 0;

    img {
      width: 100%;
      height: 100%;
      cursor: pointer;
    }
  }
  .title {
    line-height: 45px;
    font-size: 18px;
    font-size: 700;
    color: #000;
  }

  .small1 {
    font-size: 16px;
    line-height: 30px;
    color: #666;
  }

  .title span {
    float: right;
    cursor: pointer;
  }
  .zan,
  .zan1 {
    width: 70px;
    height: 40px;
    background: url('../../../static/img/赞.png') no-repeat 0 11px;
    padding-left: 30px;
    line-height: 42px;
    font-size: 14px;
    color: #333;
    cursor: pointer;
    position: absolute;
    bottom: 30px;
    right: 90px;
  }
  .zan1 {
    background: url('../../../static/img/赞(4).png') no-repeat 0 11px;
  }

  #share-box-modal {
    float: right;
    width: 60px;
    height: 40px;
    position: absolute;
    bottom: 30px;
    line-height: 40px;
    right: 30px;
    font-size: 14px;
    background: url('../../../static/img/转发.png') no-repeat 2px 11px;
    padding-left: 32px;
  }

  .ivu-modal-confirm-footer,
  .ivu-modal-confirm {
    margin: 20px 0;
    overflow: hidden;
  }
  .show-wrap .vjs-tech {
    top: -116px;
  }
  .show-wrap {
    .video-js.vjs-custom-skin .vjs-big-play-button {
      top: 20%;
    }
  }
  .close-modal {
    float: right;
    margin-top: -40px;
    margin-right: -78px;
    overflow: hidden;

    img {
      width: 60%;
      margin: 20%;
    }
  }

  .close-modal img:hover {
    transform: rotate(90deg);
    -webkit-transform: 2s !important;
    width: 58%;
    margin: 21%;
    transition: transform 0.18s, -webkit-transform 0.18s !important;
  }
}

</style>
