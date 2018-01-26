<template>
	<div class="myactivities" v-if="terminal=='pc'">
		<div class="select-div">
			 <Form class="form1" :model="formItem9" :label-width="103" inline>
		        <Form-item label="活动类型：" >
		            <Select v-model="formItem9.isMine" placeholder="请选择">
		                <Option value="0">所有活动</Option>
		                <Option value="1">我参加的活动</Option>
		            </Select>
		        </Form-item>
		          <Form-item label="活动状态：" style="margin-left: 20px;">
		            <Select v-model="formItem9.status" placeholder="请选择">
		                <Option value="0">不限</Option>
		                <Option value="1">即将开始的活动</Option>
		                <Option value="2">正在进行的活动</Option>
		                <Option value="3">已结束的活动</Option>
		            </Select>
		        </Form-item>
	        </Form>
		</div>
		 <div class="rows" v-if="activities.length!=0">
	        <div class="pc-box" v-for="(item,index) in activities" :key="index">
	        	<div class="img-wrap"  @click="goPath(item.id,item.type,item.status)" >
	        		<img :src="baseUrl+item.smallImage" alt="">
	        	</div>
				<p class="title esp">{{item.name}}</p>
			</div>
	    </div>
	    <div v-if="activities.length==0" style="font-size: 20px;line-height: 120px;text-align: center;">
	    	这里没有活动哟，去<span @click="goIndex()" style="cursor: pointer;color:#2d8cf0;margin:0 auto;">首页</span>看看吧
	    </div>
	    <div class="page" v-if="totalCount>6">
	    	<Page :total="totalCount" :current="parseInt(page)" :page-size="pageSize" show-total @on-change="change"></Page>
	    </div>
	</div>
</template>

<script>
	export default {
		data(){
			return {
				baseUrl:baseUrl,
				terminal:'pc',
				isSign:true,
				liIndex:0,
				formItem9:{
					isMine:"1",
					status:"0",
				},
				activities:[],
				totalCount:1,
				page:sessionStorage.personPage?sessionStorage.personPage:1,
				pageSize:6,
			}
		},
		mounted(){
			this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
			this.terminal = IsPC() ? 'pc' : 'phone'
			if(this.isSign&&this.terminal=='pc'){
				this.getDatas(this.page,this.pageSize,this.formItem9.status,this.formItem9.isMine)
			}else if(this.isSign&&this.terminal=='phone'){
				this.$router.push('/phoneActivities')
			}else{
				this.$router.push("/frontPage")
			}
			
		},
		methods:{
			goIndex(){
				this.$router.push("/frontPage")
			},
			getDatas(page,pageSize,status,isMine){
				this.$http.get("/v1/frontend/index/get-activity",{
					params:{
						page:page,
						length:pageSize,
						status:status,//查询活动状态 所有传0 正在进行值2 已结束传3
						type:0,//查询活动类型 所有传0 大赛传1 问答传2
						userId:sessionStorage.userId,
						isMine:isMine,  //0是所有，1是我的
					}
				})
	            .then((res)=>{
	               if(res.data.status==200){
	                    this.activities=res.data.data.activities;
                        this.totalCount=res.data.data.activityCount;
	                }
	            })
	            .catch((err)=>{
	                console.log(err)
	            })
			},
			//分页切换当前页
			change(page){
				this.page=page;
				sessionStorage.personPage = page;
				this.getDatas(this.page,this.pageSize,this.formItem9.status,this.formItem9.isMine)
			},
			goPath(id,type,status){
				if(type==1){  //活动详情
					if(status ==1){
						this.$router.push({path:"/frontPage/initDetail",query:{activityId:id,type:type,status:status}})
					}else{
						this.$router.push({path:"/frontPage/detail",query:{activityId:id,type:type,status:status}})
					}
				}else if(type==2){   //跳转到新闻报道页面
					if(status==1){
						this.$router.push({path:"/frontPage/initQuestion",query:{questionId:id,type:type,status:status}})
					}else{
						this.$router.push({path:"/frontPage/question",query:{questionId:id,type:type,status:status}})
					}
				}
			},
		},
		watch:{
			formItem9:{
　　　　　　　　handler(curVal,oldVal){
　　　　　　　　　　this.formItem9 = curVal;
					this.page=1;
					sessionStorage.personPage = this.page;
					this.getDatas(this.page,this.pageSize,this.formItem9.status,this.formItem9.isMine)
　　　　　　　　},
　　　　　　　　deep:true
　　　　　　}
		}
	}
</script>


<style lang='less'>
	.myactivities{
		.select-div{
			width: 100%;
			background-color: #fff;
		}
		.form1{
			width: 852px;
			margin:0 auto;
			padding-top: 32px;
			margin-bottom: 10px;
		}
		.ivu-select-selection{
			width: 260px;
			height: 36px;
			line-height: 36px;
		}

		.rows {
			width: 852px;
			margin:0 auto;
			overflow: hidden;
		}
		.pc-box{
			float: left;
			width: 266px;
			height: 250px;
			border:1px solid #dcdcdc;
			margin:0 27px 30px 0;
			cursor: pointer;
		}
		.rows .pc-box:nth-child(3n){
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
		
		.title{
			height: 60px;
			line-height: 60px;
			font-size: 14px;
			padding:0 15px;
		}

		.ivu-btn-ghost{
			width:88px;
			text-align: center;
			font-size: 14px;
		}

		.page{
			overflow: hidden;
			height:40px;
			width:100%;
			text-align: center;
		}
	}
</style>