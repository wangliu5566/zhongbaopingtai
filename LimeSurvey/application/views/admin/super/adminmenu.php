<?php
/**
 * This view render the main menu bar, with configuration menu
 * @var $sitename
 * @var $activesurveyscount
 * @var $dataForConfigMenu
 */
?>

<!-- admin menu bar -->
<nav class="navbar">
    <div class="navbar-header">
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#small-screens-menus">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <a class="navbar-brand" href="<?php echo $this->createUrl("/admin/survey/sa/listsurveys"); ?>">
            <?php echo $sitename; ?>
        </a>
    </div>


    <!-- Only on xs screens -->
    <div class="collapse navbar-collapse pull-left hidden-sm  hidden-md hidden-lg" id="small-screens-menus">
        <ul class="nav navbar-nav hidden-sm  hidden-md hidden-lg">

            <li><br/><br/></li>

            <!-- List surveys -->
            <li>
                <a href="<?php echo $this->createUrl("admin/survey/sa/listsurveys"); ?>">
                    <?php eT("List surveys");?>
                </a>
            </li>

            <!-- Logout -->
            <!--<li>
                <a href="<?php /*echo $this->createUrl("admin/authentication/sa/logout"); */?>">
                    <?php /*eT("Logout");*/?>
                </a>
            </li>-->
        </ul>
    </div>

    <div class="collapse navbar-collapse js-navbar-collapse pull-right">
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <div style="position: relative;display: block;padding-top: 15px;padding-right:15px;">
                    <span class="icon-user" ></span> <?php echo Yii::app()->session['user'];?>
                </div>
                <!--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" ><span class="icon-user" ></span> --><?php /*echo Yii::app()->session['user'];*/?> <!--<span class="caret"></span>--><!--</a>-->
                <!--<ul class="dropdown-menu" role="menu">-->
                    <!--<li>
                        <a href="<?php /*echo $this->createUrl("/admin/user/sa/personalsettings"); */?>"><?php /*eT("Your account");*/?></a>
                    </li>-->

                    <!--<li class="divider"></li>--><!--<li class="divider"></li>-->

                    <!-- Logout -->
                    <!--<li>
                        <a href="<?php /*echo $this->createUrl("admin/authentication/sa/logout"); */?>">
                            <?php /*eT("Logout");*/?>
                        </a>
                    </li>-->
                <!--</ul>-->
            </li>

            <!-- Admin notification system -->
            <?php //echo $adminNotifications; ?>

        </ul>
    </div><!-- /.nav-collapse -->
</nav>
