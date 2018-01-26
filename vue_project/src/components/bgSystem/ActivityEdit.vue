<template>
  <div>
    <ActivityEditCommon :activity-datas="activityEditDatas"></ActivityEditCommon>
  </div>
</template>
<script>
import ActivityEditCommon from "@/components/bgSystem/ActivityEditCommon"
export default {
  data() {
    return {
      activityEditDatas: {
        type: '1',
        organization: [],
        userId: '',
        userName: '',
        companyName: '',
        name: '',
        pcImage: '',
        mobileImage: '',
        smallImage: '',
        bigImage: '',
        intro: '',
        introImage: [],
        allowJoin:'0',
        prize: [{
          type: '',
          prizeName: '',
          prizeIntro: '',
          totalPeople: ''
        }],
        activityTime: [{
          type: '',
          signStartTime: '',
          signEndTime: '',
          reviewStartTime: '',
          announceAwardsTime: '',
        }],
        reviewStandard: '',
        activityStartTime: '',
        activityEndTime: '',
        questionAnswerDetail: '',
        score: {
          integration: '',
          number: '',
        }
      }
    }
  },
  components: {
    ActivityEditCommon
  },
  methods:{
    getActivityData(){
      this.$http.get('/v1/admin/activity/get-info-admin', {
        params: {
          type: this.$route.query.type,
          activityId: this.$route.query.id
        }
      }).then((res) => {
        this.activityEditDatas = Object.assign({},this.activityEditDatas,res.data.data.activity);
        this.activityEditDatas.organization = res.data.data.organization;
        this.activityEditDatas.introImage = res.data.data.introImages;
        this.activityEditDatas.prize = res.data.data.prizes;
        this.activityEditDatas.activityTime = res.data.data.activityTime;
        
        //处理时间
        if (this.activityEditDatas.activityTime) {
          this.activityEditDatas.activityTime.forEach((item,index)=>{
            this.$set(this.activityEditDatas.activityTime,index,{
              announceAwardsTime:this.secondsToDate(item.announceAwardsTime),
              reviewStartTime:this.secondsToDate(item.reviewStartTime),
              signEndTime:this.secondsToDate(item.signEndTime),
              signStartTime:this.secondsToDate(item.signStartTime),
              type:item.type
            })
          })
        }
        this.activityEditDatas.score = res.data.data.score;

        //把毫秒转为标准时间
        this.activityEditDatas.activityStartTime = this.secondsToDate(this.activityEditDatas.activityStartTime);
        this.activityEditDatas.activityEndTime = this.secondsToDate(this.activityEditDatas.activityEndTime)
      })
    }
  },
  mounted(){
    this.getActivityData();
  }
}

</script>
<style lang="less">


</style>
