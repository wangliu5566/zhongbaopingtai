<template>
  <div class="news-edit-common">
    <Row>
      <Button type="primary" icon="chevron-left" @click="backList">返回列表</Button>
    </Row>
    <Row class="pt20">
      <div class="form-container">
        <Form ref="newsAddFromHeader" :model="newsDatas" :label-width="100" :rules="newNewsRules">
          <Form-item label="新闻标题：" prop="title">
            <Input type="text" v-model="newsDatas.title" placeholder="请输入新闻标题"></Input>
          </Form-item>
          <Form-item label="所属活动：" prop="activityId">
            <Select v-model="newsDatas.activityId" placeholder="请选择所属活动" style="width:50%;">
              <Option v-show="eventList.length" v-for="item in eventList" :value="item.id" :key="item.id+item.name">{{ item.name }}</Option>
              <Option v-show="!eventList.length" disabled value="0">暂无活动</Option>
            </Select>
          </Form-item>
          <Form-item label="新闻概要：" prop="newsSummary">
            <Input type="textarea" v-model="newsDatas.newsSummary" placeholder="请输入新闻概要" :autosize="{minRows: 4,maxRows: 6}"></Input>
          </Form-item>
          <Form-item label="封面：" required>
            <!-- <Upload type="drag" style="width:50%;" multiple name="file" :action="baseUrl+'/v1/admin/image/save-news-cover-image'" :on-success="coverImageUploadSuccess" :before-upload="coverImageBeforeUpload" :on-error="uploadOnError" ref="coverImageUpload" :format="['jpg','jpeg','png']" :on-format-error="coverImageHandleFormatError">
              <div style="padding: 20px 0">
                <Icon type="plus-round" size="40"></Icon>
                <p>点击选择文件上传</p>
              </div>
            </Upload> -->
            <vue-core-image-upload inputOfFile="file" class="cover-upload-btn" crop="local" @imageuploaded="coverImageUploadSuccess" :max-file-size="10485760" :url="baseUrl+'/v1/admin/image/save-news-cover-image'" extensions="jpg,png,jpeg,gif" :maxWidth="1200" :maxHeight="632" inputAccept="'image/*' / " cropRatio="150:79" :cropBtn="{ok:'上传图片','cancel':'返回'}" :compress="0" text="裁剪上传封面图" @errorhandle="uploadError">
            </vue-core-image-upload>
            <div>
              <span><span style="color:#57a3f3;">新闻封面</span> 建议上传 <span style="color:red;">1200 &times; 632</span> 尺寸的图片，格式为<span style="color:#57a3f3;">【jpg、jpeg、png】</span></span>
            </div>
            <Form-item prop="headImage">
              <div class="cover-preview">
                <img v-if="newsDatas.headImage" :src="baseUrl+newsDatas.headImage" alt="新闻封面">
                <div v-else class="without-news-cover">
                  暂无新闻封面图
                </div>
                <Tooltip content="点击删除" placement="bottom-end" v-if="newsDatas.headImage">
                  <Button type="error" class="delete-image-btn" @click="deleteHeadImage">
                    <Icon type="android-cancel" size="16" />
                  </Button>
                </Tooltip>
              </div>
            </Form-item>
          </Form-item>
          <Form-item label="正文：" prop="mainBody" required>
            <div class="wang-editor-main" ref="editorContainer"></div>
            <!-- <VueUeditor @ready="editorReady" :ueditorConfig="editorConfig"></VueUeditor> -->
          </Form-item>
          <Form-item class="tr">
            <Button type="ghost" :disabled="isAdd" @click="handleSave('newsAddFromHeader')">{{$route.fullPath.indexOf('/add')!=-1?'保存':'修改并保存'}}</Button>
            <Button type="primary" :disabled="isAdd" @click="handlePublish('newsAddFromHeader')">{{$route.fullPath.indexOf('/add')!=-1?'保存并发布':'修改并发布'}}</Button>
          </Form-item>
        </Form>
      </div>
    </Row>
  </div>
