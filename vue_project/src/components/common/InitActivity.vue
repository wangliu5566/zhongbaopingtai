<template>
	<div class="pc-init-activity js" v-if="terminal=='pc'" ref="animateJs">
	 	<div  class="pc-wrap">
			 <div class="head">
				<h3><i class="pre"></i>即将开始的活动<i class="next"></i></h3>
				<p>Init activities</p>
		 	</div>
		 	<!-- 未开始活动在safari和IE10以下，显示无动画界面， -->
			<div class="rows" v-if="!showAnimate">
		        <div class="pc-box" v-for="(item,index) in activities" :key="index" @click="goPath(item.id,item.type,item.status)">
		        	<div class="img-wrap">
		        		<img :src="baseUrl+item.smallImage" alt="">
		        	</div>
					<p class="title esp">{{item.name}}</p>
				</div>
		    </div> 
		    <!-- 在谷歌等主流浏览器显示动画  -->
	      	<div class="calendar-wrap" v-if="showAnimate" >
				<div class="calendar" style="z-index: 18989">
					<div class="cube" data-bg-color="#F7E8ED" data-title="Peaceful World">
					</div>
					<div class="cube" data-bg-color="#F2D9E6" data-title="Impossible">
					</div>
					<div class="cube" data-bg-color="#ECC6DE" data-title="Everything">
					</div>
					<div class="cube" data-bg-color="#E0ECF5" data-title="Hung Up">
					</div>
				</div>
				<div class="content">
					<div class="content__block">
						<h3 class="content__title"></h3>
						<p class="content__description"></p>
						<p class="content__meta"></p>
					</div>
					<div class="content__block">
						<h3 class="content__title"></h3>
						<p class="content__description"></p>
						<p class="content__meta"></p>
					</div>
					<div class="content__block">
						<h3 class="content__title"></h3>
						<p class="content__description"></p>
						<p class="content__meta"></p>
					</div>
					<div class="content__block">
						<h3 class="content__title"></h3>
						<p class="content__description"></p>
						<p class="content__meta"></p>
					</div>
					<div class="content__number">0</div>
				</div>
			</div>
		    <div class="more-btn">
		    	  <Button type="ghost" @click="showMore()">更多活动</Button>
		    </div>
		</div>
	</div>

	 <!-- ********************************************    手机端    ************************************************************ -->
	<div v-else class="phone-init-list">
		<div class="init-wrap">
			<p class="hr"></p>
			<div class="title">
				<span>即将开始的活动</span>
				<span class="look-more" @click="showMore()">查看更多 &nbsp;
					<i class="phone-icon"></i>
				</span>
			</div>
			<div class="show-img" >
				<div v-for="(item,index) in activities" :key="index"  @click="goPath(item.id,item.type,item.status)">
					<img :src="baseUrl+item.smallImage" alt="">
					<p class="esp myFloat1">{{item.name}}</p>
				</div>
			</div>
	    </div>
	</div>
</template>

