<template>
  <div class="activity-edit-container">
    <Row>
      <Button type="primary" icon="chevron-left" @click="gobackList">返回列表</Button>
    </Row>
    <Row class="pt20">
      <div class="activity-edit-main">
        <Alert type="error" closable v-if="whyRefusalRecode.reviewDate&&whyRefusalRecode.username">
          <div slot="desc">
            <p style="font-size: 14px;">审核未通过原因：{{whyRefusalRecode.reason}}</p>
          </div>
        </Alert>
        <Form :label-width="120">
          <Form-item label="活动类型：" required>
            <Radio-group v-model="activityDatas.type" @on-change="activityTypeChange">
              <Radio label="1" :disabled="isEdit">大赛</Radio>
              <Radio label="2" :disabled="isEdit">问卷</Radio>
            </Radio-group>
          </Form-item>
        </Form>
        <Form :model="activityDatas" :label-width="120" ref="activityEditForm" :rules="activityEditFormRules">
          <Form-item label="新建问卷" v-if="activityDatas.type==2">
            <Button type="primary" @click="addNewQuestionnaire">新建问卷</Button>
          </Form-item>
          <Form-item label="参赛机构" prop="organizationNameList" required>
            <Input v-model="organizationNameList" placeholder="选择参赛机构" readonly>
            <Select v-model="activityDatas.allowJoin" slot="prepend" style="width: 120px">
              <Option value="0">选择机构参加</Option>
              <Option value="1">所有机构参加</Option>
              <Option value="2">所有人参加</Option>
            </Select>
            <Button slot="append" style="padding-left: 5px;padding-right: 5px;" :disabled="activityDatas.allowJoin!=0" @click="selectOrganizationNameList">{{organizationNameList==''?'选择参赛机构':'重新选择机构'}}</Button>
            </Input>
          </Form-item>
          <Form-item :label="activityDatas.type==1?'大赛名称：':'活动名称：'" prop="name">
            <Input v-model="activityDatas.name" :placeholder="activityDatas.type==1?'填写大赛名称':'填写活动名称'"></Input>
          </Form-item>
          <Form-item :label="activityDatas.type==1?'大赛轮播图：':'活动轮播图：'" required>
            <!-- <Upload multiple type="drag" name="file" :action="baseUrl+'/v1/admin/image/save-activity-banner-image'" :on-success="bannerUploadSuccess" :before-upload="bannerBeforeUpload" :on-error="uploadOnError" ref="bannerUpload" :format="['jpg','jpeg','png']" :on-format-error="handleFormatError">
              <div style="padding: 20px 0">
                <Icon type="plus-round" size="40"></Icon>
                <p>点击选择文件上传</p>
              </div>
            </Upload> -->
            <vue-core-image-upload inputOfFile="file" class="banner-upload-btn" crop="local" @imageuploaded="bannerUploadSuccess" :max-file-size="10485760" :url="baseUrl+'/v1/admin/image/save-activity-banner-image'" extensions="jpg,png,jpeg,gif" :maxWidth="1920" :maxHeight="500" inputAccept="'image/*' / " cropRatio="96:25" :cropBtn="{ok:'上传图片','cancel':'返回'}" :compress="0" text="裁剪上传轮播图" @errorhandle="uploadError">
            </vue-core-image-upload>
            <div class="without-banner-container">
              <Form-item prop="pcImage">
                <img v-if="activityDatas.pcImage" :src="baseUrl+activityDatas.pcImage" alt="大赛轮播图">
                <div class="without-banner-main" v-else>
                  暂无{{activityDatas.type==1?'大赛':'活动'}}轮播图
                </div>
                <Tooltip content="点击删除" placement="bottom-end" v-if="activityDatas.pcImage">
                  <Button type="error" class="delete-image-btn" @click="deletePcImage">
                    <Icon type="android-cancel" size="16" />
                  </Button>
                </Tooltip>
                <!-- <Icon type="close-circled"></Icon> -->
              </Form-item>
            </div>
            <div>
              <span><span style="color:#57a3f3;">轮播图</span> 建议上传 <span style="color:red;">1920 &times; 500</span> 尺寸的图片，格式为<span style="color:#57a3f3;">【jpg、jpeg、png】</span></span>
            </div>
          </Form-item>
          <Form-item :label="activityDatas.type==1?'大赛封面图：':'活动封面图：'" required>
            <vue-core-image-upload inputOfFile="file" class="banner-upload-btn" crop="local" @imageuploaded="smallImageUploadSuccess" :max-file-size="10485760" :url="baseUrl+'/v1/admin/image/save-activity-small-image'" extensions="jpg,png,jpeg,gif" :maxWidth="264" :maxHeight="190" inputAccept="'image/*' / " cropRatio="264:190" :cropBtn="{ok:'上传图片','cancel':'返回'}" :compress="0" text="裁剪上传封面图" @errorhandle="uploadError">
            </vue-core-image-upload>
            <Row>
              <!-- <Col span="11">
              <Upload multiple type="drag" name="file" :action="baseUrl+'/v1/admin/image/save-activity-small-image'" :on-success="smallImageUploadSuccess" :before-upload="smallImageBeforeUpload" :on-error="uploadOnError" ref="smallImageUpload" :format="['jpg','jpeg','png']" :on-format-error="handleFormatError">
                <div style="padding: 64px 0">
                  <Icon type="plus-round" size="40"></Icon>
                  <p>点击选择文件上传</p>
                </div>
              </Upload>
              </Col> -->
              <Col span="12">
              <div class="without-cover-container">
                <Form-item prop='smallImage'>
                  <img v-if="activityDatas.bigImage" :src="baseUrl+activityDatas.bigImage" alt="大赛封面图">
                  <div v-else class="without-cover-main">
                    暂无{{activityDatas.type==1?'大赛':'活动'}}封面图
                  </div>
                  <Tooltip content="点击删除" placement="bottom-end" v-if="activityDatas.bigImage">
                    <Button type="error" class="delete-image-btn" @click="deleteSmallImage">
                      <Icon type="android-cancel" size="16" />
                    </Button>
                  </Tooltip>
                </Form-item>
              </div>
              </Col>
            </Row>
            <div>
              <span><span style="color:#57a3f3;">封面图</span> 建议上传 <span style="color:red;">264 &times; 190</span> 尺寸的图片，格式为<span style="color:#57a3f3;">【jpg、jpeg、png】</span></span>
            </div>
          </Form-item>
          <Form-item :label="activityDatas.type==1?'大赛简介：':'活动简介：'" prop="intro">
            <Input v-model="activityDatas.intro" type="textarea" :autosize="{minRows: 6,maxRows: 999}" :placeholder="activityDatas.type==1?'填写大赛简介':'填写活动简介'"></Input>
          </Form-item>
          <Form-item label="简介配图：" v-if="activityDatas.type==1" required>
            <vue-core-image-upload inputOfFile="file" class="banner-upload-btn" crop="local" @imageuploaded="introImageUploadSuccess" :max-file-size="10485760" :url="baseUrl+'/v1/admin/image/save-activity-intro-image'" extensions="jpg,png,jpeg,gif" :maxWidth="558" :maxHeight="368" inputAccept="'image/*' / " cropRatio="279:184" :cropBtn="{ok:'上传图片','cancel':'返回'}" :compress="0" text="裁剪上传简介配图" @errorhandle="uploadError">
            </vue-core-image-upload>
            <Row style="margin-left: -4px;">
              <!-- <Col span="11" style="margin-right: -4px;padding-right: 2px;">
              <Upload multiple type="drag" name="file" :action="baseUrl+'/v1/admin/image/save-activity-intro-image'" :on-success="introImageUploadSuccess" :before-upload="introImageBeforeUpload" :on-error="uploadOnError" ref="introImageUpload" :format="['jpg','jpeg','png']" :on-format-error="handleFormatError">
                <div style="padding:39px 0;">
                  <Icon type="plus-round" size="30"></Icon>
                  <p>点击选择文件上传</p>
                </div>
              </Upload>
              </Col> -->
              <Col span='8' v-if="+activityDatas.introImage.length==0" class="intro-cover1 without-intro-container">
              <Form-item prop="introImage">
                <div class="without-intro-mian">
                  暂无简介配图
                </div>
              </Form-item>
              </Col>
            </Row>
            <Row style="margin-left: -4px;">
              <Col span='8' class="intro-cover" style="border-radius: 4px;border:1px solid #dddee1;margin-left: 4px;" v-if="+activityDatas.introImage.length!=0" v-for="(item,index) in activityDatas.introImage" :key="item.introImage">
              <img :src="baseUrl+item.introImage" alt="简介配图">
              <div class="tr intro-image-handle" v-if="item.introImage">
                <Tooltip content="点击前移" placement="bottom-end">
                  <Button type="error" class="delete-image-btn" @click="arrowLeft(index)">
                    <Icon type="arrow-left-c" size="16" />
                  </Button>
                </Tooltip>
                <Tooltip content="点击后移" placement="bottom-end">
                  <Button type="error" class="delete-image-btn" @click="arrowRight(index)">
                    <Icon type="arrow-right-c" size="16" />
                  </Button>
                </Tooltip>
                <Tooltip content="点击删除" placement="bottom-end">
                  <Button type="error" class="delete-image-btn" @click="deleteIntroImage(index)">
                    <Icon type="android-cancel" size="16" />
                  </Button>
                </Tooltip>
              </div>
              </Col>
            </Row>
            <div>
              <span><span style="color:#57a3f3;">简介配图</span> 建议上传 <span style="color:red;">558 &times; 368</span> 尺寸的图片，格式为<span style="color:#57a3f3;">【jpg、jpeg、png】</span></span>
            </div>
          </Form-item>
          <Form-item label="参赛须知：" v-if="activityDatas.type==1" required style="margin-bottom: 8px;">
            <div class='card-tabs'>
              <div class="tabs-header">
                <span :class="nowSelectedIndex==0?'tabs-selected':''" @click="setnowSelectedIndex('0')">奖项设置</span>
                <span :class="nowSelectedIndex==1?'tabs-selected':''" @click="setnowSelectedIndex('1')">时间流程</span>
                <span :class="nowSelectedIndex==2?'tabs-selected':''" @click="setnowSelectedIndex('2')">参赛须知</span>
              </div>
              <div class="tabs-main">
                <div v-show="nowSelectedIndex==0">
                  <div style="width:98%;">
                    <Row style="font-size:14px;font-weight:bold;text-align:center;">
                      <Col span="4"> 类别
                      </Col>
                      <Col span="7"> 奖项名称
                      </Col>
                      <Col span="10"> 奖项描述
                      </Col>
                      <Col span="3"> 设定人数
                      </Col>
                      <template v-for="(item,index) in activityDatas.prize">
                        <Row>
                          <Col span="4">
                          <Form-item style="padding-right:5px;">
                            <Input type="text" v-model="item.type" placeholder="类别" @on-change="checkTypeLength(index)" @on-blur="checkTypeLength(index)"></Input>
                          </Form-item>
                          </Col>
                          <Col span="7" style="padding-right:5px;">
                          <Form-item :prop="'prizeName'+index">
                            <Input type="text" v-model="item.prizeName" placeholder="奖项名称" @on-change="checkPrizeNameLength(index)" @on-blur="checkPrizeNameLength(index)"></Input>
                          </Form-item>
                          </Col>
                          <Col span="10" style="padding-right:5px;">
                          <Input type="text" v-model="item.prizeIntro" placeholder="奖项描述，长度20位以内" @on-change="checkPrizeIntroLength(index)" @on-blur="checkPrizeIntroLength(index)"></Input>
                          </Col>
                          <Col span="3" class="pr" style="padding-right:2px;">
                          <Input type="text" v-model="item.totalPeople" placeholder="人数"></Input>
                          <Icon class="close" type="close-round" @click.native="deleteNoticeItem(index)"></Icon>
                          </Col>
                        </Row>
                      </template>
                    </Row>
                    <p>提示：<span style="color:#57a3f3;">类别在<span style="color:red;">10</span>位以内，奖项名称在<span style="color:red;">20</span>位以内，奖项描述在<span style="color:red;">256</span>位以内，设定人数小于<span style="color:red;">256</span></span>
                    </p>
                    <p class="notice" v-show="prizeError">{{prizeError}}</p>
                    <Button type="primary" long @click="addNewNotice" class="mt5">添加奖项</Button>
                  </div>
                </div>
                <div v-show="nowSelectedIndex==1">
                  <div style="width:98%;">
                    <Row style="font-size:14px;font-weight:bold;text-align:center;">
                      <Col span="4"> 类别
                      </Col>
                      <Col span="5"> 报名开始
                      </Col>
                      <Col span="5"> 报名结束
                      </Col>
                      <Col span="5"> 评审开始
                      </Col>
                      <Col span="5"> 奖项公布
                      </Col>
                    </Row>
                    <template v-for="(item,index) in activityDatas.activityTime">
                      <Row>
                        <Col span="4" style="padding-right:5px;">
                        <Select v-model="item.type" placeholder="类别" not-found-text>
                          <Option v-for="item in prizeType" :value="item" :key="item">{{ item}}</Option>
                        </Select>
                        </Col>
                        <Col span="5" style="padding-right:5px;">
                        <Date-picker v-model='item.signStartTime' type="date" placeholder="选择日期" :clearable='false' :editable="false">
                        </Date-picker>
                        </Col>
                        <Col span="5" style="padding-right:5px;">
                        <Date-picker v-model='item.signEndTime' type="date" placeholder="选择日期" :clearable='false' :editable="false">
                        </Date-picker>
                        </Col>
                        <Col span="5" style="padding-right:5px;">
                        <Date-picker v-model='item.reviewStartTime' type="date" placeholder="选择日期" :clearable='false' :editable="false">
                        </Date-picker>
                        </Col>
                        <Col span="5" class="pr" style="padding-right:5px;">
                        <Date-picker v-model='item.announceAwardsTime' type="date" placeholder="选择日期" :clearable='false' :editable="false">
                        </Date-picker>
                        <Icon class="close" type="close-round" @click.native="deleteTimeItem(index)"></Icon>
                        </Col>
                      </Row>
                    </template>
                    <p class="notice">{{activityTimeError}}</p>
                    <Button type="primary" long @click="addNewTime" class="mt5">添加分类</Button>
                    <p>提示：<span style="color:#57a3f3;">类别下拉框无数据时，请先填写奖项设置中的类别</span></p>
                  </div>
                </div>
                <div v-show="nowSelectedIndex==2">
                  <Form-item prop="reviewStandard">
                    <Input v-model="activityDatas.reviewStandard" type="textarea" :autosize="{minRows: 4,maxRows: 999}" placeholder="填写参赛须知"></Input>
                  </Form-item>
                </div>
              </div>
            </div>
            <p style='font-size: 14px;color: #ed3f14;height: 16px;'>{{activityNotice}}</p>
          </Form-item>
          <!-- 问卷-->
          <Form-item label="问卷地址：" v-if="activityDatas.type==2" required>
            <Row>
              <Col span="20">
              <Form-item prop="questionAnswerDetail">
                <Input v-model="activityDatas.questionAnswerDetail" type="text" placeholder=""></Input>
              </Form-item>
              </Col>
              <Col span="3" offset="1">
              <Button type='primary' long @click="previewUrl">预览</Button>
              </Col>
            </Row>
          </Form-item>
          <Form-item label="设置奖品：" v-if='activityDatas.type==2' required>
            <p>注意：单次发放积分数在1~200之间，积分数随机生成</p>
            <Row>
              <Col span="5">赠送积分总数：</Col>
              <Col span="4">
              <Form-item prop="integration" class="score-form-item">
                <Input v-model.number='activityDatas.score.integration'></Input>
              </Form-item>
              </Col>
            </Row>
            <Row>
              <Col span="5">发放个数：</Col>
              <Col span="4">
              <Form-item prop="number" class="score-form-item">
                <Input v-model.number='activityDatas.score.number'></Input>
              </Form-item>
              </Col>
            </Row>
          </Form-item>
          <Form-item :label="activityDatas.type==1?'大赛时间：':'活动时间：'" required>
            <Row>
              <Col span="8">
              <Form-item prop="activityStartTime">
                <Date-picker v-model='activityDatas.activityStartTime' type="date" placeholder="选择日期" :clearable='false' :editable="false">
                </Date-picker>
              </Form-item>
              </Col>
              <Col span="2" style="text-align: center;"> ---
              </Col>
              <Col span="8">
              <Form-item prop="activityEndTime">
                <Date-picker v-model='activityDatas.activityEndTime' type="date" placeholder="选择日期" :clearable='false' :editable="false">
                </Date-picker>
              </Form-item>
              </Col>
            </Row>
          </Form-item>
        </Form>
        <div class="tr pt10 pb10">
          <Button type="primary" :disabled="isSaveAndAudit" @click="saveTheActivity('activityEditForm')">保存</Button>
          <Button type="success" :disabled="isSaveAndAudit" @click="saveAndAuditTheActivity('activityEditForm')">提交审核并发布</Button>
        </div>
      </div>
    </Row>
    <!-- 选择机构modal -->
    <Modal v-model="selectOrganizationModal" :mask-closable="false" width="980" title="选择参赛机构">
      <div class="exam-model-body">
        <Row>
          <Col span="7">
          <span class="pt5">地区：</span>
          <Select v-model="filter.region" style="width: 60%;" @on-change="getOrganizationListData">
            <Option value="0">全部</Option>
            <Option v-for="(item,index) in regionList" :label="item" :value="item" :key="item+index"></Option>
          </Select>
          </Col>
          <!--           <Col span="6"> &nbsp;
          <span class="pt5">类型：</span>
          <Select v-model="filter.type" style="width: 60%;" @on-change="getOrganizationListData">
            <Option value="0">全部</Option>
            <Option v-for="(item,index) in typeList" :label="item.value" :value="item.id" :key="item.id+index"></Option>
          </Select>
          </Col> -->
          <Col span="7">
          <Button type="primary" @click="batchAdd">批量添加</Button>
          <Button type="error" @click="clearOrganizationList">全部清空已选择</Button>
          </Col>
          <Col span="10">
          <Input v-model="searchKey" icon="ios-search-strong" placeholder="搜索机构名称" @on-click="searchInfo"></Input>
          </Col>
        </Row>
        <Row>
          <Col span="12" class="pt10 pb10">
          <div style="min-height: 80px;">
            <Spin fix v-show="loadingData">
              <Icon type="load-c" size=18 class="demo-spin-icon-load" style="margin-top: 30px;"></Icon>
              <div>数据加载中...</div>
            </Spin>
            <Table v-show="!loadingData" :columns="accountColumns" :data="organizationListDatas" @on-select="tableSelectCurrent" @on-select-all='tableSelectAll' @on-select-cancel="tableCancelCurrent" @on-selection-change="tableChange" highlight-row @on-row-click="selectedOrg"></Table>
          </div>
          <div class="pt10 tr">
            <Page :total="tp" :current="cp" :page-size="ep" size="small" show-total show-elevator :page-size-opts='[10]' @on-change="currentPageChange" @on-page-size-change="eachPageChange"></Page>
          </div>
          </Col>
          <Col span="12">
          <div class="selected-area">
            <span class="selected-tag" v-for="(item,index) in hasSelectedOrganizationList" :key="item.id+index">
              {{item.organizationName}}
              <Icon type="close-round" size="14" class="tag-close-btn" @click.native="handleCancelSelected(item,index)"></Icon>
            </span>
            <!--  <Tag v-for="(item,index) in hasSelectedOrganizationList" :key="item.id+index" :name="item.organizationName" closable @on-close="handleCancelSelected">{{item.organizationName}}</Tag> -->
          </div>
          <p style="padding-left:10px;margin-top:5px;"><span style="color:red;">提示：</span>无法重复添加 <span style="color:red;">已选择</span> 的机构</p>
          </Col>
        </Row>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="selectOrganizationModal=false">返回活动</Button>
        <Button type="primary" @click="addSelectOrganizationList">确认选择</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
