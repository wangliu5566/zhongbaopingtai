<template>
  <div>
    <div class="pc-abstract" v-if="terminal=='pc'">
      <h3><i></i> <span>{{getObj.name}}</span></h3>
      <div class="wrap-ab">
        <textarea style="resize:none" readonly="readonly" class="abstract-word">{{getObj.intro}}</textarea>
        <div class="float-div">
          <slider1 :imgList="images1"></slider1>
          <div class="QR-code">
            <img :src="qrCodeurl">
          </div>
        </div>
      </div>
    </div>
    <div class="phone-abstract" v-if="terminal!='pc'">
      <h3><i></i> <span>{{getObj.name}}</span></h3>
      <textarea style="resize: none;" readonly="readonly" class="abstract1">{{getObj.intro}}</textarea>
      <div class="float-div">
        <swiperMatch :imgs="images1"></swiperMatch>
      </div>
    </div>
  </div>
</template>
<script>
import slider1 from "../common/Slider1.vue"
import swiperMatch from "../common/SwiperMatch.vue"
export default {
  data() {
    return {
      terminal: 'pc',
      baseUrl: baseUrl,
      qrCodeurl: '',
    }
  },
  props: ['images1', 'getObj'],
  components: {
    slider1,
    swiperMatch
  },
  mounted() {
    this.terminal = IsPC() ? 'pc' : 'phone'
    this.getImg()
  },
  methods: {
    //获取二维码
    getImg() {
      this.$http.get("/v1/admin/image/qr-code", {
          params: {
            detail: window.location.href.indexOf('&access_token') != -1 ? window.location.href.split('&access_token')[0] : window.location.href
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.qrCodeurl = "data:image/png;base64," + res.data.data;
          }
        })
    },
  }
}

</script>
<style lang='less'>
.phone-abstract {
  width: 100%;
  padding: 10px 2%;
  overflow: hidden;

  .abstract1 {
    width: 100%;
    height: 2.7rem;
    border: none;
    overflow-y: visible;
    font-size: 14px;
    line-height: 25px;
    outline: none;
    margin-bottom: 0.2rem;
  }

  .float-div img {
    width: 100%;
  }
}

.pc-abstract {
  width: 100%;
  position: relative;
  overflow: hidden;

  i {
    display: inline-block;
    width: 5px;
    height: 27px;
    background-color: #333;
    margin-top: 1px;
    position: absolute;
    top: 10px;
  }

  h3 {
    width: 100%;
    height: 45px;
    font-size: 30px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    margin-top: -8px;
    margin-bottom: 18px;
    position: relative;

    span {
      margin-left: 20px;
      color: #333;
    }
  }

  .wrap-ab {
    height: 370px;
    width: 100%;
    overflow: hidden;
  }

  .abstract-word {
    width: 620px;
    height: 370px;
    height: 100%;
    border: 2px solid #313131;
    overflow-y: visible;
    padding: 15px;
    font-size: 14px;
    line-height: 25px;
    outline: none;
    float: left;
    margin-right: 20px;
  }

  .float-div {
    width: 560px;
    height: 100%;
    overflow: hidden;
    border: 1px solid #999;
    float: left;
    position: relative;

    img {
      width: 100%;
      height: 100%;
    }
  }

  .QR-code {
    position: absolute;
    top: 270px;
    left: 0;
    width: 100px;
    height: 100px;
    z-index: 10;
  }
}

</style>
