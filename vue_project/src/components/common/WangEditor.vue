<template>
  <div>
    <div class="wang-editor-main" ref="editorContainer" @input="outputContent"></div>
  </div>
</template>
<script>
import WangEditor from "wangeditor"
export default {
  data() {
    return {
      baseUrl: baseUrl,
    }
  },
  mounted() {
    this.createEditor()
  },
  props: [
    'inputContent',
    'imageuploadUrl'
  ],
  methods: {
    createEditor() {
      const self = this;
      const editor = new WangEditor(this.$refs.editorContainer);
      editor.config.menus = ['source', '|', 'bold', 'underline', 'italic', 'strikethrough', 'eraser', 'forecolor', 'bgcolor', '|', 'fontfamily', 'fontsize', '|', 'head', 'unorderlist', 'orderlist', '|', 'link', 'unlink', 'table', 'img', 'alignleft', 'aligncenter', 'alignright',
        '|', 'undo', 'redo'
      ]

      editor.config.uploadImgUrl = baseUrl + this.imageuploadUrl;

      editor.config.uploadImgFns.onload = function(resultText, xhr) {
        // resultText 服务器端返回的text
        // xhr 是 xmlHttpRequest 对象，IE8、9中不支持

        // 上传图片时，已经将图片的名字存在 editor.uploadImgOriginalName
        var originalName = editor.uploadImgOriginalName || '';

        // 如果 resultText 是图片的url地址，可以这样插入图片：
        editor.command(null, 'insertHtml', '<img src="' + resultText + '" alt="' + originalName + '" style="max-width:100%;"/>');
        // 如果不想要 img 的 max-width 样式，也可以这样插入：
        // editor.command(null, 'InsertImage', resultText);
      };

      editor.onchange = function() {
        // self.formatContent = this.$txt.text();
        self.inputContent = this.$txt.text()
      }
      // editor.config.hideLinkImg = true;
      editor.create();
      editor.$txt.html(this.inputContent);

    },
    formatContent(content) {
      this.inputContent = content
      this.outputContent()
    },
    outputContent() {
      this.$emit('input', this.inputContent)
    }
  },
  components: {}
}

</script>
<style lang="css">
.wang-editor-main {
  min-height: 400px;
  max-height: 600px;
}

.wangEditor-container {
  border-radius: 2px;
  overflow: hidden;
  border: 1px solid #CCC;
}

</style>
