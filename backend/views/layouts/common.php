<?php
/**
 * @author Eugine Terentev <eugine@terentev.net>
 * @author Victor Gonzalez <victor@vgr.cl>
 * @var \yii\web\View $this
 * @var string $content
 */

use backend\assets\BackendAsset;
use backend\modules\system\models\SystemLog;
use backend\widgets\MainSidebarMenu;
use common\models\TimelineEvent;
use yii\bootstrap4\Alert;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\log\Logger;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Html;
use rmrevin\yii\fontawesome\FAR;
use rmrevin\yii\fontawesome\FAS;

$bundle = BackendAsset::register($this);
Yii::info(Yii::$app->components["i18n"]["translations"]['*']['class'], 'test');

$logEntries = [
    [
        'label' => Yii::t('backend', 'You have {num} log items', ['num' => SystemLog::find()->count()]),
        'linkOptions' => ['class' => ['dropdown-item', 'dropdown-header']]
    ],
    '<div class="dropdown-divider"></div>',
];
foreach (SystemLog::find()->orderBy(['log_time' => SORT_DESC])->limit(5)->all() as $logEntry) {
    $logEntries[] = [
        'label' => FAS::icon('exclamation-triangle', ['class' => [$logEntry->level === Logger::LEVEL_ERROR ? 'text-red' : 'text-yellow']]). ' '. $logEntry->category,
        'url' => ['/system/log/view', 'id' => $logEntry->id]
    ];
    $logEntries[] = '<div class="dropdown-divider"></div>';
}

$logEntries[] = [
    'label' => Yii::t('backend', 'View all'),
    'url' => ['/system/log/index'],
    'linkOptions' => ['class' => ['dropdown-item', 'dropdown-footer']]
];
?>