</template>
<script>
// import VueUeditor from "vue-ueditor"
import VueCoreImageUpload from 'vue-core-image-upload'
import WangEditor from "wangeditor"
export default {
  data() {
    const checkMainBody = (rule, value, callback) => {
      if (this.mainBodyFormat === '' && this.newsDatas.mainBody.indexOf('img') == "-1") {
        callback(new Error('请填写新闻正文'))
      } else {
        callback();
      }
      // if (!this.hasContent) {
      //   callback(new Error('请填写新闻正文'))
      // } else {
      //   callback();
      // }
    }
    return {
      baseUrl: baseUrl,
      imageUploadUrl: '/v1/admin/image/save-news-main-image',
      isAdd: false,

      editorConfig: {
        toolbars: [
          [
            'fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'touppercase', 'tolowercase',
            'link', 'unlink', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'insertcode', 'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', , '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|',
            'print', 'preview', 'searchreplace', 'help'
          ]
        ],
        autoHeightEnabled: false,
        initialFrameWidth: null,
        initialFrameHeight: 400,
        serverUrl: baseUrl + '/php/controller.php',
        enableAutoSave: false,
        allowDivTransToP: false
      },

      //富文本
      mainBodyFormat: '',
      //editor实例
      editor: '',

      //判定富文本是否有数据
      hasContent: false,

      newNewsRules: {
        title: [{
          required: true,
          message: '请填写新闻标题',
          trigger: 'blur'
        }, {
          min: 2,
          max: 30,
          message: '新闻标题长度控制在2-30字以内',
          trigger: 'blur'
        }],
        // activityId: [{
        //   required: true,
        //   message: '请选择所属活动',
        //   trigger: 'change'
        // }],
        newsSummary: [{
          required: true,
          message: '请填写新闻概要',
          trigger: 'blur'
        }, {
          min: 1,
          max: 140,
          message: '新闻概要长度控制在140字以内',
          trigger: 'blur'
        }],
        headImage: [{
          required: true,
          message: '请上传新闻封面',
        }],
        mainBody: [{
          validator: checkMainBody,
          trigger: 'blur'
        }],
      },
      eventList: [],

      newsDatas: {
        title: '',
        username: '',
        userId: '',
        activityId: '',
        newsSummary: '',
        coverPath: '',
        mainBody: '',
        headImage: '',
      },
    }
  },
  props: [

  ],
  components: {
    'vue-core-image-upload': VueCoreImageUpload,
    // VueUeditor,
    WangEditor
  },
  methods: {
    backList() {
      this.$router.push({
        path: '/wrap/content/news',
        query: {

        }
      })
    },
    //保存
    handleSave(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if (this.$route.fullPath.indexOf('/add') != -1) {
            this.addNewsData('2');
          } else if (this.$route.fullPath.indexOf('/edit') != -1) {
            this.editNewsData('2');
          }
        }
      })
    },
    //发布
    handlePublish(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if (this.$route.fullPath.indexOf('/add') != -1) {
            this.addNewsData('1');
          } else if (this.$route.fullPath.indexOf('/edit') != -1) {
            this.editNewsData('1');
          }

        }
      })
    },

    //新增新闻接口
    addNewsData(status) {
      this.$http.post('/v1/admin/news/news-add',
        Object.assign({}, this.newsDatas, {
          status: status,
          username: JSON.parse(window.sessionStorage.getItem('bg_user_info')).username,
          userId: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id
        })
      ).then((res) => {
        if (res.data.status == 200) {
          this.isAdd = true;
          this.updateFilter({
            cp: 1,
            searchKey: '',
            event: '0'
          });
          if (status == '1') {
            this.$Message.success('保存并发布成功')
          } else if (status == '2') {
            this.$Message.success('保存成功')
          }
          this.$router.push('/wrap/content/news');
        }

      })
    },

    //修改新闻
    editNewsData(status) {
      this.$http.post('/v1/admin/news/modify-news',
        Object.assign({}, this.newsDatas, {
          status: status,
          newsId: this.$route.query.id
        })
      ).then((res) => {
        if (res.data.status == 200) {
          if (status == 1) {
            this.$Message.success({
              content: '修改并发布成功'
            })
          } else if (status == 2) {
            this.$Message.success({
              content: '修改并保存成功'
            })
          }
          this.$router.push('/wrap/content/news')
        }
      })
    },

    //封面上传
    coverImageUploadSuccess(res, file, fileList) {
      let _this = this;
      if (res.status == 200) {
        this.newsDatas.coverPath = res.data.coverPath;
        this.newsDatas.headImage = res.data.headImage;
      }
    },

    deleteHeadImage() {
      this.newsDatas.coverPath = '';
      this.newsDatas.headImage = '';
    },

    coverImageBeforeUpload(file) {
      // this.$Modal.confirm({
      //   title: '上传提示',
      //   content: '<p>新闻封面建议上传 1200 &times; 630 尺寸的图片</p><p>这将使页面显示更为正常</p>',
      //   onOk: () => {
      //     return true;
      //   },
      //   onCancel: () => {
      //     return false;
      //   }
      // });
    },

    coverImageHandleFormatError(file) {
      this.$Notice.error({
        title: '文件格式不正确',
        desc: '文件 ' + file.name + ' 格式不正确，请上传 jpg 或 png 格式的图片。'
      });
    },

    uploadError(value) {
      if (value.indexOf('ERROR') != -1) {
        this.$Message.warning({
          content: '只能上传jpg,png,jpeg,gif格式的图片',
          duration: 3
        })
      } else if (value.indexOf('LARGER') != -1) {
        this.$Message.warning({
          content: '选择的图片不得大于10M',
          duration: 3
        })
      }
    },

    //banner上传失败
    uploadOnError(error, file, fileList) {
      if (error) {
        this.$Message.error({
          content: '网络错误，文件上传失败，请稍后再试！',
          duration: 10
        })
      }
    },

    //获取所有活动
    getActivityList() {
      this.$http.get('/v1/admin/activity/get-all-activity', {
        params: {
          userId: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id,
          // auditState: 
        }
      }).then((res) => {
        if (res.data.data) {
          res.data.data.forEach(function(item, index) {
            item.id = item.id.toString();
          });
        }
        this.eventList = res.data.data;
      })
    },


    getNewsDetail() {
      let newsId = this.$route.query.id;
      if (newsId) {
        this.$http.get('/v1/admin/news/get-news-by-id', {
          params: {
            newsId: newsId,
          }
        }).then((res) => {
          this.newsDatas = Object.assign({}, this.newsDatas, res.data.data);

          this.editor.$txt.html(this.newsDatas.mainBody);
          this.mainBodyFormat = this.newsDatas.mainBody;
        })
      }
    },

    //富文本实例化后
    editorReady(editorInstance) {
      if (this.$route.fullPath.indexOf('/edit') != -1) {
        let _this = this;
        let timer = window.setInterval(function() {
          editorInstance.setContent(_this.newsDatas.mainBody);
          if (_this.newsDatas.mainBody != '') {
            window.clearInterval(timer);
          }
        }, 100)
      } else {
        editorInstance.setContent(this.newsDatas.mainBody);
      }
      editorInstance.addListener('contentChange', () => {
        this.hasContent = editorInstance.hasContents();
        this.newsDatas.mainBody = editorInstance.getContent();
      });
    },

    createEditor() {
      const _this = this;
      this.editor = new WangEditor(this.$refs.editorContainer);

      // 定义菜单栏
      this.editor.config.menus = ['source', '|', 'bold', 'underline', 'italic', 'strikethrough', 'eraser', 'forecolor', 'bgcolor', '|', 'fontfamily', 'fontsize', '|', 'head', 'unorderlist', 'orderlist', 'lineheight', 'indent', '|', 'quote', 'link', 'unlink', 'table', 'img', 'emotion', 'alignleft', 'aligncenter', 'alignright',
        '|', 'undo', 'redo'
      ]

      //图片上传地址
      this.editor.config.uploadImgUrl = baseUrl + this.imageUploadUrl;

      this.editor.config.withCredentials = true;
      //图片上传时文件名
      this.editor.config.uploadImgFileName = "file";

      // 阻止输出log
      wangEditor.config.printLog = false;

      //粘贴复制过滤
      this.editor.config.pasteText = true;

      // 关闭js过滤
      this.editor.config.jsFilter = false;

      this.editor.config.uploadImgFns.onload = function(data) {
        let newsData = JSON.parse(data);
        if (newsData.status == 200) {
          _this.editor.command(null, 'insertHtml', '<img src="' + baseUrl + newsData.data + '" alt="新闻插图" style="max-width:100%;"/>');
        }
        // 上传图片时，已经将图片的名字存在 this.editor.uploadImgOriginalName
        // var originalName = this.editor.uploadImgOriginalName || '';
        // // 如果 resultText 是图片的url地址，可以这样插入图片：

        // 如果不想要 img 的 max-width 样式，也可以这样插入：
        // this.editor.command(null, 'InsertImage', resultText);
      };

      // 自定义timeout事件
      this.editor.config.uploadImgFns.ontimeout = function(xhr) {
        // xhr 是 xmlHttpRequest 对象，IE8、9中不支持
        _this.$Message.error('图片上传超时')
      };

      // 自定义error事件
      this.editor.config.uploadImgFns.onerror = function(xhr) {
        // xhr 是 xmlHttpRequest 对象，IE8、9中不支持
        _this.$Message.error('网络错误，图片上传失败')
      };

      this.editor.onchange = function() {
        _this.newsDatas.mainBody = _this.editor.$txt.html();
        _this.mainBodyFormat = _this.editor.$txt.formatText();
      }

      // this.editor.config.hideLinkImg = true;
      // 
      this.editor.create();

    },

  },
  created() {
    this.getActivityList();
    if (this.$route.fullPath.indexOf('/edit') != -1) {
      this.getNewsDetail();
    }
  },
  mounted() {
    this.createEditor();
  }

}

</script>
<style lang="less">
.news-edit-common {
  .form-container {
    width: 700px;
  }
  .edui-default .edui-editor-toolbarbox {
    line-height: normal;
  }
  .ivu-tooltip {
    position: absolute;
    right: 5px;
    top: 2px;
  }
  .delete-image-btn {
    cursor: pointer;
    z-index: 100;
    padding: 5px;
  }
  .wangEditor-container {
    border-radius: 4px;
    border-color: #dddee1;
  }
  .wangEditor-menu-container {
    border-radius: 4px 4px 0 0;
  }
  .cover-upload-btn {
    width: 140px;
    height: 30px;
    background-color: #2d8cf0;
    border-color: #2d8cf0;
    color: #fff;
    border-radius: 4px;
    text-align: center;
  }
  .cover-preview {
    height: 315px;
    width: 100%;
    border: 1px solid #dddee1;
    border-radius: 4px;
    overflow: hidden;
    position: relative;

    img {
      width: 100%;
      height: auto;
    }
  }
  .ivu-upload-drag {
    border-style: solid;
  }
  .wang-editor-main {
    height: 500px;
  }
  .without-news-cover {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    line-height: 300px;
    font-size: 16px;
    text-align: center;
    color: #bbb;
  }
}

</style>
