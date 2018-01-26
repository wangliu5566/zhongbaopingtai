<template>  
    <div v-if="terminal=='pc'" class="slider-work">
        <img :src="baseUrl+url">
        <div class="leftImg"  @click="preImg()"><img src="../../../static/img/m-left.png"></div>
        <div class="rightImg"  @click="nextImg()"><img src="../../../static/img/m-right.png"></div>
  </div>
</template>  

<script>
    export default {  
      data () {  
        return { 
            terminal:"pc",
            baseUrl:baseUrl,
            setIndex:0,
            imgObj:{},
            url:"",
        }  
      }, 
      props:['imgList','selectWorkId'], 
      mounted:function(){  
        this.terminal = IsPC() ? 'pc' : 'phone'
        var _this =this
        _this.imgList.forEach(function(item,index){
            if(item.fileId==_this.selectWorkId){
                _this.setIndex=index;
            }
        })
        _this.url=_this.imgList[_this.setIndex].introImage;
      },  
      methods: {  
        nextImg(){
          this.setIndex+=1;
          if(this.setIndex == this.imgList.length){
              this.setIndex=0;
          }
          this.url=this.imgList[this.setIndex].introImage;
        },
        preImg(){
           this.setIndex-=1;
            if(this.setIndex <0){
              this.setIndex=this.imgList.length-1;
          }
          this.url=this.imgList[this.setIndex].introImage;
        },
        //点击退出大图模式，回退到小图模式
        // onesDetails(){
        //     this.$emit("clickImg")
        // },
        // 统计作品点击量
        putClickCount(id,type){
          this.$http.post("/v1/frontend/works/record-click",{
            fileId:id,
            type:type,
          })
          .then((res)=>{
            if(res.data.status==200){
            }
          })
        },
      },
      watch:{
        setIndex:{
          handler(curVal,oldVal){  //记录pdf点击上一页下一页的时候
            this.putClickCount(this.imgList[this.setIndex].fileId,this.imgList[this.setIndex].type); 
  　　　　}
        }
      } 
    }  
    
    </script>

 
<style lang='less'>
   .slider-work{
        width: 100%;
        overflow: hidden;
        position: relative;

        img{
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

       .leftImg,.rightImg{
            width:60px;
            height:60px;
            position:absolute;
            top:45%;
        }
        .leftImg{
            left:50px;
        }
        .rightImg{
            right:50px;
        }
    }  

</style>  