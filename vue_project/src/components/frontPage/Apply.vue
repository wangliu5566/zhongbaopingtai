<template>
  <div class="apply" ref="applys">
    <h3 class="big-title"><span class="return-detail" @click="retuenDetail">&lt; &nbsp;返回大赛详情</span>立即参赛</h3>
    <Form ref="formItem" :model="formItem" :label-width="80" :rules="ruleValidate" style="padding-top: 30px;">
      <!-- <p class="school">学校：{{companyName}}</p> -->
      <Form-item prop="type">
        <Select v-model="formItem.type" placeholder="作品类别">
          <Option v-for="item in selectList" :value="item.type" :key="item.id">{{item.type}}</Option>
        </Select>
      </Form-item>
      <Form-item prop="workName">
        <Input v-model.trim="formItem.workName" placeholder="作品名称">作品名称</Input>
      </Form-item>
      <Form-item prop="username">
          <Input v-model.trim="formItem.username" placeholder="作者姓名"></Input>
        </Form-item>
      <Row>
        <Col span="12" class="item-left">
         <Form-item prop="tel">
          <Input v-model.trim="formItem.tel" placeholder="手机号码" class="input-left"></Input>
        </Form-item>
        </Col>
        <Col span="12" class="item-right">
        <Form-item prop="departments">
          <Select ref="foo" v-model="formItem.departments" placeholder='所在机构' class="input-right" @on-change="companyChange" filterable remote :remote-method="remoteMethod" :label="initDepartments" :loading="loading" clearable not-found-text="">
            <Option v-for="(item,index) in companyListFilter" :value="item.name" :key="item.id+index">{{item.name}}</Option>
          </Select>
        </Form-item>
        </Col>
      </Row>
      <Row>
        <Col span="12" class="item-left">
        <Form-item prop="studentId">
          <Input v-model.trim="formItem.studentId" placeholder="学号" class="input-left"></Input>
        </Form-item>
        </Col>
        <Col span="12" class="item-right">
        <Form-item prop="division">
          <Input v-model.trim="formItem.division" placeholder="院系" class="input-right"></Input>
        </Form-item>
        </Col>
      </Row>
      <div class="load-wrap" v-show="uploadStatus==0">
        <div id="upload"></div>
        <div style="overflow: hidden;margin-top: 15px;">
          <button id="btn2">上传</button>
          <button id="btn1">暂停</button>
          <button id="btn3">删除</button>
        </div>
      </div>
      <div class="show-load" v-show="uploadStatus==1">
        <span>您已上传文件：</span>
        <span class="file-names">{{fileInfo.clientFileName}}</span>
        <button class="del-loaded-file" @click="delLoadedFile">删除</button>
      </div>
      <Form-item>
        <Checkbox-group v-model="checkbox">
          <Checkbox label="我同意用户协议" :class="checkbox.length==0?'labels':''"></Checkbox>
        </Checkbox-group>
      </Form-item>
      <a class="userAgree" href="/frontPage/user" target="_blank">《用户协议》</a>
      <Form-item>
        <Button type="primary" @click="handleSubmit('formItem')">提交作品</Button>
      </Form-item>
    </Form>
    <!-- 参赛作品 -->
    <div class="join-active">
      <games :activityId="activityId" v-if="activityId!=''"></games>
    </div>
    <!-- 更多活动 -->
    <div class="more-active">
      <moreActive></moreActive>
    </div>
  </div>
