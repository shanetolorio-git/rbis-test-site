<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';

$bgUrl = Yii::getAlias('@web') . '/assets/images/login_background.jpg';

$this->registerCss("

body {
    background-image: url('{$bgUrl}');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    margin: 0;
}

.site-login {
    min-height: 100vh;
    width: 100%;
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 24px 16px;
}

.login-card {
    width: 560px;
    max-width: 100%;
    box-sizing: border-box;
    background: rgba(255, 255, 255, 0.90);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 18px;
    padding: 32px 36px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
    text-align: center;
}

.login-logo {
    width: 100px;
    max-width: 100%;
    margin-bottom: 12px;
}

.login-title {
    font-size: 38px;
    font-weight: bold;
    color: #17375E;
    margin-bottom: 14px;
}

.rbis-title {
    font-size: 25px;
    font-weight: bold;
    color: #17375E;
}

.version {
    font-size: 17px;
    margin-bottom: 24px;
}

.login-card .form-control {
    width: 100%;
    box-sizing: border-box;
    height: 44px;
    font-size: 14px;
    border-radius: 6px;
}

.login-btn {
    width: 100%;
    height: 46px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 6px;
}

.social-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px 0 14px;
    color: #555;
    font-size: 12px;
    font-weight: 600;
}

.social-divider::before,
.social-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(23, 55, 94, 0.25);
}

.social-login-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.social-login-btn {
    width: 100%;
    min-height: 44px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.google-btn {
    background: #fff;
    color: #333;
    border: 1px solid #ccc;
}

.google-btn:hover {
    background: #f2f2f2;
    color: #222;
}

.yahoo-btn {
    background: #6001d2;
    color: #fff;
    border: 1px solid #6001d2;
}

.yahoo-btn:hover {
    background: #4b00a8;
    color: #fff;
}

.google-icon {
    font-size: 20px;
    font-weight: 800;
    background: conic-gradient(
        from -45deg,
        #4285f4 0deg 90deg,
        #34a853 90deg 180deg,
        #fbbc05 180deg 250deg,
        #ea4335 250deg 360deg
    );
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
}

.yahoo-icon {
    font-size: 18px;
    font-weight: 800;
    color: #fff;
}

@media (max-width: 600px) {
    .site-login {
        padding: 16px 12px;
    }

    .login-card {
        padding: 26px 20px;
    }

    .login-logo {
        width: 85px;
    }

    .login-title {
        font-size: 32px;
    }

    .rbis-title {
        font-size: 21px;
    }

    .version {
        font-size: 16px;
        margin-bottom: 20px;
    }
}

");
?>

<div class="site-login">

    <div class="login-card">

        <img
            src="<?= Yii::getAlias('@web/assets/images/logo_dilg.png') ?>"
            class="login-logo"
        >

        <div class="login-title">

            Login

        </div>

        <div class="rbis-title">

            ROAD AND BRIDGE INFORMATION SYSTEM (RBIS)

        </div>

        <div class="version">

            Test Site v.1.0

        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form'
        ]); ?>

            <?= $form
                ->field($model, 'username')
                ->textInput([
                    'placeholder' => 'Username or Email Address',
                    'autofocus' => true,
                    'autocomplete' => 'username',
                ])
                ->label(false)
            ?>

            <?= $form
                ->field($model, 'password')
                ->passwordInput([
                    'placeholder' => 'Password'
                ])
                ->label(false)
            ?>

            <?= Html::submitButton(
                'LOGIN',
                [
                    'class' =>
                        'btn btn-primary login-btn',
                    'name' =>
                        'login-button'
                ]
            ) ?>

        <?php ActiveForm::end(); ?>

                <div class="social-divider">
            <span>OR SIGN IN WITH</span>
        </div>

        <div class="social-login-buttons">

            <?= Html::a(
                '<span class="google-icon">G</span> Continue with Google',
                ['/site/auth', 'authclient' => 'google'],
                [
                    'class' => 'btn social-login-btn google-btn',
                    'encode' => false,
                ]
            ) ?>

            <?= Html::a(
                '<span class="yahoo-icon">Y!</span> Continue with Yahoo',
                ['/site/auth', 'authclient' => 'yahoo'],
                [
                    'class' => 'btn social-login-btn yahoo-btn',
                    'encode' => false,
                ]
            ) ?>

        </div>

    </div>

</div>