<template>
	<div class="phone-match" v-if="terminal!='pc'" >
		<div id='phone-head' style="position:relative;">
			<div class="call-back" @click="goIndex('/phonePerson')">个人中心</div>
            <img class="login-img" src="/static/img/logo1.png" @click="goIndex('/frontPage')">
        </div>
        <div class="menu-btn">
            <button :class="active==0?'active-btn':''" @click="goOther(0,'/phoneMatch/matchPass')">审核通过</button>
            <button :class="active==1?'active-btn':''" @click="goOther(1,'/phoneMatch/matchNotPass')">未审核/未通过</button>
        </div>

		<router-view></router-view>
        
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
                active:0,
			}
		},
		mounted(){
			this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
			this.terminal = IsPC() ? 'pc' : 'phone';
            this.active = this.$route.path.indexOf("/matchNotPass")>0?1:0
			if(this.isSign&&this.terminal!='pc'){

			}else{
				this.$router.push("/frontPage")
			}
		},
		methods:{
			goIndex(page) {
	           this.$router.push(page)
	        },
            goOther(index,path){
                this.active=index;
                this.$router.push(path)
            },
		}
	}
</script>


<style lang='less'>
	.phone-match{
		width: 100%;
		background-color: #efefef;
		overflow:hidden;
        overflow-y: auto;

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

        .menu-btn{
            width: 94%;
            height: 0.44rem;
            margin:0.15rem auto;

            button{
                background-color: #fff;
                color:#333;
                font-size:0.15rem;
                outline: none;
                border:none;
                width: 49%;
                height: 100%;
            }
            .active-btn{
                background-color:#ce354b;
                color:white;
            }
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
</style>