<template>
	<div class="pc-page" v-if="terminal=='pc'">
		<div class="head-img">
			<div class="word-img">
				<img src="../../../static/img/文字.png">
			</div>
		</div>
		<div class="white-bg">
			<div class="slider-img" v-if="newObj.headImage">
				<img  :src="baseUrl+newObj.headImage">
			</div>
		
			<div class="content">
				<h3>{{newObj.title}}</h3>
				<p class="date">{{showDate}}</p>
				<div v-html="newObj.mainBody"></div>
			</div>
		</div>
	</div>

	<div class="phone-page" v-else>
		<div class="mobile-img">
			<img v-if="newObj.headImage" :src="baseUrl+newObj.headImage">
		</div>

		<div class="content">
			<h3>{{newObj.title}}</h3>
			<p class="date">{{showDate}}</p>
			<div class="html-1" v-html="newObj.mainBody"></div>
		</div>
		
	</div>
</template>

<script>
	export default{
		data(){
			return {
				baseUrl:baseUrl,
				terminal:'pc',
				newsId:'',
				newObj:{
					title:'',
					publishTime:''
				},
				showDate:'',
			}
		},
		mounted(){
			this.terminal = IsPC() ? 'pc' : 'phone'
			if(this.$route.query.newId){
				this.newsId=this.$route.query.newId;
				if(this.terminal=='pc'){
					this.getNewDetail();
				}else{
					this.getNewDetail();
				}
			}else{
				this.$router.push("/frontPage/index")
			}
			
		},
		methods:{
			getNewDetail(){
				this.$http.get("/v1/frontend/news/get-info",{
					params:{
						newsId:this.newsId
					}
				})
	            .then((res)=>{
	               if(res.data.status==200){
	               		this.newObj=res.data.data[0];
	               		var getTimes= res.data.data[0].publishTime;
	               		var myYear= new Date(parseInt(getTimes)*1000).getFullYear()
               			var myMonth= new Date(parseInt(getTimes)*1000).getMonth()+1
               			var myDay= new Date(parseInt(getTimes)*1000).getDate()
               			this.showDate = myYear+"-"+myMonth+'-'+myDay
	                }
	            })
	            .catch((err)=>{
	                console.log(err)
	            })
			},
		}
	}
</script>

<style lang='less'>
	.phone-page{
		width: 100%;
		overflow: hidden;

		.mobile-img{
			width: 100%;
			height: 1.75rem;
			margin-bottom: 0.2rem;
			img{
				width: 100%;
				height: 100%;
			}
		}

		.content{
			width: 94%;
			margin:0 auto 0.15rem;
			min-height: 3.3rem;
		}

		h3{
			font-size: 0.16rem;
			font-weight: 700;
			margin:0.15rem 0 0.05rem 0;
			color:#333;
			text-align:center;
		}
		.date{
			font-size: 0.14rem;
			color:#999;
			margin-bottom: 0.15rem;
			background-color: rgb(225,225,225);
			line-height: 0.3rem;
			padding-left: 2%;
		}
		.html-1{
			width: 100%;
			word-wrap:break-word;
			overflow-x: scroll;
		}
		pre {
			overflow-x:scroll;
			width: 1088px;
		}
		
	}
	.pc-page{
		width: 100%;
		overflow: hidden;
		background-color: #f4f4f4;

		.head-img{
			width: 100%;
			height: 310px;
			overflow: hidden;
			margin-bottom: 45px;
			background:url('../../../static/img/新闻页2.png') no-repeat;
		}
		.word-img{
			width: 576px;
			height: 130px;
			margin:120px auto 0;
		}

		.white-bg{
			width: 1200px;
			padding: 0 56px;
			margin:0 auto 40px;
			background-color: #fff;
			overflow: hidden;
			box-shadow: 0 0 8px #c0c0c0;
		}
		
		.slider-img{
			width: 100%;
			height:540px;
			margin-top:40px;
			overflow: hidden;
			img{
				width: 100%;
				height: 100%;
			}
		}

		.content{
			width: 100%;
			margin-bottom: 30px;
			font-size: 18px;
			line-height: 32px;
		}

		h3{
			font-size: 36px;
			font-weight: 700;
			line-height: 120px;
			text-align: center;
		}
		.date{
			font-size: 16px;
			color:#999;
			margin-bottom: 20px;
			height: 50px;
			line-height: 50px;
			background-color: #ececec;
			padding-left: 20px;
		}
		.title{
			margin-top: 35px;
		}
	}
</style>