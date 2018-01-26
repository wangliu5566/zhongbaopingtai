<template>
  <div class="production" v-if="terminal!='pc'" ref="productionDetail">
    <div class="detail">
      <div class="img-wrap" v-if="imgList.length!=0">
        <matchSwiper :imgs="imgList"></matchSwiper>
        <p style="text-align: center;line-height: 0.2rem;">请左滑或右滑查看更多作品图片</p>
      </div>
      <div class="video-wrap" v-if="videoList.length!=0">
        <div class="video1" v-for="(item,index) in videoList" v-if="videoIndex == index">
          <matchVideo :datas="baseUrl+item.introImage"></matchVideo>
        </div>
        <div class="toggle-video" v-if="videoList.length >1">
          <div @click="preVideo">
            <i class="pre-video" :class="videoIndex==0?'no-click1':''"></i>上一个视频
          </div>
          <div @click="nextVideo" style="text-align: right;">下一个视频
            <i class="next-video" :class="videoIndex ==videoList.length-1 ?'no-click2':''"></i>
          </div>
        </div>
      </div>
      <p class="title">{{getObj.workName}}</p>
      <div class="word-wrap1">
        <p class="author" style="width: 70%">参赛者：{{getObj.username}}</p>
        <p class="pro-praise" style="width: 30%">
          <span class="zan" v-if="!getObj.isHits" @click="giveHit()">{{getObj.hits}}</span>
          <span class="zan1" v-if="getObj.isHits" @click="giveHit()">{{getObj.hits}}</span>
          <span class="look_more" @click='getWorkId()'>{{getObj.share}}</span>
        </p>
      </div>
    </div>
    <div class="hr1"></div>
    <!-- 更多作品 -->
      <moreActive></moreActive>

    <!-- 分享的模态框 -->
    <Modal v-model="ShareDetail" :mask-closable="false" id="bottom">
      <pcShare></pcShare>
      <button class="close-share" @click="ShareDetail=false">关&nbsp;闭</button>
      </Modal>
  </div>
</template>
<script>
import moreActive from "../common/MoreActive.vue"
import matchSwiper from "../common/SwiperMatch.vue"
import matchVideo from "../common/matchVideo.vue"
import  pcShare from "../common/PcShare.vue"
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      productionId: "",
      getObj: {
        id:'',
        workName: '',
        username: '',
        share: '0',
        hits: "0",
        isHits: false,
        activityId:"",
        activityStatus:'',
      },
      ShareDetail: false,
      videoList: [],
      imgList: [],
      videoIndex: 0,
    }
  },
  components: {
    pcShare,
    moreActive,
    matchSwiper,
    matchVideo,
  },
  mounted() {
    this.terminal = IsPC() ? 'pc' : 'phone'
    if (this.$route.query.productionId) {
      this.productionId = this.$route.query.productionId;
      this.getDetail()
    } else{
      this.$router.push("/frontPage/index")
    }
  },
  methods: {
    getDetail() {
      this.$http.get("/v1/frontend/works/get-info", {
          params: {
            workId: this.productionId,
            userId: sessionStorage.userId,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.getObj = res.data.data.info;

            if(this.terminal=='pc'){
              this.$router.push({ path: "/frontPage/detail", query: {activityId: this.getObj.activityId,type: 1, status:this.getObj.activityStatus } })
            }
            var arr = res.data.data.files;
            var imgList = [];
            var videoList = [];
            arr.forEach(function(item, index) {
              if (item.type == 1) {
                imgList.push(item)
              } else if (item.type == 2) {
                videoList.push(item)
              }
            })
            this.imgList = imgList
            this.videoList = videoList
            if(this.videoList.length>0){  //记录首次视频点击量
              this.putClickCount(this.videoList[0].fileId,this.videoList[0].type)
            }
          }
        })
    },
    //点赞
    giveHit() {
      if (sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null") {
        this.$Modal.confirm({
          title: '',
          content: '<h3>您还没有登录，立即去登录</h3>',
          onOk: () => {
            login(this.$route.fullPath)
          }
        });
      } else {
        this.$http.post("/v1/frontend/works/add-hits", {
            workId: this.productionId,
            userId: sessionStorage.userId,
          })
          .then((res) => {
            this.$Message.success({
              content: res.data.message,
              duration:3,
              closable: true
            });
            if (res.data.status == 200) {
              this.getDetail()
            }
          })
      }
    },
    // 统计作品点击量
    putClickCount(id,type){
      this.$http.post("/v1/frontend/works/record-click",{
        fileId:id,
        type:type,
      })
      .then((res)=>{
        if(res.data.status==200){
        }
      })
    },
    //点击分享
    getWorkId() {
      if (sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null") {
        this.$Modal.confirm({
          title: '',
          content: '<h3>您还没有登录，立即去登录</h3>',
          onOk: () => {
            login(this.$route.fullPath)
          }
        });
      } else {
        sessionStorage.workId = this.productionId;
        sessionStorage.isFromDetail=1;  //分享详情页的作品,就用当前页面分享
        this.ShareDetail = true;
      }
    },
    preVideo() {
      if (this.videoIndex <= 0) {
        this.$Message.warning({
          content:"已经是第一个视频咯",
          duration:3,
        });
      } else {
        this.videoIndex--;
      }
    },
    nextVideo() {
      if (this.videoIndex == this.videoList.length - 1) {
        this.$Message.warning({
          content:"已经是最后一个视频咯",
          duration:3,
        });
      } else if (this.videoIndex < this.videoList.length - 1) {
        this.videoIndex++;
      }
    }
  },
  watch: {
    ShareDetail: {　　　　　　　　
      handler(curVal, oldVal) {
        if (!curVal) {
          this.getDetail()
        }　　　　　　
      }　　　　
    },
    videoIndex:{
      handler(curVal, oldVal) {
       this.putClickCount(this.videoList[this.videoIndex-1].fileId,this.videoList[this.videoIndex-1].type)　　　　
      }　
    }
  }
}

