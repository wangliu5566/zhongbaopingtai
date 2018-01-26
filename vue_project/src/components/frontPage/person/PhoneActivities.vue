<template>
	<div class="phone-myactivity"  v-if="terminal!='pc'">
		<div id='phone-head' style="position:relative;" v-if="terminal!='pc'">
			<div class="call-back" @click="goIndex('/phonePerson')">个人中心</div>
            <img class="login-img" src="/static/img/logo1.png" @click="goIndex('/frontPage')">
        </div>
		<div class="select-activity">
			<p style="border-bottom: 1px solid #c0c0c0">活动状态：
				<span class="select-more" @click="modalStatus=true">
					{{formItem6.status==0?'不限':formItem6.status==2?"正在进行的活动":formItem6.status==1?"即将开始的活动":"已结束的活动"}}
					<i class="phone-more"></i>
				</span>
			</p>
			<p>活动类型：
				<span class="select-more" @click="modalType=true">
					{{formItem6.isMine==0?'所有活动':'我参加的活动'}}
					<i class="phone-more"></i>
				</span>
			</p>
		</div>
		<div class="kong"></div>
		<div class="phone-content" :style="{minHeight:personHeight}">
			<p class="hr"></p>
			<div class="title">
				<span>{{formItem6.isMine==0?'所有活动':'我的活动'}}</span>
				<span v-if="totalCount>8" class="look-more" @click="showMore()">换一换
					<i class="phone-icon"></i>
				</span>
			</div>
			<div class="show-img" v-if='activities.length!=0'>
				<div class="div-box" v-for="(item,index) in activities" :key="index"  @click="goPath(item.id,item.type,item.status)">
					<img :src="baseUrl+item.smallImage" alt="">
					<p class="esp myFloat">{{item.name}}</p>
				</div>
			</div>
			<div v-if="activities.length==0" style="text-align: center;line-height: 0.5rem;font-size: 0.2rem;">
				<p>这里没有活动哟，</p>
				<p>去<span @click="goIndex('/frontPage')" style="cursor: pointer;color:#2d8cf0;margin:0 auto;">首页</span>看看吧</p>
			</div>
		</div>

		<footer class="phone-foot" >
            <div class="foot-div" style="overflow: hidden">电话：010-82783029-803
                <span style="float:right">手机：13811797317</span>
            </div>
            <div class="foot-div">邮箱：qbz@kingchannels.com
                <span style="float:right">京ICP备10010903号-4</span>
            </div>
            <div class="foot-div">地址：北京市海淀区西三旗建材城西路31号B座四层西区</div>
        </footer>

		<Modal v-model="modalStatus" style="width: 100%;" :mask-closable="false" id="show-list">
	        <p slot="header" class='btns'>
	         	<span @click="modalStatus=false">取消</span>
	         	<span style="float: right;" @click="selectDatas(1)">确定</span>
	         </p>
	         <ul class="select-ul">
	        	<li @click="getSelectStatus(0)"> 不限 <span :class="formItem6.status==0?'show-ok':''"></span></li>
	        	<li @click="getSelectStatus(1)"> 即将开始的活动 <span :class="formItem6.status==1?'show-ok':''"></span></li>
	        	<li @click="getSelectStatus(2)"> 正在进行的活动 <span :class="formItem6.status==2?'show-ok':''"></span></li>
	        	<li @click="getSelectStatus(3)"> 已结束的活动 <span :class="formItem6.status==3?'show-ok':''"></span></li>
	        </ul>
	    </Modal>

	    <Modal v-model="modalType" style="width: 100%;" :mask-closable="false" id="show-list1">
	        <p slot="header" class='btns'>
	         	<span @click="modalType=false">取消</span>
	         	<span style="float: right;" @click="selectDatas(2)">确定</span>
	         </p>
	        <ul class="select-ul">
	        	<li  @click="getSelectType(0)">所有活动 <span :class="formItem6.isMine==0?'show-ok':''"></span></li>
	        	<li  @click="getSelectType(1)">我参加的活动 <span :class="formItem6.isMine==1?'show-ok':''"></span></li>
	        </ul>
	    </Modal>
	</div>
