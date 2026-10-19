<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\authclient\AuthAction;
use yii\web\ForbiddenHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'auth'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'auth' => [
                'class' => AuthAction::class,
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['site/login']);
    }

    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();

        $email = strtolower(trim($attributes['email'] ?? ''));

        // Accept the verification field returned by the Yii2 Google client.
        $emailVerified = $attributes['email_verified']
            ?? $attributes['verified_email']
            ?? false;

        if (
            $email === ''
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || !in_array($emailVerified, [true, 1, '1', 'true'], true)
        ) {
            throw new \yii\web\ForbiddenHttpException(
                'Google did not provide a verified email address.'
            );
        }

        // Only allow sign-in for an existing RBIS account.
        $user = \common\models\User::findByEmail($email);

        if ($user === null) {
            throw new \yii\web\ForbiddenHttpException(
                'This email is not registered as an active RBIS account.'
            );
        }

        if (Yii::$app->user->login($user)) {
            return;
        }

        throw new \yii\web\ForbiddenHttpException(
            'Unable to sign in to RBIS.'
        );
    }
}