import VueCoreImageUpload from 'vue-core-image-upload'
export default {
  components: {
    'vue-core-image-upload': VueCoreImageUpload,
  },
  data() {
    const CheckQuestionAnswerDetail = (rule, value, callback) => {
      let urlReg = /^([hH][tT]{2}[pP]:\/\/|[hH][tT]{2}[pP][sS]:\/\/)(?:(?:\w*?)\.|)(?:\w*?)\.(?:\w{2,4})(?:\?.*|\/.*|)$/ig
      if (value === '') {
        callback(new Error('该项不能为空'))
      } else if (!urlReg.test(value)) {
        callback(new Error('该项必须是以http://或https://开头的网址'))
      } else {
        callback();
      }
    };
    const CheckOrganization = (rule, value, callback) => {
      if (this.organizationNameList === '' && this.activityDatas.allowJoin == 0) {
        callback(new Error('该项不能为空'));
      } else {
        callback();
      }
    };
    const CheckReviewStandard = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('该项不能为空'));
      } else{
        callback();
      }
    };
    const CheckIntegration = (rule, value, callback) => {
      let integration = this.activityDatas.score.integration;
      if (integration === '') {
        callback(new Error('该项不能为空'));
      } else if (!Number.isInteger(integration)) {
        callback(new Error('请输入数字'));
      } else {
        callback();
      }
    };
    const CheckNumber = (rule, value, callback) => {
      let integration = this.activityDatas.score.number;
      if (integration === '') {
        callback(new Error('该项不能为空'));
      } else if (!Number.isInteger(integration)) {
        callback(new Error('请输入数字'));
      } else {
        callback();
      }
    };
    return {
      isEdit: false,
      baseUrl: baseUrl,
      nowSelectedIndex: 0,

      whyRefusalRecode: {
        reason: '',
        reviewDate: '',
        username: ''
      },

      //表格选中的项
      selectItem: [],

      //学校列表modal
      selectOrganizationModal: false,

      isSaveAndAudit: false,

      filter: {
        region: '0',
        type: '0'
      },

      searchKey: '',
      tp: 0,
      ep: 10,
      cp: 1,
      loadingData: false,

      regionList: ['北京', '天津', '河北', '山西', '内蒙古', '辽宁', '吉林', '黑龙江', '上海', '江苏', '浙江', '安徽', '福建', '江西', '山东', '河南', '湖北', '湖南', '广东', '广西', '海南', '重庆', '四川', '贵州', '云南', '西藏', '陕西', '甘肃', '青海', '宁夏', '新疆', '香港', '澳门', '台湾'],

      typeList: [{
        id: 111,
        value: '农业'
      }],


      // 学校列表数据
      organizationListDatas: [],

      accountColumns: [{
          type: 'selection',
          width: 60,
          align: 'center'
        }, {
          title: '机构',
          key: 'name'
        }, {
          title: '地区',
          key: 'area',
          width: 120
        },
        //  {
        //   title: '类型',
        //   key: 'type'
        // }, 
        {
          title: '用户数',
          key: 'userNum',
          width: 80,
        },
        // {
        //   title: '操作',
        //   key: 'action',
        //   width: 100,
        //   align: 'center',
        //   render: (h, params) => {
        //     return h('Button', {
        //       props: {
        //         type: 'primary',
        //       },
        //       on: {
        //         click: () => {
        //           this.selectedOrg(params.row, params.index)
        //         }
        //       }
        //     }, '选择')
        //   }
        // }
      ],

      //选中学校临时数组
      hasSelectedOrganizationList: [],


      //表单验证规则
      activityEditFormRules: {
        organizationNameList: [{
          validator: CheckOrganization,
          trigger: 'blur',
        }],
        name: [{
          required: true,
          message: '该项不能为空',
          trigger: 'blur'
        }, {
          min: 2,
          max: 25,
          message: '长度控制在2-25字以内',
          trigger: 'blur'
        }],
        pcImage: [{
          required: true,
          message: '请上传轮播图'
        }],
        smallImage: [{
          required: true,
          message: '请上传封面图'
        }],
        intro: [{
          required: true,
          message: '该项不能为空',
          trigger: 'blur'
        }],
        introImage: [{
          required: true,
          message: '请上传简介配图'
        }],
        reviewStandard: [{
          validator: CheckReviewStandard,
          trigger: 'blur'
        }],
        // 'prizeName0': [{ validator: checkPrizeName, trigger: 'blur' }],
        questionAnswerDetail: [{
          validator: CheckQuestionAnswerDetail,
          trigger: 'blur'
        }],
        integration: [{
          validator: CheckIntegration,
          trigger: 'blur'
        }],
        number: [{
          validator: CheckNumber,
          trigger: 'blur'
        }],
        activityStartTime: [{
          type: 'date',
          required: true,
          message: '请选择开始时间',
          trigger: 'change'
        }],
        activityEndTime: [{
          type: 'date',
          required: true,
          message: '请选择结束时间',
          trigger: 'change'
        }]
      },

      //错误提示信息
      prizeError: '',
      activityTimeError: '',
      activityNotice: '',
    }
  },
  props: [
    'activityDatas'
  ],
  methods: {
    //切换tabs
    setnowSelectedIndex(index) {
      if (index != this.nowSelectedIndex) {
        this.nowSelectedIndex = index;
      }
    },

    //banner上传成功
    bannerUploadSuccess(res, file, fileList) {
      let _this = this;
      if (res.status == 200) {
        this.activityDatas.pcImage = res.data.pcImage;
        this.activityDatas.mobileImage = res.data.mobileImage;
        // window.setTimeout(function() {
        //   _this.$refs.bannerUpload.clearFiles();
        // }, 2000)
      }
    },

    //删除轮播图
    deletePcImage() {
      this.activityDatas.pcImage = '';
      this.activityDatas.mobileImage = '';
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

    handleFormatError(file) {
      this.$Notice.error({
        title: '文件格式不正确',
        desc: '文件 ' + file.name + ' 格式不正确，请上传 jpg 或 png 格式的图片。'
      });
    },

    //banner上传之前
    bannerBeforeUpload(file) {},
    //banner上传失败
    uploadOnError(error, file, fileList) {
      if (error) {
        this.$Message.error({
          content: '网络错误，文件上传失败，请稍后再试！',
          duration: 10
        })
      }
    },


    //smallImage上传成功
    smallImageUploadSuccess(res, file, fileList) {
      let _this = this;
      if (res.status == 200) {
        this.activityDatas.bigImage = res.data.bigImage;
        this.activityDatas.smallImage = res.data.smallImage;
        // window.setTimeout(function() {
        //   _this.$refs.smallImageUpload.clearFiles();
        // }, 2000)

      }
    },

    //删除smallImage
    deleteSmallImage() {
      this.activityDatas.bigImage = '';
      this.activityDatas.smallImage = '';
    },
    //smallImage上传之前
    smallImageBeforeUpload(file) {},

    //introImage上传成功
    introImageUploadSuccess(res, file, fileList) {
      let _this = this;
      if (res.status == 200) {
        this.activityDatas.introImage.push({
          introImage: res.data.introImage
        })
        // window.setTimeout(function() {
        //   _this.$refs.introImageUpload.clearFiles();
        // }, 2000)

      }
    },
    //introImage上传之前
    introImageBeforeUpload(file) {},

    //删除introImage
    deleteIntroImage(index) {
      this.activityDatas.introImage.splice(index, 1);
    },

    //前移introImage
    arrowLeft(index) {
      if (index == 0) {
        return;
      }
      this.arrowImage(this.activityDatas.introImage, index, index - 1);
    },

    arrowRight(index) {
      if (index == this.activityDatas.introImage.length - 1) {
        return;
      }
      this.arrowImage(this.activityDatas.introImage, index, index + 1);
    },

    arrowImage(arr, index1, index2) {
      arr[index1] = arr.splice(index2, 1, arr[index1])[0];
      return arr;
    },

    checkIsEdit() {
      if (this.$route.path.indexOf('/edit') != -1) {
        this.isEdit = true;
        this.getWhyRefusalById();
      } else {
        this.isEdit = false;
      }
    },
    gobackList() {
      this.$router.push({
        path: '/wrap/activity/myactivity',
        query: {

        }
      })
    },
    //删除单条奖项
    deleteNoticeItem(index) {
      if (this.activityDatas.prize.length == 1) {
        this.activityDatas.prize.splice(index, 1);
        this.activityDatas.prize.push({
          type: '',
          prizeName: '',
          prizeIntro: '',
          totalPeople: ''
        })
      } else {
        this.activityDatas.prize.splice(index, 1)
      }
    },
    //新增单条奖项
    addNewNotice() {
      let lastPrize = this.activityDatas.prize[this.activityDatas.prize.length - 1];
      if (lastPrize.type != '' && lastPrize.prizeName != '' && lastPrize.prizeIntro != '' && lastPrize.totalPeople != '') {
        let Reg = /^[1-9]+[0-9]*]*$/;
        if (Reg.test(lastPrize.totalPeople) && parseInt(lastPrize.totalPeople) <= 255 && lastPrize.type.length <= 10 && lastPrize.prizeName.length <= 20 && lastPrize.prizeIntro.length <= 256) {
          this.activityDatas.prize.push({
            type: '',
            prizeName: '',
            prizeIntro: '',
            totalPeople: ''
          });
          this.prizeError = '';
        } else {
          this.prizeError = '上一条填写的信息不符合规则';
        }
      } else {
        this.prizeError = '请先将上一条填写完整';
        this.$Message.error({
          content: this.prizeError,
          duration: 3
        })
      }

    },

    //增加单条分类
    addNewTime() {
      let lastTime = this.activityDatas.activityTime[this.activityDatas.activityTime.length - 1];
      if (lastTime.type != '' && lastTime.signStartTime != '' && lastTime.signEndTime != '' && lastTime.reviewStartTime != '' && lastTime.announceAwardsTime != '') {
        this.activityDatas.activityTime.push({
          type: '',
          signStartTime: '',
          signEndTime: '',
          reviewStartTime: '',
          announceAwardsTime: ''
        });
        this.activityTimeError = '';
      } else {
        this.activityTimeError = '请先将上一条填写完整';
        this.$Message.error({
          content: this.activityTimeError,
          duration: 3
        })
      }
    },

    //删除单条分类
    deleteTimeItem(index) {
      if (this.activityDatas.activityTime.length == 1) {
        this.activityDatas.activityTime.splice(index, 1);
        this.activityDatas.activityTime.push({
          type: '',
          signStartTime: '',
          signEndTime: '',
          reviewStartTime: '',
          announceAwardsTime: ''
        })
      } else {
        this.activityDatas.activityTime.splice(index, 1)
      }
    },

    //保存活动
    saveTheActivity(formName) {
      // this.activityNotice = '该项有填写错误 / 未填写的内容'
      this.$refs[formName].validate((valid) => {
        let isComplete = false;
        if (this.validateActivityNotice()) {
          isComplete = true;
          this.activityNotice = '';
          this.prizeError = '';
          this.activityTimeError = '';
        } else {
          this.prizeError = '';
          this.activityTimeError = '';
          this.activityNotice = '参赛须知有填写不符合规则 / 未填写完整的内容，请仔细核查'
          this.$Message.error('表单验证失败!');
        }
        if (valid && isComplete) {
          this.saveAndAudit(4);
        } else {
          this.$Message.error('表单验证失败!');
        }
      })
    },

    //保存并提交审核
    saveAndAuditTheActivity(formName) {
      this.$refs[formName].validate((valid) => {
        let isComplete = false;
        if (this.validateActivityNotice()) {
          isComplete = true;
          this.activityNotice = '';
          this.prizeError = '';
          this.activityTimeError = '';
        } else {
          this.prizeError = '';
          this.activityTimeError = '';
          this.activityNotice = '参赛须知有填写不合符规则 / 未填写完整的内容，请仔细核查'
          this.$Message.error('表单验证失败!');
        }
        if (valid && isComplete) {
          this.saveAndAudit(2);
        } else {
          this.$Message.error('表单验证失败!');
        }
      })
    },

    //验证参赛须知是否填写完整
    validateActivityNotice() {
      if (this.activityDatas.type == 1) {
        let isComplete = false;
        let Reg = /^[1-9]+[0-9]*]*$/;

        let prizeDatas = this.activityDatas.prize;
        for (var i = 0; i <= prizeDatas.length - 1; i++) {
          if (prizeDatas[i].type != '' && prizeDatas[i].type.length <= 10 && prizeDatas[i].prizeName != '' && prizeDatas[i].prizeName.length <= 20 && prizeDatas[i].prizeIntro != '' && prizeDatas[i].prizeIntro.length <= 256 && prizeDatas[i].totalPeople != '' && Reg.test(prizeDatas[i].totalPeople) && Reg.test(prizeDatas[i].totalPeople) != 'undefined' && parseInt(prizeDatas[i].totalPeople) <= 255) {
            isComplete = true;
          } else {
            isComplete = false;
            return isComplete;
          }
        }

        let activityTimeDatas = this.activityDatas.activityTime;
        for (var i = 0; i <= activityTimeDatas.length - 1; i++) {
          if (activityTimeDatas[i].type != '' && activityTimeDatas[i].signStartTime != '' && activityTimeDatas[i].signEndTime != '' && activityTimeDatas[i].reviewStartTime != '' && activityTimeDatas[i].announceAwardsTime != '') {
            isComplete = true;
          } else {
            isComplete = false;
            return isComplete;
          }
        }

        if (this.activityDatas.reviewStandard != '') {
          isComplete = true;
        } else {
          isComplete = false;
          return isComplete;
        }
        return isComplete;
      } else {
        return true;
      }

    },

    //验证奖项设置中限制输入长度
    checkTypeLength(index) {
      // let content = this.activityDatas.prize[index].type;
      // if (content.length > 20) {
      //   this.prizeError = '第 ' + (index + 1) + ' 条类别长度控制在20位以内';
      // }
    },
    checkPrizeNameLength(index) {
      // let content = this.activityDatas.prize[index].prizeName;
      // if (content.length > 20) {
      //   this.prizeError = '第 ' + (index + 1) + ' 条奖项名称长度控制在20位以内';
      // }
    },
    checkPrizeIntroLength(index) {
      // let content = this.activityDatas.prize[index].prizeIntro;
      // if (content.length > 20) {
      //   this.prizeError = '第 ' + (index + 1) + ' 条奖项描述长度控制在20位以内';
      // }
    },

    //保存或者提交审核
    saveAndAudit(auditState) {
      if (this.isEdit == true) {
        this.$set(this.activityDatas, 'activityId', this.activityDatas.id)
      }
      if (this.activityDatas.type == 1) {
        this.activityDatas.activityTime.forEach((item, index) => {
          let newItem = Object.assign({}, item, {
            signStartTime: this.dateToSeconds(item.signStartTime),
            signEndTime: this.dateToSeconds(item.signEndTime),
            reviewStartTime: this.dateToSeconds(item.reviewStartTime),
            announceAwardsTime: this.dateToSeconds(item.announceAwardsTime)
          })
          this.$set(this.activityDatas.activityTime, index, newItem)
        });
      }
      let userInfo = JSON.parse(window.sessionStorage.getItem('bg_user_info'));
      this.$http.post('/v1/admin/activity/add',
        Object.assign({}, this.activityDatas, {
          auditState: auditState,
          userId: userInfo.id,
          userName: userInfo.username,
          companyName: userInfo.agency,
          activityStartTime: this.dateToSeconds(this.activityDatas.activityStartTime),
          activityEndTime: this.dateToSeconds(this.activityDatas.activityEndTime),
        })
      ).then((res) => {
        if (res.data.status == 200) {
          this.isSaveAndAudit = true;
          if (this.isEdit == false) {
            this.updateFilter({
              eventType: '0',
              auditStatus: '0',
              eventStatus: '0',
              company: '',
              searchKey: '',
              cp: 1,
            })
          }
          if (auditState == 4) {
            this.$Message.success('活动保存成功');
          } else {
            this.$Message.success('活动保存并提交审核成功');
          }
          this.$router.push({
            path: '/wrap/activity/myactivity'
          })
        } else if (res.data.status == 1002) {
          this.$Message.error('保存失败，请核查提交的数据')
        }
      })
    },

    // 预览问卷地址
    previewUrl() {
      let _this = this;
      this.$refs.activityEditForm.validateField('questionAnswerDetail', function(valid) {
        if (valid != '') {
          _this.$Message.error({
            content: '您输入的不是正确的网址',
            duration: 3,
          })
        } else {
          window.open(_this.activityDatas.questionAnswerDetail);
        }
      })
    },


    //点击选择参赛学校
    selectOrganizationNameList() {
      this.selectOrganizationModal = true;
    },

    //获取学校列表
    getOrganizationListData() {
      this.loadingData = true;
      this.$http.post('/v1/admin/activity/school-list', {
        area: this.filter.region,
        keyword: this.searchKey,
        pageSize: this.ep,
        page: this.cp
      }).then((res) => {
        this.organizationListDatas = res.data.data.data.rows;
        this.tp = parseInt(res.data.data.data.total)
        this.loadingData = false;
      })
    },


    searchInfo() {
      if (this.searchKey != '') {
        this.cp = 1;
        this.getOrganizationListData();
      } else {
        this.$Message.warning('搜索关键字不能为空')
      }
    },

    //选中其中一项
    selectedOrg(row, index) {
      let newSelected = {
        organizationId: row.id,
        organizationName: row.name
      }
      this.hasSelectedOrganizationList.push(newSelected);

      //去重
      var unique = {};
      this.hasSelectedOrganizationList.forEach(function(gpa) { unique[JSON.stringify(gpa)] = gpa });
      this.hasSelectedOrganizationList = Object.keys(unique).map(function(u) { return JSON.parse(u) });

    },

    //批量添加学校
    batchAdd() {
      if (this.selectItem.length != 0) {
        this.selectItem.forEach((item, index) => {
          this.hasSelectedOrganizationList.push({
            organizationId: item.id,
            organizationName: item.name
          })
        })
        //去重
        var unique = {};
        this.hasSelectedOrganizationList.forEach(function(gpa) { unique[JSON.stringify(gpa)] = gpa });
        this.hasSelectedOrganizationList = Object.keys(unique).map(function(u) { return JSON.parse(u) });
      } else {
        this.$Message.warning('请至少勾选一个机构后再试！')
      }
    },

    //删除选中额某一项
    handleCancelSelected(item, index) {
      this.hasSelectedOrganizationList.splice(index, 1)
    },

    clearOrganizationList() {
      if (this.hasSelectedOrganizationList.length != 0) {
        this.hasSelectedOrganizationList = [];
        this.$Message.success({
          content: '参赛机构已清空',
          duration: 3
        })
      } else {
        this.$Message.warning({
          content: '请先添加参赛机构',
          duration: 3
        })
      }

    },

    //确认选择的学校
    addSelectOrganizationList() {
      this.activityDatas.organization = this.hasSelectedOrganizationList.slice();
      this.selectOrganizationModal = false;
    },


    //分页
    currentPageChange(value) {
      this.cp = value;
      this.getOrganizationListData();
    },

    eachPageChange(value) {
      this.ep = value;
      this.getOrganizationListData();
    },

    //表格单选/全选
    tableSelectCurrent(selection) {
      this.selectItem = selection;
    },
    tableSelectAll(selection) {
      this.selectItem = selection;
    },
    tableCancelCurrent(selection, row) {
      this.selectItem = selection;
    },
    tableChange(selection) {
      this.selectItem = selection;
    },

    activityTypeChange() {
      this.$refs['activityEditForm'].resetFields();
      this.activityNotice = '';
    },


    //新建问卷
    addNewQuestionnaire() {
      let userInfo = JSON.parse(window.sessionStorage.getItem('bg_user_info'));
      let username = userInfo.username;
      let password = userInfo.password;

      window.open('http://answer.jqweike.com/index.php/admin/authentication/sa/login?user=' + username + '&password=' + password)
    },

    //页面加载时获取上次未通过原因
    getWhyRefusalById() {
      this.$http.post('/v1/admin/activity/get-reason', {
          activityId: this.$route.query.id,
        })
        .then((res) => {
          if (res.data.status == 200) {
            this.whyRefusalRecode.reason = res.data.data.reason;
            this.whyRefusalRecode.reviewDate = this.secondsToNormalDate(res.data.data.reviewDate * 1000);
            this.whyRefusalRecode.username = res.data.data.username;
          }
        })
    }

  },
  watch: {
    'selectOrganizationModal': function(val, oldVal) {
      if (val) {
        this.getOrganizationListData();
        this.hasSelectedOrganizationList = this.activityDatas.organization.slice();
      } else {
        this.searchKey = '';
        this.filter.region = '0';
        this.selectItem = [];
        this.cp = 1;
      }
    },
    'searchKey': function(val, oldVal) {
      if (!val && oldVal) {
        this.cp = 1;
        this.getOrganizationListData();
      }
    },
    "activityDatas.allowJoin": function(val, oldVal) {
      if (val != 0 && oldVal == 0) {
        this.$refs.activityEditForm.validateField('organizationNameList');
        this.activityDatas.organization = [];
        this.hasSelectedOrganizationList = [];
      }
    },
  },
  mounted() {
    this.checkIsEdit();
  },
  computed: {
    'prizeType': function() {
      let typeArr = [];
      this.activityDatas.prize.forEach((item, index) => {
        if (item.type != '') {
          typeArr.push(item.type);
        }
      });
      return Array.from(new Set(typeArr));
    },
    'organizationNameList': function() {
      let organizationName = [];

      if (this.activityDatas.organization.length != 0) {
        this.activityDatas.organization.forEach((item, index) => {
          organizationName.push(item.organizationName)
        })
      }
      return organizationName.join('、');
    },

  }
}

