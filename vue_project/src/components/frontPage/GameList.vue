<template>
	<div class="game-list" ref="productions" v-if="terminal!='pc'">
		<img class="img" :src="baseUrl+showImg">
		<div class="rank" v-if="endTime" :style="{height: heightNum+'px'}">
			<button>最新排名</button>
			<ul class="ranking-li" :style="{height: heightNum-40+'px'}">
	 			<li v-for="(item,index) in rankList" :key="item.username">
	 				<Row>
				        <Col span="4">
				        	<i class="ranking-num">{{index+1}}</i>
				        </Col>
				        <Col span="20">
				        	<p class="esp username">{{item.username}}</p>
				        	<p class="esp user-detail">{{item.workName}}</p>
				        </Col>
				    </Row>
	 			</li>
	 		</ul>
	 		<div class="rank-more" @click="moreRank">
	 			<img v-if="showMoreRank" src="../../../static/img/top.png">
	 			<img v-else src="../../../static/img/bottom.png">
	 		</div>
		</div>
		<div class="production" v-if="gameList.length!=0"
		v-for="(item,index) in gameList" :key="index">
			<div class="img-left" @click="goProductionDetail(item.id)">
				<img :src="item.coverPath?baseUrl+item.coverPath:defaultImg">
        	</div>
        	<div class="img-right">
        		<p class="title">{{item.workName}}</p>
				<p class="author">参赛者：{{item.username}}</p>
				<p class="icon-pro">
					<span class="zan-pro" v-if="!item.isHits" @click="giveHit(item.id)">{{item.hits}}</span>
					<span class="zan1-pro" v-if="item.isHits" @click="giveHit(item.id)">{{item.hits}}</span>
					<span class="bds_more" @click='getWorkId(item.id)'>{{item.share}}</span>
				</p>
        	</div>
		</div>
		<div v-if="gameList.length==0" class="no-content">
	 		<p>新活动，争做第一人吧！</p>
	 	</div>
	 	<Modal v-model="modalShare" style="width: 100%;" :mask-closable="false" id="bottom">
 			<pcshare></pcshare>
			<button class="close-share" @click="modalShare=false">关&nbsp;闭</button>
    	</Modal>
	</div>
</template>

<script>
import pcshare from "../common/PcShare.vue"
	export default{
		data(){
			return {
				baseUrl:baseUrl,
				terminal:'pc',
				gameList:[],
				showImg:"",
				isShowMore:false,
				endTime:true,
				rankList:[],
				heightNum:256,
				showMoreRank:false,
				defaultImg:"../../../static/img/110(图片.png",
				modalShare:false,
			}
		},
		components:{
			pcshare
		},
		mounted(){
			this.terminal = IsPC() ? 'pc' : 'phone'
			if(this.terminal=="pc"){
				this.$router.push({path:"/frontPage/detail",query:{activityId:this.$route.query.activityId,type:this.$route.query.type,status:this.$route.query.status}})
			}

			if(this.$route.query.activityId){
				this.activityId=this.$route.query.activityId;
				this.status=this.$route.query.status
				this.getImg()
				this.getDatas()
			}else{
				this.$router.push("/frontPage/index")
			}
			this.endTime=this.$route.query.status==3?true:false;
			if(this.endTime){
				this.getRankData();
			}
			
		},
		methods:{
			//获取活动大图
			getImg(){
				this.$http.get("/v1/frontend/activity/get-mobile-image",{
					params:{
						activityId:this.activityId,
					}
				})
	            .then((res)=>{
	               if(res.data.status==200){
	                    this.showImg=res.data.data;
	                }
	            })
	            .catch((err)=>{
	                console.log(err)
	            })
			},
			//获取还活动下面所有作品
			getDatas(){
				this.$http.get("/v1/frontend/works/lists",{
					params:{
						activityId:this.activityId,
						page:1,
						length:-1,
						userId:sessionStorage.userId,
					}
				})
	            .then((res)=>{
	               if(res.data.status==200){
	                    this.gameList=res.data.data.lists;
	                }
	            })
	            .catch((err)=>{
	                console.log(err)
	            })
			},
			goProductionDetail(id){
				this.$router.push({path:"/frontPage/production",query:{productionId:id}})
			},
			//获取排名数据
			getRankData(){
				this.$http.get("/v1/frontend/works/get-rank",{
					params:{
						activityId:this.activityId,
						length:7,
					}
				})
	            .then((res)=>{
	               if(res.data.status==200){
	                    this.rankList=res.data.data;
	                }
	            })
	            .catch((err)=>{
	                console.log(err)
	            })
			},
			//展示更多排名
			moreRank(){
				this.showMoreRank=!this.showMoreRank
				if(this.showMoreRank){
					this.heightNum=515;
				}else{
					this.heightNum=256;
				}
				
			},
			//点赞
			giveHit(id){
				if(sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"){
					this.$Modal.confirm({
	                    title: '',
	                    content: '<h3>您还没有登录，立即去登录</h3>',
	                    onOk: () => {
	                        login(this.$route.fullPath)
	                    }
	                });
				}else{
					this.$http.post("/v1/frontend/works/add-hits",{
							workId:id,
							userId:sessionStorage.userId,
					})
		            .then((res)=>{
		            	this.$Message.success({
	                   	 	content: res.data.message,
	                    	closable: true
	               		 });
		                if(res.data.status==200){
			               this.getDatas()
		                }
		            })
		            .catch((err)=>{
		                console.log(err);
		            })
				}
			},
			//点击分享
			getWorkId(id){
				if(sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"){
					this.$Modal.confirm({
	                    title: '',
	                    content: '<h3>您还没有登录，立即去登录</h3>',
	                    onOk: () => {
	                      login(this.$route.fullPath)
	                    }
	                });
				}else{
					sessionStorage.workId = id;
					sessionStorage.isFromDetail = 1;  //分享详情页的作品,就用当前页面分享
					this.modalShare=true;
				}
			}
		},
		watch:{
			modalShare:{
　　　　　　　　handler(curVal,oldVal){
					if(!curVal){
						this.getDatas()
					}
	　　　　　　}
	　　　　}	
		}
	}