<script>
	import "../../../static/animation/css/animation1.css"
	export default{
		data(){
			return {
				baseUrl:baseUrl,
				terminal:'pc',
				activities:[],
				activityCount:0,
				showAnimate:false,
			}
		},
		mounted(){
			this.terminal = IsPC() ? 'pc' : 'phone'
			this.getDatas(4);

			
		    if(this.terminal=='pc'){
		    	//判断浏览器
				var ua = window.navigator.userAgent;  
			    var isFirefox = ua.indexOf("Firefox") != -1;  
			    var isOpera = window.opr != undefined;  
			    var isChrome = ua.indexOf("Chrome") && window.chrome;  
			    var isSafari = ua.indexOf("Safari") != -1 && ua.indexOf("Version") != -1;

			    //判断IE
		    	var u = window.navigator.userAgent.toLocaleLowerCase(),
				ie11 = /(trident)\/([\d.]+)/,
				isIE = u.match(ie11);

		        this.showAnimate = isSafari ==true||isIE?false:true

		        if(this.showAnimate){
			        let url = "/static/animation/js/main1.js"
			        let script = document.createElement('script')
			        script.setAttribute('src', url)
			        this.$refs.animateJs.appendChild(script);
		        }
		    }
		},
		methods:{
			getDatas(length){
				this.$http.get("/v1/frontend/index/get-activity",{
					params:{
						length:length,
						status:1,//查询活动状态 所有传0 正在进行值2 已结束传3
						type:0,//查询活动类型 所有传0 大赛传1 问答传2
					}
				})
	            .then((res)=>{
	               if(res.data.status==200){
	                    this.activities=res.data.data.activities;
                        this.activityCount=res.data.data.activityCount;

                        if(this.showAnimate){
                        	var _this = this;
	                        setTimeout(function(){
	                        	var myCubeImg = document.querySelectorAll('.myShowImgs')
	                        	var myCubes = document.querySelectorAll('.cube')
	                        	var titleP = document.querySelectorAll('.title-p')

	                        	for(var i = 0;i< _this.activities.length;i++){
                                    myCubeImg[i].src = _this.baseUrl +  _this.activities[i].smallImage;
                                    myCubes[i].setAttribute('activityId',_this.activities[i].id);
                                    myCubes[i].setAttribute('type',_this.activities[i].type);
                                    titleP[i].innerHTML = _this.activities[i].name;
	                        	}
	                        },100)
                        }
	                }
	            })
	            .catch((err)=>{
	                console.log(err)
	            })
			},
			goPath(id,type,status){
				if(type==1){  //活动详情
					this.$router.push({path:"/frontPage/initDetail",query:{activityId:id,type:type,status:status}})
				}else if(type==2){   //跳转到新闻报道页面
					this.$router.push({path:"/frontPage/initQuestion",query:{questionId:id,type:type,status:status}})
				}
			},
			showMore(){
				this.$router.push("/frontPage/allActivity")
			},
		}
	}
</script>

<style lang="less">
  	.phone-init-list{
  		width: 100%;
  		background-color: #f2f2f2;
  		overflow: hidden;

  		.init-wrap{
  			width: 94%;
      		margin:0 auto;
      		padding-bottom: 20px;
  		}

      	.hr{
      		width: 6%;
      		height: 0.04rem;
      		margin-top: 0.2rem;
      		background-color: #000;
      	}

      	.title{
      		font-size: 0.16rem;
      		font-weight: 700;
      		line-height: 40px;
      	}

      	.look-more{
      		float:right;
      		color:#999;
      		font-size: 0.14rem;
      		font-weight: 400;
      		position: relative;
      		padding-right: 10px;
      	}

      	.show-img{
      		width: 100%;
      		overflow: hidden;
      	}
  		.show-img div{
  			float: left;
      		width: 48%;
      		height: 1.25rem;
      		margin-bottom: 0.15rem;
      		position: relative;
      		overflow: hidden;
      		border:1px solid #c0c0c0;
      	}
      	.show-img img{
      		width:100%;
      		height: 100%;
      	}
      	.show-img div:nth-child(2n){
      		margin-left: 4%;
      	}
      	.myFloat1{
      		background-color: #000;
  			opacity: 0.6;
  			color:white;
      		width: 100%;
      		overflow: hidden;
      		position: absolute;
      		bottom:0;
      		left:0;
      		padding: 0 3%;
      	}

      	.phone-icon{
			display: inline-block;
			width: 18px;
			height: 22px;
			background:url('/static/img/right.png') no-repeat;
			-webkit-background-size: 15px;
			background-size: 15px;
			position: absolute;
			top:12px;
			right:0px;
		}
    }

	.pc-init-activity{
      	width: 100%;
      	height: 550px;
      	background-color: #dde1e2;
      	
      	h3{
			font-size: 34px;
		}

		.rows {
			width: 1200px;
			overflow: hidden;
			margin:0 auto;
		}
		.pc-box{
			float: left;
			width: 266px;
			height: 250px;
			border:1px solid #bcbcbc;
			margin:0 45px 30px 0;
			cursor: pointer;
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
		.rows .pc-box:nth-child(4n){
			margin-right: 0;
		}

		.img-wrap{
			width: 100%;
			height: 190px;
			overflow: hidden;
			font-size: 0;

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
		
		.title{
			height: 60px;
			line-height: 60px;
			font-size: 16px;
			padding:0 15px;
		}

		.ivu-btn-ghost{
			width:88px;
			text-align: center;
			margin-top: 40px;
			font-size: 14px;
			background: #fff;
		}
	}

	.js{
		background-color: #F7E8ED;
		/*background: #f0f0f3;*/
		height: 600px;
	}
</style>