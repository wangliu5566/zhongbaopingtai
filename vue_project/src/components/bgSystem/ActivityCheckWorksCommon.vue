<template>
  <div>
    <Form :label-width="100" inline>
      <Form-item label="作品类别：" style="width:47%">
        <Input readonly :value="worksDatas.type"></Input>
      </Form-item>
      <Form-item label="作品名称：" style="width:96%">
        <Input readonly :value="worksDatas.workName"></Input>
      </Form-item>
      <Form-item label="作者姓名：" style="width:47%;">
        <Input readonly :value="worksDatas.username"></Input>
      </Form-item>
      <Form-item label="电话号码：" style="width:47%;">
        <Input readonly :value="worksDatas.tel"></Input>
      </Form-item>
      <Form-item label="所在院系：" style="width:47%;">
        <Input readonly :value="worksDatas.departments"></Input>
      </Form-item>
      <Form-item label="学号：" style="width:47%;">
        <Input readonly :value="worksDatas.studentId"></Input>
      </Form-item>
    </Form>
  </div>
</template>
<script>
export default {
  data() {
    return {
      baseUrl:baseUrl,
    }
  },
  props: [
    'worksDatas'
  ],
  methods:{
    previeWorks(item){
      console.log(item)
    },
  }
}

</script>
<style lang="less">

</style>
