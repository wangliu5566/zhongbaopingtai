<template>
    <div class="not-pass" :style="{minHeight:personHeight}">
       <div class="not-pass-list"  v-for="item in dataList" :key="item.id">
           <p class="one-line">
               <span class="line-title">{{item.clientFileName}}</span>
               <span class="line-right">{{item.totalSize}}</span>
           </p>
           <p class="one-line">
               <span class="line-title">{{item.name}}</span>
               <span class="line-right">{{item.status}}</span>
           </p>
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
                fontSize:16,
                personHeight:2,
                dataList:[]
            }
        },
        mounted(){
            this.isSign=sessionStorage.userId!=''&&sessionStorage.userId!=undefined&&sessionStorage.userObj!=undefined?true:false;
           
            this.terminal = IsPC() ? 'pc' : 'phone'
            if(this.isSign && this.terminal!='pc'){
                this.getDatas(1,1000)
            }else{
                this.$router.push("/frontPage")
            }
            this.active = this.$route.path.indexOf("/matchNotPass")>0?1:0
            this.fontSize = (window.innerWidth/ 750) * 200;
            this.personHeight = document.body.clientHeight / this.fontSize - 2.4 + 'rem';
        },
        methods:{
            getDatas(page,pageSize){
                this.$http.get("/v1/frontend/works/get-all-upload-files",{
                    params:{
                        page:page,
                        length:pageSize,
                        userId:sessionStorage.userId,
                    }
                })
                .then((res)=>{
                    if(res.data.status==200){  
                        var arr = res.data.data.lists;
                        arr.forEach(function(item,index){
                            item.status = item.status==2?"待审核":"未通过";//status: 1 通过 2 待审核 3未通过
                            if(Math.ceil(item.totalSize /1024)>1){
                                if(Math.ceil(item.totalSize /(1024*1024))>1){
                                    item.totalSize = Math.ceil(item.totalSize /(1024*1024)) +'M'
                                }else{
                                    item.totalSize = Math.ceil(item.totalSize /1024) +'KB'
                                }
                            }
                        })
                        this.dataList = arr;
                    }
                })
                .catch((err)=>{
                    console.log(err)
                })
            },
            goIndex(page) {
               this.$router.push(page)
            },
            goOther(index,path){
                this.active = index;
                this.$router.push(path)
            },
        }
    }
</script>


<style lang='less'>
    .not-pass{
        width: 100%;
        background-color: #efefef;
        overflow: hidden;

        .not-pass-list{
            width: 94%;
            height: 0.75rem;
            margin:0 auto 0.1rem;
            background-color: #fff;
        }

        .one-line{
            height: 0.32rem;
            line-height: 0.37rem;
            width: 100%;
            overflow: hidden;
            font-size: 0.14rem;
            padding-top: 0.05rem;
        }

        .line-title,.line-right{
            float: left;
            display:block;
            height: 100%;
        }
        .line-title{
            width: 70%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            padding-left: 5%;
        }
        .line-right{
            width: 30%;
            text-align: right;
            padding-right: 5%;
        }
      
    }
        
</style>