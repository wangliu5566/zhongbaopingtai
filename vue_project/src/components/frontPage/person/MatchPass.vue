<template>
  <div class="match-pass" v-if="terminal=='pc'">
    <div v-if="totalCount!=0" style="overflow: hidden;">
      <div class="rows">
        <div class="pc-box" v-for="(item,index) in matchList" :key="index">
          <div class="img-wrap" @click="getDeatil(item.id)">
            <img :src="item.coverPath?baseUrl+item.coverPath:defaultImg">
            <div v-if="item.prizeNames!=0" v-for="innerItem in item.prizeNames">{{innerItem.prizeName}}</div>
          </div>
          <p class="title esp title2">{{item.workName}}</p>
          <p class="title esp small1">参赛者：{{item.username}}</p>
          <p class="title icon-praise">
            <span class="zan" @click="giveHit(item.id)" v-if="!item.isHits">{{item.hits}}</span>
            <span class="zan1" @click="giveHit(item.id)" v-else>{{item.hits}}</span>
          </p>
          <div id="share-box" @click='getWorkId(item.id,item.activityId,item.activityStatus)'>{{item.share}}</div>
        </div>
      </div>
      <div class="page" v-if="totalCount>6">
        <Page :total="totalCount" :current="parseInt(page)" :page-size="pageSize" show-total @on-change="change"></Page>
      </div>
    </div>
    <div v-if="totalCount==0">
      <p style="text-align:center;font-size:20px;line-height:400px;">您还没参加任何比赛哟！</p>
    </div>
    <!-- 作品详情模态框 -->
    <Modal v-model="matchDetail" width="806" id="matchDetailCss" :mask-closable="false" :closable="false">
      <div class="close-modal">
        <img src="/static/img/叉.png" @click="defineGoOut">
      </div>
      <div class="show-wrap" v-if="showAll">
        <!-- 展示小图 -->
        <div class="show-pro" v-for="item in detailList" :key="item.id" @click='oneDetail(item)'>
          <img v-if="item.type==1" :src="baseUrl+item.introImage">
          <img v-if="item.type==2" src="../../../../static/img/视频.png">
          <img v-if="item.type==3" :src="baseUrl+item.pdfImage">
        </div>
      </div>
      <div class="show-wrap1" v-if="!showAll">
        <!-- 展示大图和视频 -->
        <div class="video-warp" v-if="itemObj.type==1">
          <sliderWork :imgList="imgArr" :selectWorkId="selectWorkId"></sliderWork>
        </div>
        <div class="video-warp" v-if="itemObj.type==2&&showVideo">
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
        <div id="share-box-modal" @click='getWorkId(detailObj.id,detailObj.activityId,detailObj.activityStatus)'>{{detailObj.share}}</div>
        <div class="zan" v-if="!detailObj.isHits" @click="giveHit(detailObj.id,detailObj.activityId,detailObj.activityStatus)">{{detailObj.hits}}</div>
        <div class="zan1" v-if="detailObj.isHits" @click="giveHit(detailObj.id,detailObj.activityId,detailObj.activityStatus)">{{detailObj.hits}}</div>
      </p>
      <p class="small1">参赛者：{{detailObj.username}}</p>
      <div slot="footer"></div>
    </Modal>
    <!-- 分享 -->
    <Modal v-model="matchShare" width="500" id="pcShare" :mask-closable="false">
      <pcShare></pcShare>
    </Modal>
  </div>
