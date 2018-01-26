<template>
	<div class="pc-time" v-if="terminal=='pc'">
		<table align="center" cellspacing="0" >
			<thead >
				<tr>
					<th>类别<i></i></th>
					<th>报名开始<i></i></th>
					<th>报名结束<i></i></th>
					<th>评审开始<i></i></th>
					<th>获奖公布</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="item in datas" style="border-bottom:2px solid #999;">
					<td>{{item.type}}</td>
					<td>{{getLocalTime(item.signStartTime)}}</td>
					<td>{{getLocalTime(item.signEndTime)}}</td>
					<td>{{getLocalTime(item.reviewStartTime)}}</td>
					<td>{{getLocalTime(item.announceAwardsTime)}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="phone-time" v-else>
		<div class="time-item" v-for="item in datas">
			<h4>{{item.type}}</h4>
			<p class="time-p">
				<span>报名开始：{{getLocalTime(item.signStartTime)}}</span>
				<span>评审开始:{{getLocalTime(item.reviewStartTime)}}</span>
			</p>
			<p class="time-p">
				<span>报名开始：{{getLocalTime(item.signStartTime)}}</span>
				<span>公布获奖:{{getLocalTime(item.announceAwardsTime)}}</span>
			</p>
		</div>
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
		methods:{
			getLocalTime(nS) {  
           		var myYear= new Date(parseInt(nS)*1000).getFullYear()
       			var myMonth= new Date(parseInt(nS)*1000).getMonth()+1
       			var myDay= new Date(parseInt(nS)*1000).getDate()
       			var showDate = myYear+"-"+myMonth+'-'+myDay   
			   return showDate
			}     
		},
	}
</script>

<style lang='less'>
	.phone-time{
		width:100%;
		margin-bottom: 0.48rem;
		border:1px solid #666;
		padding: 0 3%;

		.time-item{
			line-height: 0.3rem;
			font-size: 0.16rem;
			padding-bottom: 0.1rem;
			border-bottom: 1px solid #313131;
		}

		.time-item:last-child{
			border-bottom: none;
		}

		h4{
			font-size: 17px;
			font-weight: 400;
			line-height: 0.3rem;
			margin-top: 0.1rem;
		}

		.time-p{
			font-size: 0.14rem;
			color:#999;
			font-weight: 400;
			line-height: 0.26rem;

		}
		.time-p span:nth-child(2n){
			float: right;
			margin-right: 2%;
		}

	}
	.pc-time{
		width:100%;

		table{
			border:2px solid #666;
		}

		th,td{
			width: 240px;
			font-size: 18px;
			line-height: 72px;
			text-align: center;
			border-bottom:1px solid #999;
		}

		th{
			border-bottom:2px solid #666;
			line-height: 81px;
			font-weight: 700;
		}
		tbody tr:last-child{
			border-bottom: none;
		}

		th i{
			display: inline-block;
			width: 1px;
			height: 23px;
			float: right;
			background-color: black;
			margin-top: 28px;
		}
	}
</style>