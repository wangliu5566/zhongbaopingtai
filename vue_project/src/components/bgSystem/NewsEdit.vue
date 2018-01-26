<template>
  <div>
    <NewsEditCommon :news-datas="editNewsDatas"></NewsEditCommon>
  </div>
</template>
<script>
import NewsEditCommon from '@/components/bgSystem/NewsEditCommon'
export default {
  data() {
    return {
      editNewsDatas: {
        title: '',
        username: '',
        userId: '',
        activityId: '',
        newsSummary: '',
        coverPath: '',
        mainBody: '',
        headImage:'',
      },
    }
  },
  components: {
    NewsEditCommon
  },
  methods: {
    
  },
  mounted() {
    
  }
}

</script>
<style lang="less">


</style>
