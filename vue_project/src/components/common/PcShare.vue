<template>
    <div class="pc-share1" :class="terminal!='pc'?'phone-share':''">
        <p class="title-s"> 分享到 </p>
        <p class="hr"></p>
        <div style="overflow:hidden;">
            <a href="#" class="a-link " @click="open_share('qq')">
                <img src="../../../static/img/gbRes_3.png" height="120" width="120">
                <p>QQ好友</p>
            </a>
            <a href="#" class="a-link " @click="open_share('qzone')">
                <img src="../../../static/img/gbRes_4.png" height="120" width="120">
                <p>QQ空间</p>
            </a>
            <a href="#" class="a-link " @click="open_share('weixin')">
                <img src="../../../static/img/gbRes_2.png" height="120" width="120">
                <p>微信</p>
            </a>
           <a href="#" class="a-link " @click="open_share('weibo')">
            <img src="../../../static/img/gbRes_6.png" height="120" width="120">
               <p>新浪微博</p>
            </a>
        </div>

        <Modal v-model="pcWeixin" width="240" height="300" id="pWeixin" :styles="{top: '350px'}" :mask-closable="false">
            <h4 style="text-align: center;">用微信扫描二维码分享到朋友圈</h4>
            <img :src="qrCodeurl" alt="微信二维码" />
            <p style="text-align: center;">如果没有二维码，请刷新页面重试</p>
        </Modal>

         <Modal v-model="phoneWeixin"  id="phWeixin" :styles="{top: '70px'}" :mask-closable="false">
            <h4 style="text-align: center;">用微信扫描二维码分享到朋友圈</h4>
            <img :src="qrCodeurl" alt="微信二维码" />
            <p style="text-align: center;">如果没有二维码，请刷新页面重试</p>
        </Modal>
    </div>
</template>

<script>
    export default{
        data(){
            return {
                baseUrl:baseUrl,
                terminal:'pc',
                pcWeixin:false,
                phoneWeixin:false,
                qrCodeurl:'',
                shareUrl:'http://zbpf.jqweike.com',
            }
        },
        mounted(){
            this.terminal = IsPC()? 'pc':'phone'
        },
        methods:{
            open_share(type) {
                var _this = this;
                 if(sessionStorage.isFromDetail==1){  //证明是从详情页面分享的
                  _this.shareUrl = location.href.split('&access_token')[0]+'&workId='+sessionStorage.workId;
                }else if(sessionStorage.isFromDetail==2){  //证明是从个人中心分享的
                  _this.shareUrl =location.href.split('.com')[0]+'.com/frontPage/detail?type=1&activityId='+sessionStorage.shareActivityId+'&status='+sessionStorage.shareActivityStatus+'&workId='+sessionStorage.workId;
                }
               
                var shareTitle = '有惊喜分享给你哟';
                var shareImg = '';
                var shareDesc = '快来看，有惊喜分享给你哟';
                var openUrl = '';
                _this.addShare();
                switch (type) {
                    case 'qzone':
                        openUrl = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + encodeURIComponent(_this.shareUrl) + '&title=' + shareTitle + '&pics=' + shareImg
                         break;
                    case 'weixin':
                        _this.getImg();
                        if(_this.terminal=='pc'){
                             _this.pcWeixin=true;
                         }else{
                            _this.phoneWeixin=true;
                         }
                        return false;
                        break;
                     case 'qq':
                         openUrl = 'http://connect.qq.com/widget/shareqq/index.html?url=' + encodeURIComponent(_this.shareUrl) + '&desc=' + shareDesc + '&summary=' + shareDesc + '&site=' + _this.shareUrl + '&pics=' + shareImg;
                         break;
                    case 'weibo':
                        openUrl = 'http://v.t.sina.com.cn/share/share.php?url=' +encodeURIComponent( _this.shareUrl) + '&content=utf-8&title=' +  shareTitle + '&&source=' + _this.shareUrl + '&sourceUrl=' + _this.shareUrl + '&content=' + shareDesc + '&pic=' + shareImg;
                        break;
                 }
                 var u = navigator.userAgent;
                var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
                var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
                var isSafari = u.indexOf("Safari") != -1 && u.indexOf("Version") != -1;
                if(isiOS&&!isSafari){
                    this.$Modal.warning({
                        title: '',
                        content: '<h3>苹果手机建议使用Safari浏览器分享，或者到PC电脑端分享</h3>',
                    });
                }
                window.open(openUrl);  
            },
            //记录分享的次数
            addShare(){
                if (sessionStorage.workId) {
                    this.$http.post("/v1/frontend/works/add-share",{
                        workId: sessionStorage.workId,
                        userId: sessionStorage.userId,
                    })
                    .then((res)=>{
                       if(res.data.status==200){
                        }
                    })
                    .catch((err)=>{
                    })
                }else {
                  message.showMessage('warning', '请重新点击分享作品');
                }

            },
            //获取微信二维码
            getImg(){
                this.$http.get("/v1/admin/image/qr-code",{
                    params:{
                        detail:location.href.split('.com')[0]+'.com/frontPage/production?productionId='+sessionStorage.workId,
                        matrixPointSize:5, //图片大小
                    }
                })
                .then((res)=>{
                   if(res.data.status==200){
                      this.qrCodeurl ="data:image/png;base64,"+ res.data.data;
                    }
                })
                .catch((err)=>{
                })
            },
     
        }
    }
</script>


<style lang='less'>
.pc-share1{
    width: 468px;
    overflow: hidden;
    margin-bottom: 30px;

    .hr{
        position:absolute;
        height: 1px;
        width: 84%;
        background-color: #FFEC94;
        top:40px;
        left: 40px;
    }


    .title-s{
        width: 80px;
        line-height: 50px;
        text-align:center;
        margin:0 auto;
        background-color: #fff;
        position: relative;
        z-index:2;
        
    }

    .a-link{
        float: left;
        width: 25%;


        img{
            width: 60%;
            height: 60%;
            margin:20% 20% 5% 20%;
        }

        p{
            text-align: center;
            font-size: 14px;
        };

    }
}
  /*------  手机端 -------- */
.phone-share{
    width: 100%;
    overflow: hidden;
    margin-bottom: 0.1rem;

    .hr{
        top:0.2rem;
        left: 0.3rem;
    }


    .title-s{
        line-height: 0.4rem;
    }

    .a-link{

        img{
            width: 50%;
            height: 50%;
            margin:3% 25% 0 25%;
        }

        p{
            text-align: center;
            font-size: 0.14rem;
        };

    }

}

#pWeixin {
    .ivu-modal-footer{
        display: none;
    }
    img{
        margin: 20px 11.5px;
    }

    .ivu-modal-wrap{
        z-index: 1009
    }
}

#phWeixin{
   .ivu-modal{
    width: 64%!important;
    height: 300px!important;
    margin-left: 16%;
   }
    .ivu-modal-footer{
        display: none;
    }
    img{
        width: 95%;
        margin: 0.10rem 5px;
    }

    .ivu-modal-wrap{
        z-index: 1009
    }

}
</style>