</template>
<script>
import games from "../common/Games.vue"
import moreActive from "../common/MoreActive.vue"
export default {
  data() {
    const validatePhone = (rule, value, callback) => {
      var myreg = /^1[34578][0-9]{9}/;
      if (!value) {
        return callback(new Error('电话不能为空'));
      } else if (!myreg.test(value)) {
        return callback(new Error('请输入有效电话'));
      } else {
        callback();
      }
    };
    return {
      activityId: '',
      type: 1,
      terminal: 'pc',
      selectList: [],
      formItem: {
        type: '',
        workName: '',
        username: "",
        tel: "",
        studentId: "",
        departments: "",
        division:""
      },
      checkbox: [],
      ruleValidate: {
        type: [
          { required: true, message: '作品类别不能为空', trigger: 'change' }
        ],
        workName: [
          { required: true, message: '作品名称不能为空', trigger: 'blur' }
        ],
        username: [
          { required: true, message: '作者姓名不能为空', trigger: 'blur' }
        ],
        tel: [
          { validator: validatePhone, trigger: 'blur'},
        ],
        studentId: [
          { required: true, message: '学号不能为空', trigger: 'blur' }
        ],
        departments: [
          { required: true, message: '机构不能为空', trigger: 'blur' }
        ],
        division:[
          { required: true, message: '院系不能为空', trigger: 'blur' }
        ]
      },
      fileObj: {},
      isSubmite: false,
      companyName: "",
      uploadStatus: 0, //一进来就获取该用户上传的进度:0是还没上传作品和没传完，1是传完了，不能再选择文件
      fileInfo: {},
      timer2:null,
      loading:false,
      companyList:[],
      companyListFilter:[],
      initDepartments:"",
      isSign:false,
    }
  },
  mounted() {
    this.terminal = document.body.clientWidth < 768 ? 'phone' : 'pc';
    this.isSign =sessionStorage.userId == '' || !sessionStorage.userId || sessionStorage.userId == "null"?false:true;
    if (this.$route.query.activityId && this.isSign) {
      this.activityId = this.$route.query.activityId;
      this.type = this.$route.query.type;
      this.getActivityStatus = this.$route.query.status
      this.getSelectList();
      this.getProductionDetail()
      this.getCompanyList();
    } else {
      this.$router.push("/frontPage/index")
    }
    sessionStorage.removeItem('fileName')
    this.creatHupload()
  },
  components: {
    moreActive,
    games
  },
  methods: {
    //请求下拉框数据
    getSelectList() {
      this.$http.get("/v1/frontend/works/type-lists", {
          params: {
            activityId: this.activityId,
          }
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.selectList = res.data.data;
          }
        })
    },
    //获取机构列表
    getCompanyList() {
      this.$http.get('/v1/admin/user/agency-list')
        .then((res) => {
          this.companyList = res.data.data.data;
        })
    },
    //搜索机构列表
    companyChange() {
      if (this.formItem.departments!= '') {
      } else {
      }
    },
     //筛选机构
    remoteMethod(query) {
      if (query !== '') {
        this.loading = true;
        this.companyListFilter = this.companyList.filter(item => item.name.indexOf(query) > -1);
        this.loading = false;
      } else {
        this.companyListFilter = [];
      }
    },
    //获取作品详情
    getProductionDetail() {
      this.$http.get("/v1/frontend/works/user-work", {
          params: {
            activityId: this.activityId,
            userId: sessionStorage.userId,
          }
        })
      .then((res) => {
        if (res.data.status == 200) {
          if(!res.data.data.bindPhone){
            this.$Modal.confirm({
              title: '提示',
              content: '<p>目前您还没绑定电话，请前往个人中心绑定电话，如果不绑定电话，将会无法提交作品</p>',
              onOk: () => {
                this.$router.push('/frontPage/person/personalData');
              }
          })
          }
          this.formItem = res.data.data.formItem ? res.data.data.formItem : this.formItem;
          this.companyName = res.data.data.companyName
          this.uploadStatus = res.data.data.uploadStatus;
          this.fileInfo = res.data.data.fileInfo ? res.data.data.fileInfo : {}
          this.initDepartments =res.data.data.formItem.departments ? res.data.data.formItem.departments:"";
          this.$refs.foo.setQuery(this.initDepartments)
          if (res.data.data.signStatus == 2) { //已经完整的提交了 作品
            this.$router.push({ path: "/frontPage/detail", query: { activityId: this.$route.query.activityId, type: this.$route.query.type, status: this.$route.query.status } })
          }
        }
      })
    },
    //上传作品
    handleSubmit(name) {
      if (this.isSign) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            if (this.checkbox.length == 1) {
              if ((sessionStorage.fileName&&sessionStorage.uploadedSize>0) || this.uploadStatus == 1) {
               if (sessionStorage.isCanDel == 1) { //isCanDel == 1是已暂停状态
                  this.submitDatas()
                } else {
                  message.showMessage('warning', '请先暂停上传文件');
                }
              } else {
                this.$Message.error({
                  content:'请上传文件',
                  duration:3
                });
              }
            } else {
              this.$Message.error({
                content:'您还没看用户同意书呢',
                duration:3
              });
            }
          }
        })
      } else {
        this.$Modal.confirm({
          title: '',
          content: '<h3>您还没登录，现在就去登录</h3>',
          onOk: () => {
            login(this.$route.fullPath)
          },
          onCancel: () => {
            this.$Message.info({
              content:'取消了登录',
              duration:3
            });
          }
        });
      }
    },
    submitDatas() {
      this.$http.post("/v1/frontend/works/add-work", {
          activityId: this.activityId,
          workName: this.formItem.workName,
          username: this.formItem.username,
          userId: sessionStorage.userId,
          type: this.formItem.type,
          tel: this.formItem.tel,
          departments: this.formItem.departments,
          studentId: this.formItem.studentId,
          division:this.formItem.division,
          file: sessionStorage.fileName ? sessionStorage.fileName : this.fileInfo.clientFileName,
          lastModifiedTime:sessionStorage.lastModifiedTime ? sessionStorage.lastModifiedTime:this.fileInfo.lastModifiedTime
        })
        .then((res) => {
          if (res.data.status == 200) {
            if (res.data.data == 1) {
              this.$router.push({ path: "/frontPage/detail", query: { activityId: this.$route.query.activityId, type: this.$route.query.type, status: this.$route.query.status } })
            } else {
              this.getProductionDetail()
            }
            this.$Message.success({
              content:res.data.message,
              duration:3
            });
          } else if (res.data.status == 1005) {
            this.$Message.error({
              content:'由于网速太慢，上传的作品不完整，请删除作品，重新上传，建议网络好的情况下再上传',
              duration:3
            });
          } else {
            this.$Message.error({
              content:res.data.message,
              duration:3,
            });
          }
        })
    },
    creatHupload() {
      var _this = this;
      sessionStorage.isCanDel=1;
      sessionStorage.againSlecte = 2;
      let up = $('#upload').Huploadify({
        auto: false,
        fileTypeExts: '*.*',
        multi: false,
        formData: {},
        fileSizeLimit:800*1024*1024,
        showUploadedPercent: true,
        showUploadedSize: true,
        removeTimeout: 9999999,
        uploader: baseUrl + '/v1/frontend/works/work-file-upload',
        breakPoints: true,
        fileSplitSize: 1024 * 512, //断点续传的文件块大小，单位Byte，默认1M
        getUploadedSize: function(file) {
          var _size = 0;
          $.ajax({
            url: baseUrl + '/v1/frontend/works/file-progress',
            type: 'GET',
            async: false,
            data: {
              activityId: location.href.split('activityId=')[1].split('&')[0],
              userId: sessionStorage.userId,
              clientName: file.name,
              totalSize: file.size,
              lastModifiedTime: file.lastModifiedDate.getTime()
            },
            success: function(res) {
              if (res.status == 1006) { //该账号该活动下，已经上传完成的作品了
                _size = file.size
              } else {
                _size = res.data ? res.data : 0;
                 sessionStorage.uploadedSize =  _size  //储存已上传大小
              }
            }
          })
          return _size;
          // var size = parseInt(localStorage.getItem(file.name)) || 0;
          // return size;

        }, //类型：functio自定义获取已上传文件的大小函数，用于开启断点续传模式，可传入一个参数file，即当前上传的文件对象，需返回number类型
        saveUploadedSize: function(file, value) {
          if(_this.timer2){
            clearTimeout(_this.timer2)
          }
          console.log('sessionStorage.uploadedSize = ' + sessionStorage.uploadedSize)
          sessionStorage.uploadedSize = value;
          _this.timer2 = setTimeout(function() {
            alert('当前网络状态不稳定哟');
          }, 1000 * 60)
          // localStorage.setItem(file.name, value);
          $.ajax({
            url: baseUrl + '/v1/frontend/works/save-file-progress',
            type: 'post',
            data: {
              activityId: location.href.split('activityId=')[1].split('&')[0],
              userId: sessionStorage.userId,
              clientName: file.name,
              totalSize: file.size,
              uploadSize: value,
              lastModifiedTime: file.lastModifiedDate.getTime()
            },
            success: function(res) {

            }
          })
        }, //类型：function，自定义保存已上传文件的大小函数，用于开启断点续传模式，可传入两个参数：file：当前上传的文件对象，value：已上传文件的大小，单位Byte
        onUploadStart: function(file) {
          console.log(file.name + '开始上传');
        },
        onInit: function(obj) {
          console.log('初始化');
          console.log(obj);
        },
        onUploadSuccess: function(file) {
          console.log(file.name + '上传成功');
          sessionStorage.isCanDel = 1;
           clearTimeout(_this.timer2);
          message.showMessage('success', '上传成功');
        },
        onUploadComplete: function(file) {
          message.showMessage('success', '已经上传完成');
          _this.timer2 = null;
          clearTimeout(_this.timer2);
          sessionStorage.isCanDel = 1;
          console.log(file.name + '上传完成');
        },
        onCancel: function(file) {
          if (sessionStorage.uploadedSize > 0) {
            $.ajax({
              url: baseUrl + "/v1/frontend/works/remove-file",
              type: 'post',
              data: {
                activityId: location.href.split('activityId=')[1].split('&')[0],
                userId: sessionStorage.userId,
                totalSize: file.size,
                lastModifiedTime: file.lastModifiedDate.getTime()? file.lastModifiedDate.getTime():this.fileInfo.lastModifiedTime
              },
              success: function(data) {
                if (data.status == 200) {
                  sessionStorage.removeItem('fileName')
                  sessionStorage.removeItem('uploadedSize')
                  _this.$Message.success({
                    content: file.name + '删除成功',
                    duration: 3
                  });
                  sessionStorage.againSlecte = 2 //能重新选择文件
                } else {
                  _this.$Message.warning({
                    content: '请刷新页面重试',
                    duration: 3
                  });
                }
              }
            })
          } else {
           
          }
        },
        onClearQueue: function(queueItemCount) {
          console.log('有' + queueItemCount + '个文件被删除了');
        },
        onDestroy: function() {
          console.log('destroyed!');
        },
        onSelect: function(file) {
          console.log(file)
          this.formData = {
            totalSize: file.size,
            activityId: location.href.split('activityId=')[1].split('&')[0],
            userId: sessionStorage.userId,
            lastModifiedTime:file.lastModifiedDate.getTime()
          }
          sessionStorage.fileName = file.name;
          sessionStorage.lastModifiedTime = file.lastModifiedDate.getTime();
          sessionStorage.isCanDel = 1 //一旦选择了文件就能操作删除
          sessionStorage.againSlecte = 2 //能重新选择文件
          console.log(file.name + '加入上传队列');
        },
        onQueueComplete: function(queueData) {
          console.log('队列中的文件全部上传完成', queueData);
        }
      })
      // -------------- 暂停文件 ------------
      $('#btn1').click(function() {
        if (sessionStorage.fileName) {
          if(sessionStorage.isCanDel==1){
            message.showMessage('warning', '目前已是暂停状态');
          }else{
            setTimeout(function(){   //停止后还会再发送一个saveUploadedSize函数，所以延后5秒执行
              clearTimeout(_this.timer2)
            },5000)   
            sessionStorage.isCanDel = 1;  //暂停了能操作删除过程了
            sessionStorage.againSlecte = 2 //能重新选择文件
            up.stop();
            message.showMessage('success', '暂停上传');
          }
        } else {
          message.showMessage('warning', '请先上传文件');
        }
      });
       // -------------- 上传文件 ------------
      $('#btn2').click(function() {
         if (sessionStorage.fileName) {
          message.showMessage('success', '开始上传文件');
          up.upload('*');
          sessionStorage.isCanDel=0; //上传过程中不能操作删除功能
          sessionStorage.againSlecte = 1 //不能重新选择文件
        } else {
          message.showMessage('warning', '请选择文件');
        }
      });
      // -------------- 删除操作 ------------
      $('#btn3').click(function() {   
        if (sessionStorage.fileName) {  //有文件情况下
          if (sessionStorage.isCanDel == 1){  //能刪除状态
            if (sessionStorage.uploadedSize >0 ){    //上传大小不为0，必须通过ajax删除
              message.showMessage("info", "正在删除中，请稍等...")
              setTimeout(function() {   //避免删除时，上传的ajax还没回来，延后5秒执行
                up.cancel('*');
                sessionStorage.againSlecte = 2 //能重新选择文件
                clearTimeout(_this.timer2)
              },5000)
            }else{  //上传大小为0，直接操作节点删除
              message.showMessage("info", "删除中...")
              up.cancel('*');
              sessionStorage.againSlecte = 2 //能重新选择文件
            }
          }else{
             message.showMessage("warning", "请先暂停上传文件");
          }
        } else {
          message.showMessage('warning', '请先上传文件');
        }
      });
    },
    retuenDetail() {
      this.$router.push({ path: "/frontPage/detail", query: { activityId: this.$route.query.activityId, type: this.$route.query.type, status: this.$route.query.status } })
    },
    //删除已上传完整的文件
    delLoadedFile() {
      this.$http.post("v1/frontend/works/remove-file", {
          activityId: this.fileInfo.activityId,
          userId: this.fileInfo.userId,
          totalSize: this.fileInfo.totalSize,
          lastModifiedTime:this.fileInfo.lastModifiedTime
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.$Message.success({
              content:res.data.message,
              duration:3
            });
            var _this = this
            setTimeout(function() {
              _this.getProductionDetail()
            }, 1200)
          } else {
            this.$Message.warning({
              content:'请刷新页面重试',
              duration:3,
            });
          }
        })
        .catch((err) => {
          console.log(err)
        })

    }
  }
}