</script>
<style lang="less">
.production {
  width: 100%;

  .detail {
    width: 94%;
    margin: 0 auto;
    overflow: hidden;
    background-color: #fff;
  }

  .img-wrap,
  .video-wrap,
  .video {
    width: 100%;
    height: 1.95rem;
    margin: 10px 0;
    overflow: hidden;
  }
  .video-wrap {
    height: 2.1rem;
    position: relative;
  }
  .video1 {
    width: 100%;
    position: absolute;
    top: 0;
    height: 1.75rem;
    overflow: hidden;
  }
  .toggle-video {
    height: 0.3rem;
    margin-top: 1.85rem;
    line-height: 0.25rem;
  }
  .toggle-video div {
    width: 50%;
    float: left;
    height: 100%;
    line-height: 0.3rem;
  }
  .pre-video,
  .next-video {
    display: inline-block;
    width: 25px;
    height: 25px;
    background: url('/static/img/上一步1.png') no-repeat 0 5px;
    -webkit-background-size: 20px;
    background-size: 20px;
    float: left;
  }
  .next-video {
    background: url('/static/img/下一步.png') no-repeat 0 5px;
    -webkit-background-size: 20px;
    background-size: 20px;
    float: right;
    margin-left: 6px;
  }
  .toggle-video .no-click1 {
    background: url('/static/img/上一步.png') no-repeat 0 5px;
    -webkit-background-size: 20px;
    background-size: 20px;
  }
  .toggle-video .no-click2 {
    background: url('/static/img/下一步1.png') no-repeat 0 5px;
    -webkit-background-size: 20px;
    background-size: 20px;
  }
  .title {
    font-size: 0.16rem;
    margin-bottom: 10px;
  }
  .word-wrap1 {
    height: 0.4rem;
    position: relative;
  }
  .word-wrap1 p {
    float: left;
    font-size: 0.14rem;
    margin-bottom: 0.1rem;
  }

  .hr1 {
    height: 15px;
    background-color: #f2f2f2;
  }

  .pro-praise .look_more {
    background: url('/static/img/转发.png') no-repeat 2px 5px;
    margin: 0;
  }

  .zan,
  .zan1,
  .look_more {
    display: block;
    width: 50%;
    height: 30px;
    background: url('/static/img/赞.png') no-repeat 2px 4px;
    padding-left: 30px;
    line-height: 30px;
    font-size: 0.13rem;
    color: #333;
    float: left;
    font-size: 0.14rem;
  }
  .zan1 {
    background: url('/static/img/赞(4).png') no-repeat 2px 4px;
  }
}

/*模态框*/
  #bottom{
    .ivu-modal,.ivu-modal-content{
      margin:0;
      height:1.8rem;
      top: 0;
    }
    .ivu-modal-content{
      border-radius: 0;
      background-color: #fff;
    }
    .ivu-modal-wrap{
        position: fixed;
        overflow: auto;
        top:73%;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1000;
        -webkit-overflow-scrolling: touch;
        outline: 0
    }
    .ivu-modal-footer,.ivu-modal-close{
      display: none;
    }

    .ivu-modal-header,.ivu-modal-body{
      padding: 0 2%;
    }
    .share-title{
      line-height: 0.4rem;
    }
    .close-share{
      width: 100%;
      line-height: 0.3rem;
      text-align: center;
      outline: none;
      background-color: #bf233b;
      border:none;
      margin-top: 0;
      color:white;
      font-weight: 700;
      font-size: 0.14rem;
    }

    .ivu-modal-close{
      display: none;
    }

    .phone-warning{
      line-height:0.3rem;
      font-size:0.12rem;
      text-align:center;
      overflow: hidden;
    }
  }
</style>
