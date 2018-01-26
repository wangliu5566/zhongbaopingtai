<template>
  <div style="width: 100%;">
    <div v-if="terminal=='pc'" class="pc-activing">
      <div class="head">
        <h3><i class="pre"></i>正在进行的活动<i class="next"></i></h3>
        <p>Ongoing activities</p>
      </div>
      <div class="rows">
        <div class="pc-box" v-for="(item,index) in activities" :key="index" @click="goPath(item.id,item.type,item.status)">
          <div class="img-wrap">
            <img :src="baseUrl+item.smallImage" alt="">
          </div>
          <p class="title esp">{{item.name}}</p>
        </div>
      </div>
      <div class="more-btn">
        <Button type="ghost" @click="showMore()">更多活动</Button>
      </div>
    </div>
    <div v-else class="phone-activing">
      <p class="hr"></p>
      <div class="title">
        <span>正在进行的活动</span>
        <span class="look-more" @click="showMore()">查看更多 &nbsp;
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
  </div>
</template>
<script>
export default {
  data() {
    return {
      baseUrl: baseUrl,
      terminal: 'pc',
      activities: [],
      activityCount: 0,
    }
  },
  created() {
    this.terminal = IsPC() ? 'pc' : 'phone'
    if (this.terminal == 'pc') {
      this.getDatas(8);
    } else {
      this.getDatas(4);
    }
  },
  methods: {
    getDatas(length) {
      this.$http.get("/v1/frontend/index/get-activity", {
          params: {
            length: length,
            status: 2, //2是正在进行
            type: 0, //查询活动类型 所有传0 大赛传1 问答传2
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.activities = res.data.data.activities;
            this.activityCount = res.data.data.activityCount;
          }
        })
        .catch((err) => {
          console.log(err)
        })
    },
    goPath(id, type, status) {
      if (type == 1) { //活动详情
        this.$router.push({ path: "/frontPage/detail", query: { activityId: id, type: type, status: status } })
      } else if (type == 2) { //跳转到问答页面
        this.$router.push({ path: "/frontPage/question", query: { questionId: id, type: type, status: status } })
      }
    },
    showMore() {
      this.$router.push({ path: "/frontPage/allActivity" })
    },
  }
}

</script>
<style lang="less">
/*手机端*/

.phone-activing {
  width: 94%;
  margin: 0 auto;
  padding-bottom: 15px;

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
    overflow: hidden;
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

.pc-activing {
    width: 1200px;
    margin: 0 auto;
    overflow: hidden;

    .head {
        width: 1200px;
        margin: 0 auto;
    }

    h3 {
        font-size: 34px;
    }

    .rows {
        width: 1200px;
        overflow: hidden;
    }
    .pc-box {
        float: left;
        width: 266px;
        height: 250px;
        border: 1px solid #dcdcdc;
        margin: 0 45px 30px 0;
        cursor: pointer;
        overflow: hidden;
    }
    .pc-box:hover {
        color: #f53051;
        img{  
            opacity:.9;
            -webkit-transform:scale(1.3);
            -moz-transform:scale(1.3);
            -o-transform:scale(1.3);
            -ms-transform:scale(1.3);
            transform:scale(1.3);
        } 
    }
    .rows .pc-box:nth-child(4n) {
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
            -webkit-transition:all .4s ease-in-out;
            -moz-transition:all .4s ease-in-out;
            -o-transition:all .4s ease-in-out;
            -ms-transition:all .4s ease-in-out;
            transition:all .4s ease-in-out;
        }
    }

    

    .title {
        height: 60px;
        line-height: 60px;
        font-size: 16px;
        padding-left: 15px;
    }


    .ivu-btn-ghost {
        font-size: 14px;
    }
}

</style>
