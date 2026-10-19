<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);

?>

<?php $this->beginPage() ?>

<!DOCTYPE html>

<html lang="<?= Yii::$app->language ?>">

<head>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <meta charset="<?= Yii::$app->charset ?>">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <?php $this->registerCsrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <?php
    $this->registerCssFile(
        Yii::getAlias('@web/css/rbis.css'),
        ['depends' => [\yii\web\YiiAsset::class]]
    );
    ?>

    <?php $this->head() ?>

</head>

<body>

<?php $this->beginBody() ?>

<div class="rbis-wrapper">

    <!-- SIDEBAR -->
    <aside class="rbis-sidebar">

        <div class="sidebar-logo">

            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <span class="logo-text">
                RBIS
            </span>

        </div>

        <ul class="sidebar-menu">

            <li>

                <a href="index.php?r=taskboard/index">

                    <i class="fas fa-tasks"></i>

                    <span>
                        Taskboard
                    </span>

                </a>

            </li>

            <li>
                <a href="<?= \yii\helpers\Url::to(['/dashboard/index']) ?>">
                    <i class="fas fa-border-all"></i>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="<?= \yii\helpers\Url::to(['/timeline/index']) ?>">
                    <i class="fas fa-chart-line"></i>
                    Timeline
                </a>
            </li>

            <li>
                <a href="<?= \yii\helpers\Url::to(['/road/index']) ?>">
                    <i class="fas fa-road"></i>
                    Roads Management
                </a>
            </li>

            <li>
                <a href="<?= \yii\helpers\Url::to(['/roadmap/index']) ?>">
                    <i class="fas fa-map"></i>
                    Road Maps
                </a>
            </li>

            <li>
                <a href="<?= \yii\helpers\Url::to(['/report/index']) ?>">
                    <i class="fas fa-file-lines"></i>
                    Reports
                </a>
            </li>

            <?php if (
                !Yii::$app->user->isGuest &&
                Yii::$app->user->identity->role == 'super_admin'
            ): ?>

                <li>
                    <a href="<?= \yii\helpers\Url::to(['/user/index']) ?>">
                        <i class="fas fa-gear"></i>
                        System Administration
                    </a>
                </li>

            <?php endif; ?>

        </ul>

    </aside>

    <!-- MAIN -->
    <main class="rbis-main">

        <!-- TOPBAR -->
        <div class="rbis-topbar">

            <div class="topbar-left">

                <h5>
                    <?= Html::encode($this->title) ?>
                </h5>

            </div>

            <?php if (!Yii::$app->user->isGuest): ?>

                <div class="topbar-user dropdown">

                    <a class="dropdown-toggle user-dropdown"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown">

                        <img src="https://i.pravatar.cc/40"
                             class="user-avatar">

                        <div>

                            <div class="user-name">
                                <?= Yii::$app->user->identity->username ?>
                            </div>

                            <div class="user-role">
                                <?= strtoupper(Yii::$app->user->identity->role) ?>
                            </div>

                        </div>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>

                            <?= Html::beginForm(
                                ['/site/logout'],
                                'post'
                            ) ?>

                            <?= Html::submitButton(
                                'Logout',
                                ['class' => 'dropdown-item']
                            ) ?>

                            <?= Html::endForm() ?>

                        </li>

                    </ul>

                </div>

            <?php endif; ?>

        </div>

        <!-- PAGE CONTENT -->
        <div class="rbis-content">

            <?= $content ?>

        </div>

    </main>

</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php $this->endBody() ?>

</body>

</html>

<?php $this->endPage() ?>