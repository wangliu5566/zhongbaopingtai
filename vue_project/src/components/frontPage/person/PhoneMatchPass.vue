<template>
    <div class="match-pass" v-if="terminal!='pc'">
        <div class="big-title">
            <p class="hr"></p>
            <span>作品列表</span>
            <span v-if="totalCount>6" class="look-more" @click="showMore()">换一换
                <i class="phone-icon"></i>
            </span>
        </div>
        <div class="middle"  :style="{minHeight:personHeight}">
            <div class="production" v-if="matchList.length!=0"
        v-for="(item,index) in matchList" :key="index" >
                <div class="img-left" @click="goProductionDetail(item.id)">
                    <img :src="item.coverPath?baseUrl+item.coverPath:defaultImg">
                </div>
                <div class="img-right">
                    <p class="title">{{item.workName}}</p>
                    <p class="author">参赛者：{{item.username}}</p>
                    <p class="icon-pro">
                        <span class="zan-pro" v-if="!item.isHits" @click="giveHit(item.id)">{{item.hits}}</span>
                        <span class="zan1-pro" v-if="item.isHits" @click="giveHit(item.id)">{{item.hits}}</span>
                        <span class="bds_more" @click='getWorkId(item.id,item.activityId,item.activityStatus)'>{{item.share}}</span>
                    </p>
                </div>
            </div>
            <div v-if="matchList.length==0" class="no-content">
                <p>您还未参过过任何活动哟！</p>
            </div>
        </div>
        
        <Modal v-model="shareMyMatch" style="width: 100%;" :mask-closable="false" id="bottom">
            <pcShare></pcShare>
            <button class="close-share" @click="shareMyMatch=false">关&nbsp;闭</button>
        </Modal>

    </div>
</template>

<script>
import pcShare from "../../common/PcShare.vue"
    export default {
        data(){
            return {
                baseUrl:baseUrl,
                terminal:'pc',
                isSign:true,
                matchList:[],
                totalCount:1,
                page:sessionStorage.phoneMatchPage?sessionStorage.phoneMatchPage:1,
                pageSize:6,
                defaultImg:"/static/img/small（图片）.png",
                shareMyMatch:false,
            }
        },
        components:{
            pcShare
        },
        mounted(){
            this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
            this.page = sessionStorage.phoneMatchPage?sessionStorage.phoneMatchPage:1,
            this.terminal = IsPC() ? 'pc' : 'phone'
            if(this.isSign&&this.terminal!='pc'){
                this.getDatas(this.page,this.pageSize)
            }else{
                this.$router.push("/frontPage")
            }

            this.fontSize = (window.innerWidth/ 750) * 200;
            this.personHeight = document.body.clientHeight / this.fontSize - 2.35 + 'rem';
        },
        methods:{
            getDatas(page,pageSize){
                this.$http.get("/v1/frontend/works/lists",{
                    params:{
                        page:page,
                        length:pageSize,
                        userId:sessionStorage.userId,
                        isMine:1,
                    }
                })
                .then((res)=>{
                   if(res.data.status==200){
                        this.matchList=res.data.data.lists;
                        this.totalCount=res.data.data.count;
                    }
                })
                .catch((err)=>{
                    console.log(err)
                })
            },
            goProductionDetail(id){
                this.$router.push({path:"/frontPage/production",query:{productionId:id}})
            },
            //手机端换一换
            showMore(){
                var totalPage =this.totalCount%this.pageSize==0?this.totalCount/this.pageSize:this.totalCount/this.pageSize+1
                this.page++;
                if(this.page>totalPage){
                    this.page=1;
                }
                sessionStorage.phoneMatchPage = this.page 
                this.getDatas(this.page,this.pageSize)
            },
            //点赞
            giveHit(id){
                this.$http.post("/v1/frontend/works/add-hits",{
                        workId:id,
                        userId:sessionStorage.userId,
                })
                .then((res)=>{
                    if(res.data.status==200){
                        this.$Message.success({
                            content:res.data.message,
                            duration:3,
                        })
                        this.getDatas(this.page,this.pageSize)
                    }else{
                        this.$Message.error({
                            content:res.data.message,
                            duration:3,
                        })
                    }
                })
                .catch((err)=>{
                    console.log(err);
                })
            },
            //分享前获取id
            getWorkId(id,activityId,activityStatus){
                sessionStorage.workId = id;
                sessionStorage.isFromDetail=2;  //分享个人中心的作品,自定义分享地址，不能用当前的路径
                sessionStorage.shareActivityId=activityId; 
                sessionStorage.shareActivityStatus=activityStatus; 
                this.shareMyMatch=true;
            },
        }
    }
</script>


<style lang='less'>
    .match-pass{
        width: 100%;
        background-color: #efefef;
        overflow:hidden;

        .call-back{
            position: absolute;
            left: 3%;
            top:0.2rem;
            width: 30%;
            font-size: 0.15rem;
            color:white;
            padding-left: 4%;
            background:url('/static/img/返回(5).png') no-repeat 0 0.05rem;
            -webkit-background-size: 8%;
            background-size: 8%;
        }

        .big-title{
            width: 94%;
            margin-left: 3%;
            font-size: 0.16rem;
            font-weight: 700;
            line-height: 0.36rem;
            color:#080808;
            margin-bottom: 0.1rem;
            padding-top: 0.2rem;
        }
        .hr{
            width: 5%;
            height: 0.04rem;
            background-color: #000;
        }

        .middle{
            width: 100%;
            overflow: hidden;
        }

        .look-more{
            float:right;
            color:#999;
            font-size:0.14rem;
            font-weight: 400;
            position: relative;
            padding-right: 9%;
        }
        .phone-icon{
            display: inline-block;
            width: 30%;
            height: 0.18rem;
            background:url('/static/img/circle.png') no-repeat ;
            -webkit-background-size:0.15rem;
            background-size:0.15rem;
            position: absolute;
            top:0.09rem;
            right:0;
        }

        .production{
            width: 94%;
            margin:0 auto;
            margin-bottom:0.15rem;
            background-color: #fff;
            overflow:hidden;
            height: 1.1rem;
            font-size: 14px;
            
        }

        .img-left{
            width: 47%;
            height: 1.1rem;
            margin-right: 3%;
            float: left;
            img{
                width: 100%;
                height: 100%;
            }
        }
        .img-right{
            width: 50%;
            height:1.1rem;
            float: left;
            position: relative;
        }
        .icon-pro{
            width: 100%;
            position: absolute;
            bottom: 0;
            height:0.35rem;
        }

        .title{
            margin-top:0.15rem;
            font-size:0.15rem;
            color:#333;
            font-weight: 700;
        }

        .author{
            font-size: 0.13rem;
            color:#999;
            line-height: 0.2rem;
            overflow:hidden;
            white-space: normal;
            text-overflow: ellipsis;
        }

        .zan-pro,.zan1-pro,.bds_more{
            display: inline-block;
            width: 50%;
            height: 30px;
            background: url('/static/img/赞.png') no-repeat 2px 5px;
            padding-left: 16%;
            line-height: 0.3rem;
            float: left;
            font-size: 0.14rem;
        }
        .zan1-pro{
            background: url('/static/img/赞(4).png') no-repeat 2px 5px;
        }
        .bds_more{
            background: url('/static/img/转发.png') no-repeat 2px 5px;
            margin:0;
            width:30%;
            margin-left: 20%;
        }

        .no-content{
            text-align:center;
            font-size:0.2rem;
            line-height:1rem;
            min-height: 1.5rem;
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