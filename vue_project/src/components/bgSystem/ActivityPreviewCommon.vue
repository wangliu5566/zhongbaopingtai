<template>
  <div class="activity-preview-container">
    <Row>
      <Button type="primary" icon="chevron-left" @click="gobackList">返回列表</Button>
    </Row>
    <Row class="pt20">
      <div class="activity-preview-main">
        <Alert type="error" closable v-if="whyRefusalRecode.reviewDate&&whyRefusalRecode.username">
          <div slot="desc">
            <div class="why-refusal-main">
              <div class="refusal-time">{{whyRefusalRecode.reviewDate}}</div>
              <div class="refusal-main">审核未通过原因：{{whyRefusalRecode.reason}}</div>
            </div>
            <p style="text-align: right;padding-right: 20px;">
              审核人：{{whyRefusalRecode.username}}
            </p>
          </div>
        </Alert>
        <Form :model="previewDatas" :label-width="120">
          <Form-item label="活动类型：">
            <Radio-group v-model="previewDatas.activity.type">
              <Radio label="1" disabled>大赛</Radio>
              <Radio label="2" disabled>问卷</Radio>
            </Radio-group>
          </Form-item>
          <Form-item label="活动适用范围">
            <Select v-model="previewDatas.activity.allowJoin" style="width: 200px" disabled placeholder="">
              <Option value="0">选择机构参加</Option>
              <Option value="1">所有机构参加</Option>
              <Option value="2">所有人参加</Option>
            </Select>
          </Form-item>
          <Form-item label="参赛机构">
            <Input type="textarea" :autosize="{minRows: 1,maxRows: 6}" v-model="organizationList" placeholder="" readonly>
            </Input>
          </Form-item>
          <Form-item :label="previewDatas.activity.type==1?'大赛名称：':'活动名称：'">
            <Input :value="previewDatas.activity.name" placeholder="" readonly></Input>
          </Form-item>
          <Form-item :label="previewDatas.activity.type==1?'大赛轮播图：':'活动轮播图：'">
            <div class="without-banner-container">
              <img :src="baseUrl+previewDatas.activity.pcImage" alt="">
            </div>
          </Form-item>
          <Form-item :label="previewDatas.activity.type==1?'大赛封面图：':'活动封面图：'">
            <Row>
              <Col span="12">
              <div class="without-cover-container">
                <Form-item prop='smallImage'>
                  <img v-if="previewDatas.activity.smallImage" :src="baseUrl+previewDatas.activity.smallImage" alt="">
                </Form-item>
              </div>
              </Col>
            </Row>
          </Form-item>
          <Form-item :label="previewDatas.activity.type==1?'大赛简介：':'活动简介：'">
            <Input :value="previewDatas.activity.intro" type="textarea" :autosize="true" placeholder="" readonly></Input>
          </Form-item>
          <Form-item label="简介配图：" v-if="previewDatas.activity.type==1">
            <div class="intro-cover" v-for="item in previewDatas.introImages" :key="item.introImage">
              <img :src="baseUrl+item.introImage" alt="简介配图">
            </div>
          </Form-item>
          <Form-item label="参赛须知：" v-if="previewDatas.activity.type==1">
            <div class="demo-tabs-style">
              <Tabs type="card">
                <Tab-pane label="奖项设置">
                  <table>
                    <thead style="font-size:14px;">
                      <th>类别</th>
                      <th>奖项名称</th>
                      <th>奖项描述</th>
                      <th>设定人数</th>
                    </thead>
                    <tbody>
                      <tr v-for="(item,index) in previewDatas.prizes" :key="index">
                        <td>
                          <Input :value="item.type" readonly></Input>
                        </td>
                        <td>
                          <Input :value="item.prizeName" readonly></Input>
                        </td>
                        <td>
                          <Input :value="item.prizeIntro" style="width:340px;" readonly></Input>
                        </td>
                        <td>
                          <Input :value="item.totalPeople" style="width:60px;" readonly></Input>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </Tab-pane>
                <Tab-pane label="时间流程">
                  <table>
                    <thead style="font-size:14px;">
                      <th>类别</th>
                      <th>报名开始</th>
                      <th>报名结束</th>
                      <th>评审开始</th>
                      <th>奖项公布</th>
                    </thead>
                    <tbody>
                      <tr v-for="(item,index) in previewDatas.activityTime" :key="index">
                        <td>
                          <Input :value="item.type" readonly></Input>
                        </td>
                        <td>
                          <Date-picker :value='item.signStartTime' type="date" placeholder="" :clearable='false' readonly>
                          </Date-picker>
                        </td>
                        <td>
                          <Date-picker :value='item.signEndTime' type="date" placeholder="" :clearable='false' readonly>
                          </Date-picker>
                        </td>
                        <td>
                          <Date-picker :value='item.reviewStartTime' type="date" placeholder="" :clearable='false' readonly>
                          </Date-picker>
                        </td>
                        <td>
                          <Date-picker :value='item.announceAwardsTime' type="date" placeholder="" :clearable='false' readonly>
                          </Date-picker>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </Tab-pane>
                <Tab-pane label="参赛须知">
                  <Input :value="previewDatas.activity.reviewStandard" type="textarea" :autosize="{minRows: 2,maxRows: 6}" readonly></Input>
                </Tab-pane>
              </Tabs>
            </div>
          </Form-item>
          <!-- 问卷-->
          <Form-item label="问卷地址：" v-if="previewDatas.activity.type==2">
            <Row>
              <Col span="20">
              <Input v-model="previewDatas.activity.questionAnswerDetail" type="text" placeholder="" readonly></Input>
              </Col>
              <Col span="3" offset="1">
              <Button type='primary' long @click="previewUrl">预览</Button>
              </Col>
            </Row>
          </Form-item>
          <Form-item label="设置奖品：" v-if='previewDatas.activity.type==2'>
            <p>注意：单次发放积分数在1~200之间，积分数随机生成</p>
            <Row>
              <Col span="5">赠送积分总数：</Col>
              <Col span="4">
              <Input v-model='previewDatas.score.integration' readonly></Input>
              </Col>
            </Row>
            <Row>
              <Col span="5">发放个数：</Col>
              <Col span="4">
              <Input v-model='previewDatas.score.number' readonly></Input>
              </Col>
            </Row>
          </Form-item>
          <Form-item :label="previewDatas.activity.type==1?'大赛时间：':'活动时间：'">
            <Row>
              <Col span="8">
              <Date-picker :value='previewDatas.activity.activityStartTime' type="date" placeholder="" :clearable='false' readonly>
              </Date-picker>
              </Col>
              <Col span="2" style="text-align: center;"> ---
              </Col>
              <Col span="8">
              <Date-picker :value='previewDatas.activity.activityEndTime' type="date" placeholder="" :clearable='false' readonly>
              </Date-picker>
              </Col>
            </Row>
          </Form-item>
          <Form-item class="tr" v-if="isAudit">
            <Button type="primary" :disabled="refuseDisabled" @click="refuseTheAudit">拒绝</Button>
            <Button type="success" :disabled="passDisabled" @click="passTheAudit">通过</Button>
          </Form-item>
        </Form>
      </div>
    </Row>
    <Modal v-model="whyRefusalModal" :mask-closable="false" width="600" title="请填写拒绝原因">
      <div class="exam-model-body">
        <Form :rules="whyRefusalRules" ref="whyRefusalForm" :model="whyRefusal">
          <Form-item prop="whyRefusal">
            <Input v-model="whyRefusal.whyRefusal" type="textarea" :autosize="{minRows: 4,maxRows: 6}" placeholder="请填写拒绝原因，最多35字"></Input>
          </Form-item>
        </Form>
      </div>
      <div slot="footer">
        <Button type="ghost" @click="whyRefusalModal=false">返回</Button>
        <Button type="primary" @click="submitWhyRefusal('whyRefusalForm')">确认提交</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