</script>
<style lang="less">
.activity-edit-container {
  /*   .ivu-form-item .ivu-form-item {
  margin-bottom: 20px;
} */
  .image-aside {
    top: 80px;
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
  .intro-image-handle {
    position: absolute;
    left: 0;
    top: 5px;
    width: 100%;
    padding-right: 5px;
  }
  .intro-image-handle .ivu-tooltip {
    position: static;
  }

  .banner-upload-btn {
    width: 140px;
    border: 1px solid #dddee1;
    border-radius: 4px;
    margin-bottom: 20px;
    text-align: center;
    background-color: #2d8cf0;
    border-color: #2d8cf0;
    color: #fff;
  }

  .g-resize {
    display: none!important;
  }
  .activity-edit-main {
    padding-bottom: 150px;
    width: 700px;
    .ivu-upload-drag {
      border-style: solid;
    }
    .close {
      position: absolute;
      right: -10px;
      top: 10px;
      cursor: pointer;
      color: red;
      &:hover {
        color: #2d8cf0;
      }
    }
    img {
      width: 100%;
      height: 100%;
    }
    .intro-cover {
      height: 127px;
      margin-bottom: 10px;
      overflow: hidden;
      margin-right: -4px;
    }
    .intro-cover1 {
      height: 127px;
      margin-bottom: 10px;
      overflow: hidden;
      margin-right: -4px;
    }

    .demo-tabs-style>.ivu-tabs.ivu-tabs-card>.ivu-tabs-bar .ivu-tabs-tab {
      border-radius: 0;
      background: #fff;
      padding: 0;
      width: 190px;
      line-height: 30px;
      text-align: center;
    }
    .demo-tabs-style>.ivu-tabs.ivu-tabs-card>.ivu-tabs-bar .ivu-tabs-tab-active {
      border-bottom: 2px solid #3399ff;
    }
    .without-banner-container {
      height: 152px;
      position: relative;
      border: 1px solid #dddee1;
      border-radius: 4px;
      overflow: hidden;
    }
    .without-banner-main {
      font-size: 16px;
      color: #bbb;
      line-height: 150px;
      text-align: center;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
    }
    .without-cover-container {
      position: relative;
      height: 208px;
      border: 1px solid #dddee1;
      border-radius: 4px;
      overflow: hidden;
    }
    .without-cover-main {
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      line-height: 208px;
      font-size: 16px;
      text-align: center;
      color: #bbb;
    }
    .without-intro-container {
      position: relative;
      overflow: hidden;
      .ivu-form-item-error-tip {
        left: 6px;
      }
    }
    .without-intro-mian {
      border: 1px solid #dddee1;
      font-size: 16px;
      margin-left: 4px;
      height: 127px;
      color: #bbb;
      border-radius: 4px;
      line-height: 127px;
      text-align: center;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      overflow: hidden;
    }
    .card-tabs {
      width: 100%;
      .notice {
        font-size: 14px;
        color: red;
        height: 16px;
      }
      .tabs-header {
        overflow: hidden;
        height: 40px;
        border-bottom: 1px solid #dddee1;
        span {
          width: 32.82%;
          display: inline-block;
          text-align: center;
          height: 39px;
          line-height: 40px;
          font-size: 16px;
          font-weight: bold;
          border-top: 1px solid #dddee1;
          border-left: 1px solid #dddee1;
          border-right: 1px solid #dddee1;
          cursor: pointer;
          &:hover {
            color: #5c9acf;
          }
        }
        .tabs-selected {
          border-bottom: 2px solid #5c9acf;
          color: #5c9acf;
        }
      }
      .tabs-main {
        padding: 10px 0 0;
        width: 100%;
        position: relative;
        min-height: 150px;
      }
    }
  }
  .ivu-date-picker {
    .ivu-input {
      font-size: 13px;
    }
  }

  .score-form-item {
    margin-bottom: 20px;
  }
}

.selected-area {
  height: 500px;
  border: 1px solid #dddee1;
  border-radius: 4px;
  margin-top: 10px;
  margin-left: 10px;
  padding: 10px;
  overflow: auto;
  .selected-tag {
    display: inline-block;
    background-color: #dddee1;
    cursor: pointer;
    padding: 4px 26px 4px 10px;
    margin-right: 10px;
    margin-bottom: 6px;
    border-radius: 4px;
    position: relative;

    &:hover {
      background-color: #e9eaec;
    }

    .tag-close-btn {
      position: absolute;
      right: 10px;
      top: 6px;

      &:hover {
        color: #2d8cf0;
      }
    }
  }
}

</style>
