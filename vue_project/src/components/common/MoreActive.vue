<template>
	<div class="more">
	 	<div  class="pc-more" v-if="terminal=='pc'">
			<div class="head">
				<h3><i class="pre"></i>更多活动<i class="next"></i></h3>
					<p>More activities</p>
		 	</div>
			<div class="rows">
		        <div class="pc-box" v-for="(item,index) in activities" :key="index" @click="goPath(item.id,item.type,item.status)">
		        	<div class="img-wrap">
		        		<img :src="baseUrl+item.smallImage" alt="">
		        	</div>
					<p class="title esp">{{item.name}}</p>
				</div>
		    </div>
		    <div class="more-btn">
		    	  <Button type="ghost" @click="showMore()">更多活动</Button>
		    </div>
		</div>
		<div v-else class="phone-content">
			<p class="hr"></p>
			<div class="title">
				<span>更多活动</span>
				<span class="look-more" v-if="!isShowMore" @click="showMore()">查看更多 &nbsp;
					<i class="phone-icon"></i>
				</span>
				<span class="look-more" v-else>查看更多 &nbsp;<i class="phone-icon"></i></span>
			</div>
			<div class="show-img" >
				<div v-for="(item,index) in activities" :key="index"  @click="goPath(item.id,item.type,item.status)">
					<img :src="baseUrl+item.smallImage" alt="">
					<p class="esp myFloat">{{item.name}}</p>
				</div>
			</div>
			 <div class="more-btn" v-if="isShowMore">
		    	  <Button type="ghost" @click="showMore()">{{showWord}}</Button>
		    </div>
		</div>

	</div>
</template>

<script>
	export default{
		data(){
			return {
				baseUrl:baseUrl,
				terminal:'pc',
				activities:[],
				activityCount:0,
				isShowMore:false,
			}
		},
		mounted(){
			this.terminal = IsPC() ? 'pc' : 'phone'
			this.getDatas(4);
		},
		methods:{
			getDatas(length){
				this.$http.get("/v1/frontend/index/get-activity",{
					params:{
						length:length,
						status:0,//查询活动状态 所有传0 正在进行值2 已结束传3
						type:0,//查询活动类型 所有传0 大赛传1 问答传2
					}
				})
	            .then((res)=>{
	               if(res.data.status==200){
	                    this.activities=res.data.data.activities;
                        this.activityCount=res.data.data.activityCount;
	                }
	            })
	            .catch((err)=>{
	                console.log(err)
	            })
			},
			goPath(id,type,status){
				if(type==1){  
					if(status==1){
						this.$router.push({path:"/frontPage/initDetail",query:{activityId:id,type:type,status:status}})
					}else{
						window.location.href="/frontPage/detail?activityId="+id+"&type="+type+"&status="+status
					}
				}else if(type==2){  
					if(status == 1){
						this.$router.push({path:"/frontPage/initQuestion",query:{questionId:id,type:type,status:status}})
					}else{
						window.location.href="/frontPage/question?questionId="+id+"&type="+type+"&status="+status
					}
				}
			},
			showMore(){
				this.$router.push({path:"/frontPage/allActivity",query:{status:2}})
			},
		}
	}
</script>

<style lang="less">
		
  	.phone-content{
      		width: 94%;
      		margin:0 auto;
      		padding-bottom: 20px;

      	.hr{
      		width: 6%;
      		height: 2px;
      		margin-top: 0.2rem;
      		background-color: #000;
      	}

      	.title{
      		font-size: 0.14rem;
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
      	.myFloat{
      		background-color: #000;
  			opacity: 0.6;
  			color:white;
      		width: 100%;
      		overflow: hidden;
      		position: absolute;
      		bottom:0;
      		left:0;
      	}

      	.phone-icon{
			display: inline-block;
			width: 18px;
			height: 22px;
			background:url('/static/img/right.png') no-repeat ;
			-webkit-background-size: 15px;
			background-size: 15px;
			position: absolute;
			top:12px;
			right:0px;
		}
    }

	.pc-more{
      	width: 1200px;
      	margin:0 auto;

      	h3{
			font-size: 34px;
		}

		.rows {
			width: 1200px;
			overflow: hidden;
		}
		.pc-box{
			float: left;
			width: 266px;
			height: 250px;
			border:1px solid #dcdcdc;
			margin:0 45px 30px 0;
			cursor: pointer;
		}
		.rows .pc-box:nth-child(4n){
			margin-right: 0;
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
	    
	    .rows .pc-box:nth-child(4n) {
	        margin-right: 0;
	    }

	    .img-wrap {
	        width: 100%;
	        height: 190px;
	        overflow: hidden;

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

	    .title {
	        height: 60px;
	        line-height: 60px;
	        font-size: 16px;
	        padding-left: 15px;
	    }

		.ivu-btn-ghost{
			width:88px;
			text-align: center;
			font-size: 14px;
		}
	}
</style>