export default {
  data() {
    return {
      isAudit: false,
      baseUrl: baseUrl,
      previewDatas: {
        activity: {
          type: '1',
          userId: '',
          userName: '',
          companyName: '',
          name: '',
          pcImage: '',
          mobileImage: '',
          smallImage: '',
          bigImage: '',
          reviewStandard: '',
          activityStartTime: '',
          activityEndTime: '',
          questionAnswerDetail: '',
        },
        organization: [],
        introImages: [],
        prizes: [{
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
        score: {
          integration: '',
          number: '',
        }
      },

      //拒绝原因
      whyRefusal: {
        whyRefusal: ''
      },
      whyRefusalModal: false,

      whyRefusalRules: {
        whyRefusal: [{
          required: true,
          message: '该项不能为空',
          trigger: 'blur'
        }, {
          max: 35,
          message: '最多填写35字',
          trigger: 'blur'
        }]
      },

      whyRefusalRecode: {
        reason: '',
        reviewDate: '',
        username: ''
      }

    }
  },
  methods: {
    checkIsAudit() {
      if (this.$route.path.indexOf('/audit') != -1) {
        this.isAudit = true;
        this.getWhyRefusalById();
      } else {
        this.isAudit = false;
      }
    },
    gobackList() {
      this.$router.push({
        path: '/wrap/activity/myactivity',
        query: {

        }
      })
    },

    getListData() {
      this.$http.get('/v1/admin/activity/get-info-admin', {
        params: {
          type: this.$route.query.type,
          activityId: this.$route.query.id
        }
      }).then((res) => {
        this.previewDatas = res.data.data;
        //把毫秒转为标准时间
        if (this.previewDatas.activityTime) {
          this.previewDatas.activityTime.forEach((item, index) => {
            this.$set(this.previewDatas.activityTime, index, {
              announceAwardsTime: this.secondsToDate(item.announceAwardsTime),
              reviewStartTime: this.secondsToDate(item.reviewStartTime),
              signEndTime: this.secondsToDate(item.signEndTime),
              signStartTime: this.secondsToDate(item.signStartTime),
              type: item.type
            })
          })
        }

        this.previewDatas.activity.activityStartTime = this.secondsToDate(this.previewDatas.activity.activityStartTime);
        this.previewDatas.activity.activityEndTime = this.secondsToDate(this.previewDatas.activity.activityEndTime)
      })
    },

    //审核不通过
    refuseTheAudit() {
      this.whyRefusalModal = true;

    },

    submitWhyRefusal(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$http.post('/v1/admin/activity/review-activity', {
            activityId: this.previewDatas.activity.id,
            allow: '3',
            userId: JSON.parse(window.sessionStorage.getItem('bg_user_info')).id,
            reason: this.whyRefusal.whyRefusal
          }).then((res) => {
            if (res.data.status == 200) {
              this.$Message.success({
                content: '该活动审核不通过操作成功',
                duration: 2
              });
              this.previewDatas.activity.auditState = 3;
              this.$router.push({
                path: '/wrap/activity/myactivity',
                query: ''
              });
            }
          })
        }
      })
    },

    //审核通过
    passTheAudit() {
      this.$http.post('/v1/admin/activity/review-activity', {
        activityId: this.previewDatas.activity.id,
        allow: '1'
      }).then((res) => {
        if (res.data.status == 200) {
          this.$Message.success({
            content: '该活动审核通过操作成功',
            duration: 4
          });
          this.previewDatas.activity.auditState = 1;
          this.$router.push({
            path: '/wrap/activity/myactivity',
            query: ''
          });
        }
      })
    },
    //预览问卷地址
    previewUrl() {
      if (this.previewDatas.activity.questionAnswerDetail.indexOf('http://') != -1 || this.previewDatas.activity.questionAnswerDetail.indexOf('https://') != -1) {
        window.open(this.previewDatas.activity.questionAnswerDetail);
      } else {
        window.open('http://' + this.previewDatas.activity.questionAnswerDetail);
      }

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
  computed: {
    'organizationList': function() {
      let organizationName = [];
      if (this.previewDatas.organization.lenght != 0) {
        this.previewDatas.organization.forEach((item, index) => {
          organizationName.push(item.organizationName)
        })
      }
      return organizationName.join('、');
    },
    'refuseDisabled': function() {
      return this.previewDatas.activity.auditState == 3 ? true : false;
    },
    'passDisabled': function() {
      return this.previewDatas.activity.auditState == 1 ? true : false;
    }
  },
  mounted() {
    this.checkIsAudit();
    this.getListData();
  },
  watch: {
    'whyRefusalModal': function(val, oldVal) {
      if (!val && oldVal) {
        this.$refs.whyRefusalForm.resetFields();
      }
    }
  }
}

</script>
<style lang="less">
.activity-preview-container {
  .activity-preview-main {
    width: 700px;
    .why-refusal-main{
      font-size: 14px;
      overflow: hidden;
      .refusal-time{
        float: left;
        width: 100px;
      }
      .refusal-main{
        float: left;
        width: 540px;
      }
      
    }
    img {
      width: 100%;
      height: 100%;
    }
    .intro-cover {
      width: 186px;
      margin-right: 10px;
      height: 120px;
      float: left;
      margin-bottom: 10px;
      border-radius: 4px;
      overflow: hidden;
      &:nth-child(3n) {
        margin-right: 0;
      }
    }
    .without-banner-container {
      height: 150px;
      position: relative;
      border: 1px solid #dddee1;
      border-radius: 4px;
      overflow: hidden;
    }
    .without-cover-container {
      height: 208px;
      border: 1px solid #dddee1;
      border-radius: 4px;
      overflow: hidden;
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
  }
  .ivu-date-picker {
    .ivu-input {
      font-size: 13px;
    }
  }
}

</style>
