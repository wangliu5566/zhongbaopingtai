<template>
	<div class="mymatch" v-if="terminal=='pc'">
		<div class="menu-btn">
			<Button type="primary" :class="active==0?'active-btn':''" @click="goIndex(0,'/frontPage/person/mymatch/matchPass')">审核通过</Button>
			<Button type="primary" :class="active==1?'active-btn':''" @click="goIndex(1,'/frontPage/person/mymatch/matchNotPass')">未审核/未通过</Button>
		</div>
		<router-view></router-view>
	</div>
</template>

<script>
	export default {
		data(){
			return {
				baseUrl:baseUrl,
				terminal:'pc',
				active:0,
			}
		},
		mounted(){
			this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
			this.terminal = IsPC() ? 'pc' : 'phone';
			this.active = this.$route.path.indexOf("/matchNotPass")>0?1:0
			if(this.isSign&&this.terminal=='pc'){
			
			}else{
				this.$router.push("/frontPage")
			}
		},
		methods:{
			goIndex(index,path){
				this.active=index;
				this.$router.push(path)
			},
			
		},
	}
</script>


<style lang='less'>
	.mymatch{

		.menu-btn{
			width: 852px;
			margin:0 auto;
			margin:25px auto 0;

			.ivu-btn-primary{
				background-color: #fff;
				color:#1091db;
				font-size:15px;
			}
			.active-btn{
				background-color:#1091db;
				color:white;
			}
		}

		
	}
</style>