</template>


<script>
	export default{
		data(){
			return {
				baseUrl:baseUrl,
				terminal:'pc',
				totalCount:0,
				page:1,
				pageSize:8,
				formItem6:{
					status:"0", // 查询活动状态 不限传0 正在进行值2 已结束3
					isMine:'1', //1是我的活动，0是所有活动
				},
				activities:[],
				modalStatus:false,
				modalType:false,
			}
		},
		mounted(){
			this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
			this.terminal = IsPC() ? 'pc' : 'phone'
			if(this.isSign&&this.terminal!='pc'){
				this.getDatas(this.page,this.pageSize,this.formItem6.status,this.formItem6.isMine)
			}else{
				this.$router.push("/frontPage")
			}
			
			this.fontSize = (window.innerWidth/ 750) * 200;
			this.personHeight = document.body.clientHeight / this.fontSize - (1.65+1.16) + 'rem';
		},
		methods:{
			getDatas(page,pageSize,status,isMine){
				this.$http.get("/v1/frontend/index/get-activity",{
					params:{
						page:page,
						length:pageSize,
						status:status,//查询活动状态 所有传0 正在进行值2 已结束传3
						type:0,//查询活动类型 所有传0 大赛传1 问答传2
						userId:sessionStorage.userId,
						isMine:isMine,  //0是所有，1是我的
					}
				})
	            .then((res)=>{
	               if(res.data.status==200){
	                    this.activities=res.data.data.activities;
                        this.totalCount=res.data.data.activityCount;
	                }
	            })
	            .catch((err)=>{
	                console.log(err)
	            })
			},
			//手机端换一换
			showMore(){
				var totalPage =this.totalCount%this.pageSize==0?this.totalCount/this.pageSize:this.totalCount/this.pageSize+1
				this.page++;
				if(this.page>totalPage){
					this.page=1;
				}
				sessionStorage.page = this.page;
				this.getDatas(this.page,this.pageSize,this.formItem6.status,this.formItem6.isMine)

			},
			goPath(id,type,status){
				if(type==1){  //活动详情
					this.$router.push({path:"/frontPage/detail",query:{activityId:id,type:type,status:status}})
				}else if(type==2){   //跳转到新闻报道页面
					this.$router.push({path:"/frontPage/question",query:{questionId:id,type:type,status:status}})
				}
			},
			getSelectStatus(index){
				this.formItem6.status=index;
			},
			getSelectType(index){
				this.formItem6.isMine=index;
			},
			//模态框请求数据
			selectDatas(index){
				this.page=1;
				this.getDatas(this.page,this.pageSize,this.formItem6.status,this.formItem6.isMine)
				if(index==1){
					this.modalStatus=false;
				}else if(index ==2){
					this.modalType=false;
				}
				
			},
			 goIndex(page) {
	           this.$router.push(page)
	        },
		},
		watch:{
			formItem6:{
　　　　　　　　　//注意：当观察的数据为对象或数组时，curVal和oldVal是相等的，因为这两个形参指向的是同一个数据对象
　　　　　　　　　　handler(curVal,oldVal){
　　　　　　　　　　　　this.formItem6 = curVal;
　　　　　　　　　　},
　　　　　　　　　　deep:true
　　　　　　　　}
		}
	}
</script>

