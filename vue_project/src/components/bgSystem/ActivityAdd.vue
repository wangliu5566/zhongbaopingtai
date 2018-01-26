<template>
  <div>
    <ActivityEditCommon :activity-datas="activityAddDatas"></ActivityEditCommon>
  </div>
</template>
<script>
import ActivityEditCommon from "@/components/bgSystem/ActivityEditCommon"
export default {
  data() {
    return {
      activityAddDatas: {
        type: '1',
        organization:[],
        userId:'',
        userName:'',
        companyName:'',
        name: '',
        pcImage: '',
        mobileImage:'',
        smallImage:'',
        bigImage:'',
        intro: '',
        allowJoin:'0',
        introImage: [],
        prize: [{
          type:'',
          prizeName: '',
          prizeIntro: '',
          totalPeople : ''
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
        questionAnswerDetail:'',
        score:{
          integration:'',
          number:'',
        }
      }
    }
  },
  components: {
    ActivityEditCommon
  }
}

</script>
<style lang="less">


</style>