</script>

<style lang="less">
    .game-list{
    	width: 100%;
    	padding-bottom: 5px;
    	background-color: #f2f2f2;

      	.img{
      		width: 100%;
      		height:1.75rem;
      	}

      	.rank{
      		overflow: hidden;
      		margin-bottom: 0.3rem;
      		
      		button{
				width: 50%;
				line-height: 0.4rem;
				font-size:0.15rem;
				border:none;
				background-color: #333;
				color:white;
				font-weight: 700;
				margin-left: 25%;
			}

			.ranking-li{
				width: 94%;
				margin:0 auto;
				border:1px solid #666;
				overflow: hidden;
				padding-top: 0.2rem;
				margin-top: -0.2rem;

			}
			.ranking-li li{
				width: 90%;
				margin:0 auto;
				height: 65px;
				border-bottom: 1px solid #999;
			}

			.username{
				color:#333;
				font-weight: 700;
				font-size: 0.14rem;
				line-height: 0.2rem;
				margin-top: 0.1rem;
			}

			.user-detail{
				color:#999
			}

			.ranking-num{
				display: inline-block;
				width: 35px;
				height: 35px;
				font-size: 24px;
				background-color: #fff;
				border-radius: 20px;
				text-align: center;
				line-height: 35px;
				font-weight: 700;
				margin-top: 10px;

			}
			li:first-child i{
				background-color: #df3010;
				color:white;
			}
			li:nth-child(2) i{
				background-color: #f7472f;
				color:white;
			}
			li:nth-child(3) i{
				background-color: #f8851f;
				color:white;
			}
			.rank-more{
				width: 94%;
				margin:0 auto;
				height: 20px;
				background-color: #333;

				img{
					width: 5%;
					margin-left: 47.5%;
				}
			}
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
      		margin-right: 3%;
      		float: left;
      		img{
      			width: 100%;
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
      		font-size: 13px;
      		color:#999;
      		line-height: 40px;
      	}

      	.zan-pro,.zan1-pro,.bds_more{
			display: inline-block;
			width: 50%;
			height: 30px;
			background: url('../../../static/img/赞.png') no-repeat 2px 5px;
			padding-left: 30px;
			line-height: 30px;
			float: left;
			font-size: 0.14rem;
		}
		.zan1-pro{
			background: url('../../../static/img/赞(4).png') no-repeat 2px 5px;
		}
		.bds_more{
			background: url('../../../static/img/转发.png') no-repeat 2px 5px;
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