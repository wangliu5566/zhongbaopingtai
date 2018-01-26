<?php
/**
 * Login Form
 */
?>
<noscript>If you see this you have probably JavaScript deactivated. LimeSurvey does not work without Javascript being activated in the browser!</noscript>
<div class="container-fluid welcome">
    <div class="row text-center">
        <div id="login-panel">
            <div class="panel panel-primary login-panel" id="panel-1">

                <!-- Header -->
              <!--  <div class="panel-body">
                    <div class="row">
                          <img alt="logo" id="profile-img" class="profile-img-card center-block" src="<?php /*echo LOGO_URL;*/?>" />
                             <p><?php /*eT("Administration");*/?></p>
                    </div>
                </div>-->

                <!-- Action Name -->
                <div class="row login-title login-content" style="border-bottom: none;">
                      <div class="col-lg-12">
                       <h3 id="loginTitle"></h3>
                    </div>
                </div>

                <!-- Form -->
                <?php echo CHtml::form(array('admin/authentication/sa/zbLogin'), 'post', array('id'=>'loginform', 'name'=>'loginform'));?>
                    <div  class="row login-content login-content-form" style="display: none;">
                        <div class="col-lg-12">
                            <?php
                                $pluginNames = array_keys($pluginContent);
                                if (!isset($defaultAuth))
                                {
                                    // Make sure we have a default auth, if not set, use the first one we find
                                    $defaultAuth = reset($pluginNames);
                                }

                                if (count($pluginContent)>1)
                                {
                                    $selectedAuth = App()->getRequest()->getParam('authMethod', $defaultAuth);
                                    if (!in_array($selectedAuth, $pluginNames))
                                    {
                                        $selectedAuth = $defaultAuth;
                                    }
                            ?>

                           <label for='authMethod'><?php eT("Authentication method"); ?></label>
                                <?php
                                    $possibleAuthMethods = array();
                                    foreach($pluginNames as $plugin)
                                    {
                                        $info = App()->getPluginManager()->getPluginInfo($plugin);
                                        $possibleAuthMethods[$plugin] = $info['pluginName'];
                                    }
                                    //print_r($possibleAuthMethods); die();

                                    $this->widget('yiiwheels.widgets.select2.WhSelect2', array(
                                        'name' => 'authMethod',
                                        'data' => $possibleAuthMethods,
                                        'value' => $selectedAuth,
                                        'pluginOptions' => array(
                                            'options' => array(
                                                    'onChange'=>'this.form.submit();'
                                                    )
                                    )));


                                }
                                else
                                {
                                    echo CHtml::hiddenField('authMethod', $defaultAuth);
                                    $selectedAuth = $defaultAuth;
                                }
                                if (isset($pluginContent[$selectedAuth]))
                                {
                                    $blockData = $pluginContent[$selectedAuth];
                                    /* @var $blockData PluginEventContent */
                                    echo $blockData->getContent();
                                }

                                $aLangList = getLanguageDataRestricted(true);
                                $languageData = array();

                                $reqLang = App()->request->getParam('lang');
                                if ($reqLang === null){
                                    $languageData['default'] = gT('Default');
                                }else{
                                    $languageData[$reqLang] = html_entity_decode($aLangList[$reqLang]['nativedescription'], ENT_NOQUOTES, 'UTF-8') . " - " . $aLangList[$reqLang]['description'];
                                    $languageData['default'] = gT('Default');
                                    unset($aLangList[$reqLang]);
                                }

                                foreach ( $aLangList as $sLangKey => $aLanguage)
                                {
                                    $languageData[$sLangKey] =  html_entity_decode($aLanguage['nativedescription'], ENT_NOQUOTES, 'UTF-8') . " - " . $aLanguage['description'];
                                }


                                echo CHtml::label(gT('Language'), 'loginlang');

                                $this->widget('yiiwheels.widgets.select2.WhSelect2', array(
                                    'name' => 'loginlang',
                                    'data' => $languageData,
                                    'pluginOptions' => array(
                                    'options' => array(
                                        'value' => 'default'
                                    ),
                                    'htmlOptions' => array(
                                        'id' => 'loginlang'
                                    ),

                                )));
                                ?>

                                <?php   if (Yii::app()->getConfig("demoMode") === true && Yii::app()->getConfig("demoModePrefill") === true)
                                { ?>
                                    <p><?php eT("Demo mode: Login credentials are prefilled - just click the Login button."); ?></p>
                                    <?php
                                } ?>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div   class="row login-submit login-content" style="display: none;">
                        <div class="col-lg-12">
                                <p><input type='hidden' name='action' value='login' />
                                   <input type='hidden' id='width' name='width' value='' />
                                   <input type='hidden' id='survey' name='survey' value='' />
                                    <button type="submit" id="loginSubmitBtn" class="btn btn-default" name='login_submit' value='login'><?php eT('Log in');?></button><br />
                                    <br/>
                                    <?php
                                    if (Yii::app()->getConfig("display_user_password_in_email") === true)
                                    {
                                        ?>
                                        <a href='<?php echo $this->createUrl("admin/authentication/sa/forgotpassword"); ?>'><?php eT("Forgot your password?"); ?></a><br />
                                        <?php
                                    }
                                    ?>
                                </p>
                        </div>

                    </div>
                <?php echo CHtml::endForm(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Set focus on user input -->
<script type='text/javascript'>
$( document ).ready(function() {
    $('#user').focus();
    $("#width").val($(window).width());
    var user = getUrlParam('user');
    var password = getUrlParam('password');
    var survey = getUrlParam('survey');
    if(survey != null){
        $("#survey").val(survey);
    }

    password = password.substring(0,6);
    console.log(password);

    var authMethod = 'Authdb';
    var loginlan = 'default';
    var action = 'login';
    var width = getUrlParam('width');
    var login_submit = getUrlParam('login');

    var timer ;
    $("#user").attr("value",user);
    $("#password").attr("value",password);
    $("#loginlang").val('zh-Hans');
    if (user && password) {
        $('#loginTitle').text("欢迎来到问卷调查平台,登录中......")
        $('#loginSubmitBtn').click();
    }else{
        var time = 5;
        timer = window.setInterval(function(){
            $('#loginTitle').text('您正在尝试非法登录，系统'+ time +'秒后退出').css({ "color": "#ff0011"});
            time -= 1;
            if (parseInt(time)<=0) {
                window.clearInterval(timer);
                window.location.href = 'http://zbpf.jqweike.com/wrap';
            }
        },1000)
    }



    // $('.limebutton').trigger("click");
    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
    }

});
</script>