<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
<div class="wrapper">
    <!-- navbar -->
    <?php NavBar::begin([
        'renderInnerContainer' => false,
        'options' => [
            'class' => ['main-header', 'navbar', 'navbar-expand', 'navbar-dark'],
        ],
    ]); ?>

        <!-- left navbar links -->
        <?php echo Nav::widget([
            'options' => ['class' => ['navbar-nav']],
            'encodeLabels' => false,
            'items' => [
                [
                    // sidebar menu toggler
                    'label' => FAS::icon('bars'),
                    'url' => '#',
                    'options' => [
                        'data' => ['widget' => 'pushmenu'],
                        'role' => 'button',
                    ]
                ],
            ]
        ]); ?>
        <!-- /left navbar links -->

        <!-- right navbar links -->
        <?php echo Nav::widget([
            'options' => ['class' => ['navbar-nav', 'ml-auto']],
            'encodeLabels' => false,
            'items' => [
                [
                    // timeline events
                    'label' => FAR::icon('bell').' <span class="badge badge-success navbar-badge">'.TimelineEvent::find()->today()->count().'</span>',
                    'url'  => ['/timeline-event/index']
                ],
                [
                    // log events
                    'label' => FAR::icon('flag').' <span class="badge badge-warning navbar-badge">'.SystemLog::find()->count().'</span>',
                    'url' => '#',
                    'linkOptions' => ['class' => ['no-caret']],
                    'dropdownOptions' => [
                        'class' => ['dropdown-menu', 'dropdown-menu-lg', 'dropdown-menu-right'],
                    ],
                    'items' => $logEntries,
                ],
                [
                    // control sidebar button
                    'label' => FAS::icon('th-large'),
                    'url'  => '#',
                    'linkOptions' => [
                        'data' => ['widget' => 'control-sidebar', 'slide' => 'true'],
                        'role' => 'button'
                    ],
                    'visible' => isset($this->params['control-sidebar']),
                ],
            ]
        ]); ?>
        <!-- /right navbar links -->

    <?php NavBar::end(); ?>
    <!-- /navbar -->

    <!-- main sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- brand logo -->
        <a href="/" class="brand-link text-center">
            <!-- <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                style="opacity: .8"> -->
            <span class="brand-text font-weight-bold"><?php echo Yii::$app->name ?></span>
        </a>
        <!-- /brand logo -->

        <!-- sidebar -->
        <div class="sidebar">
            <!-- sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <?php echo Html::img(
                        Yii::$app->user->identity->userProfile->getAvatar('/img/anonymous.png'),
                        ['class' => ['img-circle', 'elevation-2', 'bg-white'], 'alt' => 'User image']
                    ) ?>
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?php echo Yii::$app->user->identity->publicIdentity ?></a>
                </div>
            </div>
            <!-- /sidebar user panel -->

            <!-- sidebar menu -->
            <nav class="mt-2">
                <?php echo MainSidebarMenu::widget([
                    'items' => [
                        [
                            'label' => Yii::t('backend', 'Main'),
                            'options' => ['class' => 'nav-header'],
                        ],
                        [
                            'label' => Yii::t('backend', 'Timeline'),
                            'icon' => FAS::icon('stream', ['class' => ['nav-icon']]),
                            'url' => ['/timeline-event/index'],
                            'badge' => TimelineEvent::find()->today()->count(),
                            'badgeBgClass' => 'badge-success',
                        ],
                        [
                            'label' => Yii::t('backend', 'Users'),
                            'icon' => FAS::icon('users', ['class' => ['nav-icon']]),
                            'url' => ['/user/index'],
                            'active' => Yii::$app->controller->id === 'user',
                            'visible' => Yii::$app->user->can('administrator'),
                        ],
                        [
                            'label' => Yii::t('backend', 'Content'),
                            'options' => ['class' => 'nav-header'],
                        ],
                        [
                            'label' => Yii::t('backend', 'Static pages'),
                            'url' => ['/content/page/index'],
                            'icon' => FAS::icon('thumbtack', ['class' => ['nav-icon']]),
                            'active' => Yii::$app->controller->id === 'page',
                        ],
                        [
                            'label' => Yii::t('backend', 'Articles'),
                            'url' => '#',
                            'icon' => FAS::icon('newspaper', ['class' => ['nav-icon']]),
                            'options' => ['class' => 'nav-item has-treeview'],
                            'active' => 'content' === Yii::$app->controller->module->id &&
                                ('article' === Yii::$app->controller->id || 'category' === Yii::$app->controller->id),
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Articles'),
                                    'url' => ['/content/article/index'],
                                    'icon' => FAR::icon('circle', ['class' => ['nav-icon']]),
                                    'active' => Yii::$app->controller->id === 'article',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Categories'),
                                    'url' => ['/content/category/index'],
                                    'icon' => FAR::icon('circle', ['class' => ['nav-icon']]),
                                    'active' => Yii::$app->controller->id === 'category',
                                ],
                            ],
                        ],
                        [
                            'label' => Yii::t('backend', 'Widgets'),
                            'url' => '#',
                            'icon' => FAS::icon('puzzle-piece', ['class' => ['nav-icon']]),
                            'options' => ['class' => 'nav-item has-treeview'],
                            'active' => Yii::$app->controller->module->id === 'widget',
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Text Blocks'),
                                    'url' => ['/widget/text/index'],
                                    'icon' => FAR::icon('circle', ['class' => ['nav-icon']]),
                                    'active' => Yii::$app->controller->id === 'text',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Menu'),
                                    'url' => ['/widget/menu/index'],
                                    'icon' => FAR::icon('circle', ['class' => ['nav-icon']]),
                                    'active' => Yii::$app->controller->id === 'menu',
                                ],
                                [
                                    'label' => Yii::t('backend', 'Carousel'),
                                    'url' => ['/widget/carousel/index'],
                                    'icon' => FAR::icon('circle', ['class' => ['nav-icon']]),
                                    'active' => in_array(Yii::$app->controller->id, ['carousel', 'carousel-item']),
                                ],
                            ],
                        ],
                        [
                            'label' => Yii::t('backend', 'Translation'),
                            'options' => ['class' => 'nav-header'],
                            'visible' => Yii::$app->components["i18n"]["translations"]['*']['class'] === \yii\i18n\DbMessageSource::class,
                        ],
                        [
                            'label' => Yii::t('backend', 'Translation'),
                            'url' => ['/translation/default/index'],
                            'icon' => FAS::icon('language', ['class' => ['nav-icon']]),
                            'active' => (Yii::$app->controller->module->id == 'translation'),
                            'visible' => Yii::$app->components["i18n"]["translations"]['*']['class'] === \yii\i18n\DbMessageSource::class,
                        ],
                        [
                            'label' => Yii::t('backend', 'System'),
                            'options' => ['class' => 'nav-header'],
                        ],
                        [
                            'label' => Yii::t('backend', 'RBAC Rules'),
                            'url' => '#',
                            'icon' => FAS::icon('user-shield', ['class' => ['nav-icon']]),
                            'options' => ['class' => 'nav-item has-treeview'],
                            'active' => (Yii::$app->controller->module->id == 'rbac'),
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Assignments'),
                                    'url' => ['/rbac/rbac-auth-assignment/index'],
                                    'icon' => FAR::icon('circle', ['class' => ['nav-icon']]),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Items'),
                                    'url' => ['/rbac/rbac-auth-item/index'],
                                    'icon' => FAR::icon('circle', ['class' => ['nav-icon']]),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Child Items'),
                                    'url' => ['/rbac/rbac-auth-item-child/index'],
                                    'icon' => FAR::icon('circle', ['class' => ['nav-icon']]),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Rules'),
                                    'url' => ['/rbac/rbac-auth-rule/index'],
                                    'icon' => FAR::icon('circle', ['class' => ['nav-icon']]),
                                ],
                            ],
                        ],
                        [
                            'label' => Yii::t('backend', 'Files'),
                            'url' => '#',
                            'icon' => FAS::icon('folder-open', ['class' => ['nav-icon']]),
                            'options' => ['class' => 'nav-item has-treeview'],
                            'active' => (Yii::$app->controller->module->id == 'file'),
                            'items' => [
                                [
                                    'label' => Yii::t('backend', 'Storage'),
                                    'url' => ['/file/storage/index'],
                                    'icon' => FAS::icon('database', ['class' => ['nav-icon']]),
                                    'active' => (Yii::$app->controller->id == 'storage'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Manager'),
                                    'url' => ['/file/manager/index'],
                                    'icon' => FAS::icon('archive', ['class' => ['nav-icon']]),
                                    'active' => (Yii::$app->controller->id == 'manager'),
                                ],
                            ],
                        ],
                        [
                            'label' => Yii::t('backend', 'Key-Value Storage'),
                            'url' => ['/system/key-storage/index'],
                            'icon' => FAS::icon('exchange-alt', ['class' => ['nav-icon']]),
                            'active' => (Yii::$app->controller->id == 'key-storage'),
                        ],
                        [
                            'label' => Yii::t('backend', 'Cache'),
                            'url' => ['/system/cache/index'],
                            'icon' => FAS::icon('sync', ['class' => ['nav-icon']]),
                        ],
                        [
                            'label' => Yii::t('backend', 'System Information'),
                            'url' => ['/system/information/index'],
                            'icon' => FAS::icon('tachometer-alt', ['class' => ['nav-icon']]),
                        ],
                        [
                            'label' => Yii::t('backend', 'Logs'),
                            'url' => ['/system/log/index'],
                            'icon' => FAS::icon('exclamation-triangle', ['class' => ['nav-icon']]),
                            'badge' => SystemLog::find()->count(),
                            'badgeBgClass' => 'badge-danger',
                        ],
                    ],
                ]) ?>
            </nav>
            <!-- /sidebar menu -->
        </div>
        <!-- /sidebar -->
    </aside>
    <!-- /main sidebar -->

    <!-- content wrapper -->
    <div class="content-wrapper" style="min-height: 402px;">
        <!-- content header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark"><?php echo Html::encode($this->title) ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <?php echo Breadcrumbs::widget([
                            'tag' => 'ol',
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                            'options' => ['class' => ['breadcrumb', 'float-sm-right']]
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /content header -->

        <!-- main content -->
        <section class="content">
            <div class="container-fluid">
                <?php if (Yii::$app->session->hasFlash('alert')) : ?>
                    <?php echo Alert::widget([
                        'body' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                        'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                    ]) ?>
                <?php endif; ?>
                <?php echo $content ?>
            </div>
        </section>
        <!-- /main content -->
    </div>
    <!-- /content wrapper -->

    <!-- footer -->
    <footer class="main-footer">
        <strong>&copy; My Company <?php echo date('Y') ?></strong>
        <div class="float-right d-none d-sm-inline-block"><?php echo Yii::powered() ?></div>
    </footer>
    <!-- /footer -->

    <?php if (isset($this->params['control-sidebar'])) : ?>
    <!-- control sidebar -->
    <div class="control-sidebar control-sidebar-dark">
        <div class="control-sidebar-content p-3">
            <?php echo $this->params['control-sidebar'] ?>
        </div>
    </div>
    <!-- /control sidebar -->
    <?php endif; ?>
</div>
<?php $this->endContent(); ?>