</template>
<script>
import myVideo from "../../common/Videos.vue"
import sliderWork from "../../common/sliderWork.vue"
import pcShare from "../../common/PcShare.vue"
import vuePdf from 'vue-pdf'
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      isSign: true,
      liIndex: 0,
      matchList: [],
      totalCount: 1,
      page: sessionStorage.matchPage ? sessionStorage.matchPage : 1,
      pageSize: 6,
      defaultImg: "/static/img/small（图片）.png",
      matchDetail: false,
      showAll: true,
      detailList: [],
      detailObj: {},
      itemObj: {},
      matchShare: false,
      showVideo: false,
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
    this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
    this.terminal = IsPC() ? 'pc' : 'phone'
    if (this.isSign && this.terminal == 'pc') {
      this.getDatas(this.page, this.pageSize)
    } else if (this.isSign && this.terminal == 'phone') {
      this.$router.push('/phoneMatch')
    } else {
      this.$router.push("/frontPage")
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
    goIndex(index, path) {
      this.liIndex = index;
      this.$router.push(path)
    },
    getDatas(page, pageSize) {
      this.$http.get("/v1/frontend/works/lists", {
          params: {
            page: page,
            length: pageSize,
            userId: sessionStorage.userId,
            isMine: 1,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.matchList = res.data.data.lists;
            this.totalCount = res.data.data.count;
          }
        })
    },
    //分页切换当前页
    change(page) {
      this.page = page;
      sessionStorage.matchPage = page;
      this.getDatas(this.page, this.pageSize)
    },
    //点赞
    giveHit(id) {
      this.$http.post("/v1/frontend/works/add-hits", {
          workId: id,
          userId: sessionStorage.userId,
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.$Message.success({
              content: res.data.message,
              duration: 3,
            })
            this.getDatas(this.page, this.pageSize)
          } else {
            this.$Message.error({
              content: res.data.message,
              duration: 3,
            })
          }
        })
    },
    //获取作品详情
    getDeatil(id) {
      this.matchDetail = true;
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
            var pdfArr = [];
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
    //单击照片显示大图
    oneDetail(item) {
      this.showVideo = true;
      this.showAll = false;
      this.itemObj = item;
      this.selectWorkId = item.fileId;
      this.PDFPage = 1;
      if (item.type != 1) {
        this.putClickCount(this.itemObj.fileId, this.itemObj.type); //记录视频的点击量和首次点击的pdf,图片在轮播里会记录
      }
    },
    //单击大图，回退到九宫格小图
    allDeatil() {
      this.showAll = true;
      this.itemObj = {};
    },
    //pdf上一页
    previewPrevPage() {
      this.PDFPage -= 1;
    },
    //pdf下一页
    previewNextPage() {
      this.PDFPage += 1;
    },
    //分享前获取id
    getWorkId(id, activityId, activityStatus) {
      sessionStorage.workId = id;
      sessionStorage.isFromDetail = 2; //分享个人中心的作品,自定义分享地址，不能用当前的路径
      sessionStorage.shareActivityId = activityId;
      sessionStorage.shareActivityStatus = activityStatus;
      this.matchShare = true;
    },
    defineGoOut() {
      if (this.showAll == true) {
        this.matchDetail = false;
      } else {
        this.allDeatil()
      }
    }
  },
  watch: {
    matchShare: {
      handler(curVal, oldVal) {
        if (!curVal) {
          this.getDatas(this.page, this.pageSize)
        }　　　　　　
      }　　　　
    },
    matchDetail: {
      handler(curVal, oldVal) {
        if (!curVal) {
          this.showVideo = false;
        }　　　　　　
      }
    },
  }
}

</script>
<style lang='less'>
.match-pass {
  img {
    width: 100%;
    height: 100%;
    cursor: pointer;
  }

  .rows {
    width: 852px;
    margin: 0 auto;
    overflow: hidden;
    margin-top: 30px;
  }
  .pc-box {
    float: left;
    width: 266px;
    height: 298px;
    border: 1px solid #dcdcdc;
    margin: 0 27px 30px 0;
    cursor: pointer;
  }

  .pc-box:hover {
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
  .rows .pc-box:nth-child(3n) {
    margin-right: 0;
  }

  .img-wrap {
    width: 100%;
    height: 190px;
    overflow: hidden;
    font-size: 0;
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

  #share-box {
    display: block;
    float: right;
    display: inline-block;
    width: 70px;
    height: 40px;
    margin-top: -37px;
    background: url('../../../../static/img/转发.png') no-repeat 2px 10px;
    padding-left: 32px;
    font-size: 15px;
    line-height: 35px;
  }
  .zan,
  .zan1 {
    display: inline-block;
    width: 100px;
    height: 40px;
    background: url('../../../../static/img/赞.png') no-repeat 2px 13px;
    padding-left: 30px;
    line-height: 40px;
  }
  .zan1 {
    background: url('../../../../static/img/赞(4).png') no-repeat 2px 13px;
  }


  .ivu-btn-ghost {
    width: 88px;
    text-align: center;
    font-size: 14px;
  }

  .page {
    overflow: hidden;
    height: 40px;
    width: 100%;
    text-align: center;
    margin-bottom: 30px;
  }
}


/*详情模态框*/

#matchDetailCss {
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

  .show-pro:nth-child(3n) {
    margin: 0 0 25px 0;
  }
  .show-pro {
    float: left;
    width: 227px;
    height: 150px;
    margin: 0 25px 25px 0;
    img {
      width: 100%;
      height: 100%;
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
    background: url('../../../../static/img/赞.png') no-repeat 0 11px;
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
    background: url('../../../../static/img/赞(4).png') no-repeat 0 11px;
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
    background: url('../../../../static/img/转发.png') no-repeat 2px 11px;
    padding-left: 32px;
    cursor: pointer;
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
