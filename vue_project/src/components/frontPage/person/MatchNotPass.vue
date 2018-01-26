<template>
    <div class="not-pass">
        <Table border :columns="columns1" :data="data1"></Table>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                baseUrl:baseUrl,
                terminal:'pc',
                isSign:false,
                columns1: [
                    {
                        title: '作品名字',
                        key: 'clientFileName'
                    },
                    {
                        title: '活动名字',
                        key: 'name'
                    },
                    {
                        title: '作品大小',
                        key: 'totalSize'
                    },
                    {
                        title: '审核状态',
                        key: 'status'
                    }
                ],
                data1: [],
            }
        },
        mounted(){
            this.isSign=sessionStorage.userId!=''&&sessionStorage.userId!=undefined&&sessionStorage.userObj!=undefined?true:false;
            this.terminal = IsPC() ? 'pc' : 'phone'
            if(this.isSign&&this.terminal=='pc'){
                this.getDatas(1,99999)
            }else{
                this.$router.push("/frontPage")
            }
        },
        methods:{
            goIndex(index,path){
                this.active=index;
                this.$router.push(path)
            },
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
                        this.data1 = arr;

                    }
                })
                .catch((err)=>{
                    console.log(err)
                })
            },
            
        },
    }
</script>


<style lang='less'>
    .not-pass{
        width: 852px;
        overflow: hidden;
        margin:30px auto;

        .ivu-table{
            font-size: 14px;
        }
    }
</style>