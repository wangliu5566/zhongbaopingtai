<template>
	<div class="phone-personal" v-if="terminal!='pc'">
		<div id='phone-head' style="position:relative;" v-if="terminal!='pc'">
			<div class="call-back" @click="goIndex('/phonePerson')">个人中心</div>
            <img class="login-img" src="/static/img/logo1.png" @click="goIndex('/frontPage')">
        </div>
        <div class="middle" :style="{minHeight:personHeight}">
			<ul class="person-message">
				<li>头像<span class="float-r" style="padding-right:0"><img :src="url" class="show-img"></span></li>
				<li>账号<span class="float-r" style="padding-right:0">{{userObj.username}}</span></li>
				<li @click="goIndex('/phone')">手机<span class="float-r"><span class="float-span">{{userObj.phone?userObj.phone:'暂无'}}</span><i class="i-img"></i></span></li>
				<li @click="goIndex('/mail')">邮箱<span class="float-r"><span class="float-span">{{userObj.email?userObj.email:'暂无'}}</span><i class="i-img"></i></span></li>
				<li @click="goIndex('/psd')">密码<span class="float-r"><span class="float-span">{{userObj.setPwd==1?'修改':'设置'}}</span><i class="i-img"></i></span></li>
				<li @click="goIndex('/address')">城市<span class="float-r"><span class="float-span">{{userObj.address[0]?userObj.address[0]+' '+userObj.address[1]:'暂无'}}</span><i class="i-img"></i></span></li>
				<li @click="goIndex('/sex')">性别<span class="float-r"><span class="float-span">{{userObj.sex?userObj.sex:'保密'}}</span><i class="i-img"></i></span></li>
			</ul>
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
				userObj:{
					phone:"",
					email:"",
					address:[],
					sex:"男"
				},
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
			this.personHeight = document.body.clientHeight / this.fontSize - 1.65 + 'rem';
		},
		methods:{
			getPerson(){
				this.$http.get(baseUrlCommon+"/v1/frontend/user/get-user-info", {
		            params: {
		                userId: sessionStorage.userId,
		                accessToken:sessionStorage.accessToken
		            }
		        })
		        .then((res) => {
		          if (res.data.status == 200) {
		            this.userObj = res.data.data;
		            sessionStorage.personObj = JSON.stringify(this.userObj)
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
			goIndex(path){
				this.$router.push(path)
			},
		},
	}
</script>


<style lang='less'>
	.phone-personal{
		width: 100%;
		overflow:hidden;
		background-color: #efefef;

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

		.person-message li{
			background-color: #fff;
			width: 100%;
			height:0.44rem;
			border-bottom: 1px solid #d5d5d5;
			line-height:0.44rem;
			font-size: 0.14rem;
			padding-left: 3%;
			overflow:hidden;
		}
		.float-r{
			width: 50%;
			float: right;
			margin-right: 3%;
			padding-right:5%;
			display: block;
			height: 0.44rem;
			overflow:hidden;
			position: relative;
			text-align: right;
			white-space: normal;
			text-overflow: ellipsis;
		}
		.float-span{
			display: inline-block;
			height:0.44rem;
		}

		.show-img{
			width: 18%;
			border-radius: 50%;
			float: right;
			margin-top: 0.05rem;
		}
		
		.i-img{
			display: inline-block;
			width: 25%;
			height:0.44rem;
			background: url('/static/img/right2.png') no-repeat 20% 0.14rem;
			-webkit-background-size: 20%;
			background-size: 20%;
			position: absolute;
		}

	}
</style>