<template>
	<div class="phone-person"  v-if="terminal!='pc'">
		<div id='phone-head' style="position:relative;" v-if="terminal!='pc'">
            <img class="login-img" src="/static/img/logo1.png" @click="goIndex('/frontPage')">
        </div>
		<div class="head-top">
			<img :src="url" class="head-url">
			<p class="user-name">{{userObj.username}}</p>
			<p class="user-mail">{{userObj.email?userObj.email:'暂无邮箱'}}</p>
		</div>
		<div class="middle" :style="{minHeight:personHeight}">
			<ul class="phone-ul">
				<li style="background:url('/static/img/活动.png') no-repeat 3% 0.12rem;background-size: 6%;"  @click="goIndex('/phoneActivities')">我的活动<span></span></li>
				<li style="background:url('/static/img/作品.png') no-repeat 4% 0.12rem;background-size: 5%;"  @click="goIndex('/phoneMatch')">我的作品<span></span></li>
				<li style="background:url('/static/img/资料.png') no-repeat 4% 0.12rem;background-size: 5%;"  @click="goIndex('/phoneData')">个人资料<span></span></li>
			</ul>

			<p class="go-out" @click="goOut">退出账号</p>
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
	</div>
</template>

 <script>
	export default {
		data(){
			return {
				baseUrl:baseUrl,
				terminal:'pc',
				isSign:true,
				url:"/static/img/default_photo.png",
				userObj:{},
				fontSize:"",
			}
		},
		mounted(){
			this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
			this.terminal = IsPC() ? 'pc' : 'phone'
			if(this.isSign&&this.terminal!='pc'){
				this.getPerson()
			}else{
				this.$router.push("/frontPage")
			}
			
        	this.fontSize = (window.innerWidth/ 750) * 200;
			this.personHeight = document.body.clientHeight / this.fontSize - (1.65+1.44) + 'rem';
		},
		methods:{
			goIndex(path){
				this.$router.push(path)
			},
			getPerson(){
				this.$http.get(baseUrlCommon+"/v1/frontend/user/get-user-info", {
		            params: {
		                userId: sessionStorage.userId,
		            }
		        })
		        .then((res) => {
		          if (res.data.status == 200) {
		            this.userObj = res.data.data;
		            if (res.data.data.bigAvatar) {
		              this.url = res.data.data.bigAvatar
		            } else if (res.data.data.middleAvatar) {
		              this.url = res.data.data.middleAvatar
		            } else {
		              this.url = "/static/img/default_photo.png";
		            }
		          }
		        })
		        .catch((err) => {
		          console.log(err)
		        })
			},
			goOut(){
				sessionStorage.removeItem('userId');
	            sessionStorage.removeItem('userObj');
	            this.$router.push("/frontPage/index")
			}
		},
	}
 </script>


<style lang='less'>
.phone-person{
	background-color: #efefef;
	
	.head-top{
		overflow:hidden;
		height: 1.44rem;
		background:url("/static/img/bg1.png");
		color:white;
	}

	.head-url{
		width: 16%;
		margin: 0.19rem 0 0 42%;
		border-radius: 50%;
	}
	.user-name{
		font-size: 0.16rem;
		text-align: center;
		line-height:0.3rem;
	}
	.user-mail{
		font-size:0.12rem;
		color:#e0979f;
		line-height: 0.2rem;
		text-align: center;
	}
	.middle{
		width: 100%;
		overflow: hidden;
	}
	.phone-ul{
		margin-top: 0.2rem;
		overflow:hidden;
		background-color: #fff;
	}
	.phone-ul li{
		height:0.44rem;
		line-height:0.44rem;
		font-size: 0.14rem;
		cursor: pointer;
		border-bottom: 1px solid #d5d5d5;
		padding-left: 12%;

		span{
			float: right;
			width: 8%;
			height: 100%;
			background:url('/static/img/right2.png') no-repeat 0 0.13rem;
			-webkit-background-size:30%;
			background-size:30%;
		}
	}
	.go-out{
		margin-top: 0.2rem;
		height: 0.44rem;
		line-height: 0.44rem;
		text-align: center;
		color:#Ce354b;
		background-color: #fff;
		font-size: 0.15rem;
	}
}
</style>