<style lang="less">
      .phone-myactivity{
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

      	.phone-slider{
      		height: 1.75rem;
      		overflow: hidden;
      	}
		
		.select-activity{
			width: 100%;
			background-color: #fff;
			height: 1.06rem;
			padding: 0 2%
		}

		.select-activity p{
			height: 0.53rem;
			line-height: 0.53rem;
			font-size: 0.15rem;
		}

		.kong{
			width: 100%;
			height: 0.1rem;
			background-color: #eeeeee;
		}

		.phone-content{
      		width: 94%;
      		margin:0 auto;
      		padding-bottom: 15px;
      	}

      	.hr{
      		width: 5%;
      		height: 0.04rem;
      		margin-top: 0.2rem;
      		background-color: #000;
      	}

      	.title{
      		font-size: 0.16rem;
      		font-weight: 700;
      		line-height: 0.36rem;
      		color:#080808;
      		margin-bottom: 0.1rem;
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
			background:url('../../../../static/img/circle.png') no-repeat ;
			-webkit-background-size:0.15rem;
			background-size:0.15rem;
			position: absolute;
			top:0.09rem;
			right:0;
		}
		.select-more{
      		float:right;
      		font-size: 0.14rem;
      		font-weight: 400;
      		position: relative;
      		padding-right:25px;
      		overflow: hidden;
      	}
		.phone-more{
			display: inline-block;
			width: 20px;
			height: 0.18rem;
			background:url('../../../../static/img/right2.png') no-repeat ;
			-webkit-background-size: 0.1rem;
			background-size: 0.09rem;
			position: absolute;
			top:0.18rem;
			right:0;
		}
      	.show-img{
      		width: 100%;
      		overflow: hidden;
      	}
  		.div-box{
  			float: left;
      		width: 48%;
      		height: 1.25rem;
      		margin-bottom: 4%;
      		position: relative;
      		border:1px solid #c0c0c0;
      	}
      	.div-box img{
      		width:100%;
      		height: 100%;
      	}
      	.div-box:nth-child(2n){
      		margin-left: 4%;
      	}
  		.myFloat{
  			background-color: #000;
  			opacity: 0.6;
  			color:white;
      		width: 100%;
      		overflow: hidden;
      		position: absolute;
      		bottom: 0;
      		left:0;
      		padding: 0 3%;
      	}

	}

	
	#show-list1{
      		
		.ivu-modal,.ivu-modal-content{
			margin:0;
			height:1.3rem;
			top:0;
		}
		.ivu-modal-content{
			border-radius: 0;
		}
		.ivu-modal-wrap{
		    position: fixed;
		    overflow: auto;
		    top:81%;
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
		.ivu-modal-header .btns{
			height: 0.38rem;
			font-size: 0.14rem;
			color:#1e76e2;
			font-weight: 400;
			line-height: 0.38rem;
		}
		.select-ul{
			width: 100%;
			overflow: hidden;
		}
		.select-ul li{
			height: 0.44rem;
			border-bottom: 1px solid #dbdbdb;
			line-height: 0.44rem;
			font-size: 0.15rem;
			color:#000;
		}
		.show-ok{
			float: right;
			display: inline-block;
			width: 10%;
			height: 100%;
			background:url('/static/img/ok.png') no-repeat 0 0.1rem;
			background-size: 0.2rem;
		}
	}

	#show-list{
      		
		.ivu-modal,.ivu-modal-content{
			margin:0;
			height:2.15rem;
			top:0;
		}
		.ivu-modal-content{
			border-radius: 0;
		}
		.ivu-modal-wrap{
		    position: fixed;
		    overflow: auto;
		    top:68%;
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
		.ivu-modal-header .btns{
			height: 0.38rem;
			font-size: 0.14rem;
			color:#1e76e2;
			font-weight: 400;
			line-height: 0.38rem;
		}
		.select-ul{
			width: 100%;
			overflow: hidden;
		}
		.select-ul li{
			height: 0.44rem;
			border-bottom: 1px solid #dbdbdb;
			line-height: 0.44rem;
			font-size: 0.15rem;
			color:#000;
		}
		.show-ok{
			float: right;
			display: inline-block;
			width: 10%;
			height: 100%;
			background:url('/static/img/ok.png') no-repeat 0 0.1rem;
			background-size: 0.2rem;
		}
	}
	
</style>