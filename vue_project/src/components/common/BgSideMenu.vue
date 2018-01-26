<template>
  <div>
    <Menu theme="light" accordion width="100%" :active-name="editUrl1($route.path)" @on-select="gopath" :open-names="[editUrl($route.path)]">
      <template v-for="(item,index) in menuList">
        <template v-if="!item.children.length">
          <Menu-item :name="item.path">
            <Icon :type="item.icon" size="16"></Icon>
            {{ item.label }}
          </Menu-item>
        </template>
        <template v-else>
          <Submenu :name="item.path">
            <template slot="title">
              <Icon :type="item.icon" size="16"></Icon>
              {{ item.label }}
            </template>
            <template v-for="(el,i) in item.children">
              <Menu-item :name="el.path">
                <Icon :type="el.icon" size="16"></Icon>{{ el.label}}
              </Menu-item>
            </template>
          </Submenu>
        </template>
      </template>
    </Menu>
    <!-- <Button @click="haha">haha</Button> -->
  </div>
</template>
<script>
import sha1 from 'crypto-js/sha1';
export default {
  data() {
    return {
      // isSuper: window.sessionStorage.getItem('isSuper')
    }
  },
  props: [
    'menuList'
  ],
  methods: {
    gopath(url) {
      //路由跳转
      this.$router.push(url);
    },
    editUrl(url) {
      let urlArr = url.split("\/");
      return '/' + urlArr[1] + '/' + urlArr[2];
    },
    editUrl1(url) {
      let urlArr = url.split("\/");
      if (urlArr[3] != undefined) {
        return '/' + urlArr[1] + '/' + urlArr[2] + '/' + urlArr[3];
      }else{
        return '/' + urlArr[1] + '/' + urlArr[2]
      }      
    },

    //sha1加密
    tkip(value){
      return  sha1(value).toString();
    },

    haha(){
      console.log(this.menuList)
    }
  }
}

</script>
<style>


</style>
