<template>
	<div class="pc-set" v-if="terminal=='pc'">
		<div class="set-item" v-if="dataList.length!=0" v-for="item in dataList" :key="item.id">
			<p class="set-title">{{item.prizeName}} <span style="float: right;font-size: 16px;">{{item.totalPeople}}名</span></p>
			<div class="left-intro">{{item.prizeIntro}}</div>
		</div>
		<div class="set-item1" v-if="dataList.length==0">
			暂无
		</div>
	</div>
	<div class="phone-set" v-else>
		<div class="set-item" v-for="item in dataList" :key="item.id">
			<p class="set-title">{{item.prizeName}} <span style="float: right;font-size: 0.15rem;">{{item.totalPeople}}名</span></p>
			<div class="left-intro">{{item.prizeIntro}}</div>
		</div>
	</div>
</template>
<script>
	export default{
		data(){
			return {
				terminal:'pc',
				activityId:"",
				activityEndTime:'',
				type:1,
				status:2,
			}
		},
		props:['dataList'],
		mounted(){
			this.terminal = IsPC() ? 'pc' : 'phone'
			if(this.$route.query){
				this.activityId=this.$route.query.activityId;
				this.type = this.$route.query.type
				this.status = this.$route.query.status
			}else{
				 this.$router.push("/frontPage/index")
			}
		},
		
	}
</script>

<style lang="less">
	.phone-set{
		width:100%;
		border:1px solid #666;
		padding: 0 3%;
		margin-bottom: 0.48rem;

		.set-item{
			border-bottom: 1px solid #666;
			line-height: 22px;
			overflow: hidden;
			padding-bottom: 15px;
		}
		.set-item:last-child{
			border-bottom: none;

		}

		.set-title{
			margin:0.15rem 0 0.1rem;
			font-weight: 700;
			font-size: 0.16rem;
		}
	}
	.pc-set{
		width:100%;
		border:2px solid #666;
		padding:0 38px;

		.set-item{
			border-bottom: 1px solid #666;
			padding: 10px 0;
			line-height: 22px;
		}
		.set-item:last-child{
			border-bottom: none;

		}
		.set-item1{
			padding: 10px 0;
			line-height: 22px;
		}

		.set-title{
			margin:30px 0 15px;
			font-weight: 700;
			font-size: 22px;
		}
		.left-intro{
			font-size: 15px;
			color:#999;
		}

		.set-right{
			float: right;
			margin-top: -15px;
			font-size: 18px;
			padding-right: 15px;
			color:#333;
		}
		
	}
</style>