</script>
<style lang='less'>
.apply {
  width: 100%;

  .big-title {
    width: 1200px;
    margin: 0 auto;
    line-height: 128px;
    text-align: center;
    font-size: 30px;
    color: #222222;
  }
  .return-detail {
    float: left;
    font-size: 18px;
    font-weight: 400;
    cursor: pointer;
    color: #333;
  }

  .return-detail:hover {
    color: #0d96ca;
  }

  .ivu-form {
    width: 1200px;
    margin: 0 auto;
    border: 2px solid #313131;
    margin-bottom: 67px;
    overflow: hidden;
    position: relative;
  }

  .school {
    line-height: 117px;
    text-align: center;
    font-size: 20px;
    margin-top: 5px;
    color: #333;
  }
  .ivu-form-item {
    width: 80%;
    margin: 0 10%;
    height: 40px;
    margin-bottom: 22px;
  }

  .ivu-form-item-content {
    margin-right: 80px;
  }
  .ivu-row .ivu-form-item-content {
    margin-right: 0;
  }
  .ivu-select-selection,
  .ivu-input {
    height: 40px;
    line-height: 40px;
  }

  .ivu-select .ivu-select-placeholder,.ivu-select-input {
    height: 40px;
    line-height: 40px;
    font-size: 14px;
  }
  .ivu-select .ivu-select-selected-value {
    height: 40px;
    line-height: 40px;
    font-size: 15px;
  }

  .input-left {
    width: 95%;
    margin-left: 60px;
  }
  .item-left .ivu-form-item-error-tip {
    left: 16%;
  }
  .input-right {
    width: 95%;
    margin-left: -120px;
  }
  .item-right .ivu-form-item-error-tip {
    left: -29%;
  }

  .labels>span:nth-child(2) {
    color: red;
  }

  .up-load {
    padding: 10px 33px;
    background-color: #000;
    border-radius: 0;
    margin-left: 23px;
    color: white;
    border: none;
    outline: none;
  }

  .ivu-btn-primary {
    width: 798px;
    height: 40px;
    line-height: 28px;
    background-color: #f63051;
    border-color: #f63051;
    margin-bottom: 60px;
    font-size: 15px;
    overflow: hidden;
  }
  .userAgree {
    display: inline-block;
    width: 100px;
    height: 20px;
    position: absolute;
    left: 325px;
    bottom: 98px;
    z-index: 102;
    cursor: pointer;
  }

  .join-active {
    background-color: #f2f2f2;
  }
  .load-wrap {
    width: 797px;
    margin-left: 200px;
    overflow: hidden;
    margin-bottom: 20px;

    button {
      padding: 5px 15px;
      border: 1px solid #666;
      border-radius: 10px;
      cursor: pointer;
      margin-right: 10px;
      background-color: #fff;
      outline: none;
    }
    button:hover {
      color: #2d8cf0;
      border: 1px solid #2d8cf0;
    }
  }

  .show-load {
    width: 797px;
    margin-left: 200px;
    overflow: hidden;
    margin-bottom: 20px;
    font-size: 14px;

    .file-names {
      font-weight: 700;
    }

    .del-loaded-file {
      display: inline-block;
      border: 1px solid #999;
      line-height: 24px;
      border-radius: 4px;
      padding: 3px 18px;
      font-size: 12px;
      color: #666;
      text-decoration: none;
      background-color: #fff;
      cursor: pointer;
      margin-left: 30px;
    }

    .del-loaded-file:hover {
      color: #2d8cf0;
      border-color: #2d8cf0;
    }
  }

  .uploadify-button,
  #file_upload_1-button {
    display: inline-block;
    background-color: #000;
    line-height: 38px;
    padding: 0 35px;
    font-size: 14px;
    font-weight: 600;
    font-family: '微软雅黑';
    color: #FFF;
    cursor: pointer;
    text-decoration: none
  }
}

</style>
