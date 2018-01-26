<template>
	<div class="mail" v-if="terminal!='pc'">
		<div class="set-mail" style="position:relative;" >
            <span @click="goPage('/phoneData')">取消</span>
            <span>设置性别</span>
            <span @click="updateMail()">完成</span>
        </div>
        <div class="middle" :style="{minHeight:mailHeight}">
        	 <Select v-model="userObj.sex" placeholder="请选择">
                <Option value="男">男</Option>
                <Option value="女">女</Option>
                <Option value="保密">保密</Option>
            </Select>
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
				mailHeight:"2",
				userObj:{
					email:"",
					address:[],
					sex:"男"
				},
			}
		},
		mounted(){
			this.isSign=sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
			this.terminal = IsPC() ? 'pc' : 'phone'
			if(this.isSign){
				this.getSex()
			}else{
				this.$router.push("/frontPage")
			}

			this.fontSize = (window.innerWidth/ 750) * 200;
			this.mailHeight = document.body.clientHeight / this.fontSize - 1.45 + 'rem';
		},
		methods:{
			getSex(){
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
		         	 }
		        })
		        .catch((err) => {
		           console.log(err)
		        })
			},
            updateMail(){
        		var itemObj = JSON.parse(sessionStorage.personObj)
            	if(this.userObj.sex == itemObj.sex){
            		 this.$Message.error({
                        content:'值未改变',
                        duration:3,
                    });
            	}else{
            		this.$http.post(baseUrlCommon+"/v1/frontend/user/update-user-info", {
		                userId: sessionStorage.userId,
		                email:itemObj.email,
		                province:itemObj.address[0]?itemObj.address[0]:'',
		                city:itemObj.address[1]?itemObj.address[1]:'',
		                sex:this.userObj.sex,
			        })
			        .then((res) => {
				        if (res.data.status == 200) {
				        	this.$Message.success({
                                content:'保存成功',
                                duration:3,
                            })
				        	var _this =this 
				        	setTimeout(function(){
				        		_this.$router.push('/phoneData')
				        	},2000)
				        }
			        })
			        .catch((err) => {
			            console.log(err)
			        })
            	}
            },
            goPage(path){
				this.$router.push(path)
			},
		},
	}
</script>


<style lang='less'>
	.mail{
		.set-mail{
			height: 0.4rem;
			color:white;
			background-color: #000;
			line-height: 0.4rem;
			padding: 0 3%
		}
		.set-mail span:nth-child(2){
			margin-left: 30%;
			font-size: 0.16rem;

		}
		.set-mail span:nth-child(3){
			float: right;
			color:#0fcf49;
		}

		.middle{
			width: 100%;
			overflow: hidden;
			background-color: #efefef;
		}
		
		.ivu-select-selection{
			margin-top: 0.15rem;
			border-radius: 0;
			font-size: 0.14rem;
			height: 0.4rem;
			line-height: 0.4rem;
		}
		.ivu-select-single .ivu-select-selection .ivu-select-placeholder, .ivu-select-single .ivu-select-selection .ivu-select-selected-value{
			border-radius: 0;
			font-size: 0.14rem;
			height: 0.4rem;
			line-height: 0.4rem;
		}
		.ivu-cascader-arrow{
			top:60%;
			font-size: 0.16rem;
		}

	}
</style>