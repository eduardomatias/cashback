<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

$this->title = '';

frontend\assets\DhtmlxAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en-us">	
    <head>
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta name="author" content="">

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <!-- #CSS Links -->
        <!-- Basic Styles -->
        <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">

        <!-- SmartAdmin Styles : Caution! DO NOT change the order -->
        <link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-production-plugins.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-production.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-skins.min.css">

        <!-- DEV links : turn this on when you like to develop directly -->
        <!--<link rel="stylesheet" type="text/css" media="screen" href="../Source_UNMINIFIED_CSS/smartadmin-production.css">-->
        <!--<link rel="stylesheet" type="text/css" media="screen" href="../Source_UNMINIFIED_CSS/smartadmin-skins.css">-->

        <!-- SmartAdmin RTL Support -->
        <link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-rtl.min.css"> 

        <!-- We recommend you use "your_style.css" to override SmartAdmin
             specific styles this will also ensure you retrain your customization with each SmartAdmin update.
        <link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

        <!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->

        <!--<link rel="stylesheet" type="text/css" media="screen" href="css/demo.min.css">-->

        <!-- #FAVICONS -->
        <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">
        <link rel="icon" href="img/favicon/favicon.ico" type="image/x-icon">

        <!-- #GOOGLE FONT -->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

        <!-- #APP SCREEN / ICONS -->
        <!-- Specifying a Webpage Icon for Web Clip 
                 Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
        <link rel="apple-touch-icon" href="img/splash/sptouch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="76x76" href="img/splash/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="120x120" href="img/splash/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="152x152" href="img/splash/touch-icon-ipad-retina.png">

        <!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">

        <!-- Startup image for web apps -->
        <link rel="apple-touch-startup-image" href="img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
        <link rel="apple-touch-startup-image" href="img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
        <link rel="apple-touch-startup-image" href="img/splash/iphone.png" media="screen and (max-device-width: 320px)">
        <!--<link rel="stylesheet" type="text/css" media="screen" href="css/your_style2.css">-->

        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>

        <style>
            .fixed-navigation nav>ul {
                width: 100%;
                overflow-x: hidden;
                overflow-y: hidden;
            }
            #main {
                margin-left: 180px;
                padding: 15px;
                padding-bottom: 52px;
                min-height: 500px;
                position: relative;
            }
            .fixed-header #main {
                margin-top: 49px;
            }
            #header>:first-child, aside {
                width: 180px;
            }
            .container {
                width: 100%;
            }
        </style>

        <script>

            var SYSTEM = cesta = {};

            document.addEventListener("DOMContentLoaded", function (event) {

                var SYSTEM = (function () {
                    
                    $.blockUI.defaults.css.border = 'none';
                    $.blockUI.defaults.css.padding = '0px';
                    $.blockUI.defaults.css.textAlign = 'center';
                    $.blockUI.defaults.css.backgroundColor = 'rgba(8, 4, 4, 1))';
                    $.blockUI.defaults.css.opacity = .3;
                    $.blockUI.defaults.message = '<img src="<?= \Yii::getAlias('@vendor'); ?>/js/layoutMask/loading.svg">';
                    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

                    cesta.boot = function () {
                        SYSTEM.Layout = loadLayout();
                        dhtmlx.image_path = "<?= \Yii::getAlias('@vendor'); ?>/js/dhtmlx/terrace/imgs/";
                        SYSTEM.Toolbar = loadToolbar();
                    }

                    return cesta;
                })();

                function loadLayout() {
                    var outerLayout = new dhtmlXLayoutObject(document.getElementById('layout-view'), "1C");
                    var innerLayout = outerLayout.cells("a").attachLayout("1C");

                    innerLayout.setEffect('resize', true);
                    innerLayout.setEffect('collapse', true);

                    return{
                        innerLayout: innerLayout,
                        outerLayout: outerLayout,
                        tela: innerLayout.cells("a"),
                        //Define título das células
                        tituloCell: function (cell, titulo) {
                            innerLayout.cells(cell).setText(titulo);
                            innerLayout.setCollapsedText(cell, titulo);
                        },
                    }
                }

                function loadToolbar() {
                    var toolbar = SYSTEM.Layout.outerLayout.cells("a").attachToolbar();
                    //Declarando um método apto a ser sobrescrito sob necessidade, para trabalhar com os ícones da toolbar
                    toolbar.doWithItem = function (itemId) {};
                    toolbar.setIconsPath("<?= \Yii::getAlias('@vendor'); ?>/js/layoutMask/imgs/");
                    toolbar.loadXML("<?= \Yii::getAlias('@vendor'); ?>/js/layoutMask/dhxtoolbar.xml?etc=" + new Date().getTime());
                    toolbar.attachEvent("onXLE", function () {
                        toolbar.addSpacer("titulo");
                        toolbar.forEachItem(function (itemId) {
                            toolbar.hideItem(itemId);
                            //Chamando o método genérico para cada item
                            toolbar.doWithItem(itemId);
                        });
                    });

                    return {
                        core: toolbar,
                        icones: function (iconsIds) {
                            setTimeout(function () {
                                for (var i = 0; iconsIds.length > i; i++) {
                                    toolbar.showItem(iconsIds[i]);
                                }
                            }, 1000);
                        },
                        setIconesAcoes: function (iconsIds) {
                            SYSTEM.Layout.icons = iconsIds;
                            setTimeout(function () {
                                $.each(iconsIds[0], function (icon, action) {
                                    toolbar.showItem(icon);
                                });
                            }, 1000);
                        },
                        titulo: function (titulo) {
                            setTimeout(function () {
                                toolbar.showItem('titulo');
                                toolbar.setItemText('titulo', titulo);
                            }, 1000);
                        }
                    }
                }

                // SAIR ----------------------------------------------------
                $("#menu_sair").click(function (e) {
                    $.SmartMessageBox({
                        title: "Deseja sair?",
                        //content : "",
                        buttons: '[Não][Sim]'
                    }, function (ButtonPressed) {
                        if (ButtonPressed === "Sim") {
                            $('form[name=form-sair]').submit();
                        }
                    });
                    return false;
                });

            });

        </script>

    </head>


    <body class="desktop-detected fixed-header fixed-navigation">
