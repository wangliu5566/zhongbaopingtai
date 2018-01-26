<template>  
    <div v-if="terminal=='pc'" class="slider-pc" @mouseenter="stopPlay()" @mouseleave="autoPlay()">
        <img :src="baseUrl+url">
        <div class="left-img"  @click="preImg()">&lt;</div>
        <div class="right-img"  @click="nextImg()">&gt;</div>
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
            timer:"",

        }  
      }, 
      props:['imgList'], 
      mounted:function(){  
        this.terminal = IsPC() ? 'pc' : 'phone'
        this.autoPlay();
      },  
      methods: {  
        autoPlay(){
            this.url=this.imgList[0].introImage;
            var _this =this
            _this.timer=setInterval(function(){
                _this.imgObj=_this.imgList[_this.setIndex];
                _this.url=_this.imgList[_this.setIndex].introImage;
                _this.setIndex+=1;
                if(_this.setIndex == _this.imgList.length){
                    _this.setIndex=0;
                }
             },4000)
         },
        stopPlay(){
            var _this =this
            clearInterval(_this.timer)
         },
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
      }  
    }  
    
    </script>

 
<style lang='less'>
   .slider-pc{
        width: 100%;
        height: 370px;
        overflow: hidden;
        position: relative;

        img{
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .left-img,.right-img{
            width:30px;
            height:30px;
            background-color: #686868;
            position:absolute;
            bottom:2px;
            right: 0px;
            color:white;
            font-size: 20px;
            line-height: 30px;
            text-align: center;
            cursor: pointer;
        }
        .left-img{
            right:40px;
        }
    }  
</style>  