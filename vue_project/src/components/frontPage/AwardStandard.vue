<template>
	<div class="pc-standard" v-if="terminal=='pc'">
		<h3>参赛须知</h3>
		<textarea style="resize:none" readonly="readonly" class="word">{{datas}}</textarea>
		<div v-html=""></div>
	</div>
	<div class="phone-standard" v-else>
		<textarea style="resize:none" readonly="readonly"  class="word">{{datas}}</textarea>
	</div>
</template>

<script>
	export default {
		data(){
			return {
				terminal:'pc',
			}
		},
		props:['datas'],
		mounted(){
			this.terminal = IsPC() ? 'pc' : 'phone'
		},
	}
</script>

<style lang='less'>
	.phone-standard{
		width:100%;
		padding: 2%;

		.word{
			overflow: hidden;
			height: 2.7rem;
			width: 100%;
			border:none;
			font-size:0.14rem;
			overflow-y:visible;

		}

	}
	.pc-standard{
		width:100%;
		border:2px solid #666;
		padding: 0 34px;

		h3{
			line-height: 30px;
			font-size: 18px;
			font-weight: 700;
			color:#333;
			margin:20px 0 15px 0;
		}
		.word{
			height: 300px;
			overflow-y: visible;
			margin-bottom: 30px;
			width: 100%;
			border:none;
			font-size: 14px;
			line-height: 25px;
			padding: 15px;
		}
		.word:focus{
			outline: none;
		}

	}
</style>