<?php $this->beginBody() ?> 

        <header id="header">
            <div id="logo-group">
                <!-- PLACE YOUR LOGO HERE -->
                <span id="logo"> <img src="img/logo.png" alt="SmartAdmin"> </span>
                <!-- END LOGO PLACEHOLDER -->
            </div>
        </header>

        <aside id="left-panel">

            <nav>
                <ul>
                    <li class="top-menu-invisible open active">
                        <a href="#" title="Principal"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">Home</span></a>
                    </li>
                    <li class="">
                        <a href="<?= \yii\helpers\Url::to('index.php?r=estabelecimento/empresa') ?>" title="Empresa"><i class="fa fa-lg fa-fw fa-briefcase"></i> <span class="menu-item-parent">Empresa</span></a>
                    </li>
                    <li class="">
                        <a href="#" title="Empresa"><i class="fa fa-lg fa-fw fa-inbox"></i> <span class="menu-item-parent">Produto</span></a>
                    </li>
                    <?=
                    Html::beginForm(['/estabelecimento/logout'], 'post', ['name' => 'form-sair'])
                     . Html::endForm()
                     . '<li>'
                     . Html::a('<i class="fa fa-lg fa-fw fa-power-off"></i> Sair', '#', ['id' => 'menu_sair'])
                     . '</li>';
                    ?>
                </ul>
            </nav>


            <span class="minifyme" data-action="minifyMenu"> <i class="fa fa-arrow-circle-left hit"></i> </span>

        </aside>

        <!-- #MAIN PANEL -->
        <div id="main" role="main">

            <!-- #MAIN CONTENT -->
            <div class="container" id="main-container">
            <?= $content ?>
            </div>
            <!-- END #MAIN CONTENT -->

        </div>
        <!-- END #MAIN PANEL -->

        <!-- #PAGE FOOTER -->
        <div class="page-footer">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <span class="txt-color-white">

                    </span>
                </div>
            </div>
        </div>
        <!-- END FOOTER -->

        <!--================================================== -->

        <!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)
        <script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>-->


        <!-- #PLUGINS -->

        <script src="js/libs/jquery-2.1.1.min.js"></script>

        <script src="js/libs/jquery-ui-1.10.3.min.js"></script>


        <script src="js/jquery.priceformat.min.js"></script>

        <!-- IMPORTANT: APP CONFIG -->
        <script src="js/app.config.js"></script>

        <!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
        <script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> 

        <!-- BOOTSTRAP JS -->
        <script src="js/bootstrap/bootstrap.min.js"></script>

        <!-- CUSTOM NOTIFICATION -->
        <script src="js/notification/SmartNotification.min.js"></script>

        <!-- JARVIS WIDGETS -->
        <script src="js/smartwidgets/jarvis.widget.min.js"></script>

        <!-- EASY PIE CHARTS -->
        <script src="js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

        <!-- SPARKLINES -->
        <script src="js/plugin/sparkline/jquery.sparkline.min.js"></script>

        <!-- JQUERY VALIDATE -->
        <script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>

        <!-- JQUERY MASKED INPUT -->
        <script src="js/plugin/masked-input/jquery.maskedinput.min.js"></script>

        <!-- JQUERY SELECT2 INPUT -->
        <script src="js/plugin/select2/select2.min.js"></script>

        <!-- JQUERY UI + Bootstrap Slider -->
        <script src="js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

        <!-- browser msie issue fix -->
        <script src="js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

        <!-- FastClick: For mobile devices: you can disable this in app.js -->
        <script src="js/plugin/fastclick/fastclick.min.js"></script>

        <!--[if IE 8]>
                <h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
        <![endif]-->

        <!-- Demo purpose only -->
        <!--<script src="js/demo.min.js"></script>-->

        <!-- MAIN APP JS FILE -->
        <script src="js/app.min.js"></script>

        <!-- ENHANCEMENT PLUGINS : NOT A REQUIREMENT -->
        <!-- Voice command : plugin -->
        <script src="js/speech/voicecommand.min.js"></script>

        <!-- SmartChat UI : plugin -->
        <script src="js/smart-chat-ui/smart.chat.ui.min.js"></script>
        <script src="js/smart-chat-ui/smart.chat.manager.min.js"></script>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>