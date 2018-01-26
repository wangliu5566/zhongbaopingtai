<template>
  <div class="all-active">
    <div class="content" v-if="terminal=='pc'">
      <div class="head">
        <h3><i class="pre"></i>所有活动报道<i class="next"></i></h3>
        <p>All activities</p>
      </div>
      <div class="rows">
        <div class="pc-box" v-for="(item,index) in news" :key="index" @click="goNews(item.id)">
          <div class="img-wrap">
            <img :src="baseUrl+item.coverPath" alt="">
          </div>
          <p class="title esp">{{item.title}}</p>
          <p class="describe">{{item.newsSummary}}</p>
          <div class="show-shadow" id="showShadow"></div>
        </div>
      </div>
      <div class="more-btn" v-if="newsCount>8">
        <Button type="ghost" @click="showMore">{{showWord}}</Button>
      </div>
    </div>


    <div v-else class="phone-new-list">
        <div class="phone-content">
            <p class="hr"></p>
            <div class="title">
                <span>所有活动报道</span>
                <span class="look-more" v-if="!isShowMore" @click="showMore()">查看更多 &nbsp;
                            <i class="phone-icon"></i>
                        </span>
                <span class="look-more" v-else>查看更多 &nbsp;<i class="phone-icon"></i></span>
            </div>
            <div class="show-img">
                <div class="div-box" v-for="item in news" :key="item.id" @click="goNews(item.id)">
                    <img :src="baseUrl+item.coverPath">
                    <p class="myFloat esp">{{item.title}}</p>
                </div>
            </div>
            <div class="more-btn" v-if="isShowMore">
                <Button type="ghost" @click="showMore">收起报道</Button>
            </div>
        </div>
    </div>

  </div>
</template>
<script>
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      news: [],
      newsCount: 10,
      isShowMore: false,
      showWord: '更多报道'
    }
  },
  created() {
    this.terminal = document.body.clientWidth < 768 ? 'phone' : 'pc';
    if (this.terminal == 'pc') {
      this.getDatas(8);
    } else {
      this.getDatas(4);
    }
  },
  methods: {
    getDatas(length) {
      this.$http.get("/v1/frontend/index/get-news", {
          params: {
            length: length
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.newsCount = res.data.data.newsCount
            this.news = res.data.data.news;
          }
        })
        .catch((err) => {
          console.log(err)
        })
    },
    goNews(id) {
      this.$router.push({ path: '/frontPage/news', query: { newId: id } })
    },
    showMore() {
      if (!this.isShowMore) {
        this.isShowMore = true;
        this.$http.get("/v1/frontend/index/get-news", {
            params: {
              length: -1
            }
          })
          .then((res) => {
            if (res.data.status == 200) {
              if (this.terminal == 'pc') {
                this.showWord = "收起报道"
                this.news = res.data.data.news;
              } else {
                this.news = res.data.data.news;
              }
            }
          })
          .catch((err) => {
            alert(err)
          })
      } else {
        this.isShowMore = false;
        this.showWord = "更多报道"
        this.getDatas(this.terminal == 'pc' ? 8 : 4);
      }
    },
  }
}

</script>
<style lang="less">
.all-active {
  width: 100%;
  overflow: hidden;
  background-color: #f2f2f2;
}
.phone-new-list{
    width: 100%;
    background-color: #fff;
    overflow: hidden;

    .phone-content {
      width: 94%;
      margin: 0 auto;
      background-color: #fff;
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
  }

  .look-more {
    float: right;
    color: #999;
    font-size: 0.14rem;
    font-weight: 400;
    position: relative;
    padding-right: 10px;
  }
  .phone-icon {
    display: inline-block;
    width: 20%;
    height: 0.18rem;
    background: url('/static/img/right.png') no-repeat;
    -webkit-background-size: 0.15rem;
    background-size: 0.15rem;
    position: absolute;
    top: 0.09rem;
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


/*pc*/

.content {
    width: 100%;
    background-color: inherit;
    overflow: hidden;

    .head {
        width: 1200px;
        margin: 0 auto;
    }

    h3 {
        font-size: 34px;
    }

    .rows,.h3 {
        width: 1200px;
        margin: 0 auto;
        overflow: hidden;
    }
    .pc-box {
        float: left;
        width: 266px;
        height: 280px;
        margin: 0 42px 0 0;
        cursor: pointer;
        background-color: #fff;
    }

    .rows .pc-box:nth-child(4n) {
        margin-right: 4px;
    }
    .rows .pc-box:nth-child(4n+1) {
        margin-left: 4px;
    }

    .pc-box:hover {
        img{  
            opacity:.9;
            -webkit-transform:scale(1.3);
            -moz-transform:scale(1.3);
            -o-transform:scale(1.3);
            -ms-transform:scale(1.3);
            transform:scale(1.3);
        } 
        .title{
            color: #f53051;
        }
    }

    .img-wrap {
        width: 100%;
        height: 140px;
        overflow: hidden;

        img {
            width: 100%;
            height: 100%;
            opacity: 1;
            -webkit-transition:all .4s ease-in-out;
            -moz-transition:all .4s ease-in-out;
            -o-transition:all .4s ease-in-out;
            -ms-transition:all .4s ease-in-out;
            transition:all .4s ease-in-out;
        }
        
    }

    .title {
        height: 40px;
        line-height: 40px;
        padding: 0 15px;
        margin-top: 5px;
        font-size: 16px;
        color: #080808;
    }

    .describe {
        height: 60px;
        padding: 0 15px;
        font-size: 14px;
        line-height: 20px;
        color: #666;
        overflow: hidden;
    }
    .show-shadow {
        height: 35px;
        background: url("/static/img/out.png") no-repeat #f2f2f2;
    }

    .ivu-btn-ghost {
        background-color: #fff;
        font-size: 14px;
    }
    .more-btn {
        width: 1200px;
        margin: 0 auto;
        margin-bottom: 30px;
     }
}

</style>
