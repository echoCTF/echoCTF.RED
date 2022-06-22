<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\models\forms\LoginForm;
use app\models\forms\SignupForm;
use app\models\forms\ResendVerificationEmailForm;
use app\models\forms\VerifyEmailForm;
use app\models\forms\PasswordResetRequestForm;
use app\models\forms\ResetPasswordForm;

class SiteController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login','logout', 'changelog', 'register', 'request-password-reset', 'verify-email', 'resend-verification-email', 'changelog', 'captcha'],
                'rules' => [
                    'eventActive'=>[
                      'actions' => ['register', 'verify-email', 'resend-verification-email'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['register','login','verify-email', 'resend-verification-email','request-password-reset', 'index','captcha'],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            \Yii::$app->session->setFlash('warning', 'Only guests can access this area.');
                            return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage],303);
                          },
                    ],
                    'teamsAccess'=>[
                       'actions' => ['index'],
                       'roles' => ['@'],
                    ],
                    [
                        'actions' => ['register'],
                        'allow' => false,
                        'matchCallback' => function ($rule, $action) {
                          return Yii::$app->sys->disable_registration===true;
                        },
                        'denyCallback' => function ($rule, $action) {
                          Yii::$app->session->setFlash('info', 'Registrations are disabled on this competition');
                          return  \Yii::$app->getResponse()->redirect(['/site/login']);
                        },
                    ],
                    [
                        'actions' => ['register', 'verify-email', 'resend-verification-email'],
                        'allow' => false,
                        'roles' => ['?'],
                        'matchCallback' => function ($rule, $action) {
                          return Yii::$app->sys->registrations_start!==false && (time()<=Yii::$app->sys->registrations_start || time()>=Yii::$app->sys->registrations_end);
                        },
                        'denyCallback' => function ($rule, $action) {
                          if(time()<(int)Yii::$app->sys->registrations_start)
                            Yii::$app->session->setFlash('info', 'Registrations havent started yet.');
                          else
                            Yii::$app->session->setFlash('info', 'Registrations are closed.');
                          return  \Yii::$app->getResponse()->redirect(['/site/login']);

                        },
                    ],
                    [
                      'actions' => ['login','index','register','verify-email', 'resend-verification-email','captcha', 'request-password-reset',],
                      'allow' => true,
                      'roles'=>['?']
                    ],
                    [
                      'actions' => ['changelog'],
                      'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'app\widgets\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'offset' => 2,
                'minLength' => 7,
                'maxLength' => 7,
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
      if(!Yii::$app->user->isGuest && Yii::$app->sys->default_homepage!==false && Yii::$app->sys->default_homepage!=="")
          $this->redirect([Yii::$app->sys->default_homepage]);
      return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if(!Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        $model=new LoginForm();
        if($model->load(Yii::$app->request->post()) && $model->login())
        {
            return $this->goBack();
        }

        $model->password='';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionRegister()
    {

        $model=new SignupForm();
        $transaction=Yii::$app->db->beginTransaction();
        try
        {
          if($model->load(Yii::$app->request->post()))
          {
              $model->signup();
              $transaction->commit();
              Yii::$app->session->setFlash('success', 'Thank you for registering. Your account is activated feel free to login.');
              if(Yii::$app->sys->require_activation===true)
              {
                Yii::$app->session->setFlash('success', 'Thank you for registering. Please check your inbox for the verification email. <small>Make sure you also check the spam or junk folders.</small>');
              }
              return $this->goHome();
          }
        }
        catch(\Exception $e)
        {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Registration failed.');
        }
        catch(\Throwable $e)
        {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Registration failed.');
        }
        $referred=false;
        if(Yii::$app->getSession()->get('referred_by')!==null && \app\models\Player::findOne(Yii::$app->getSession()->get('referred_by')))
        {
          $referred=\app\models\Player::findOne(Yii::$app->getSession()->get('referred_by'));
        }
        return $this->render('signup', [
            'model' => $model,
            'referred'=>$referred,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model=new PasswordResetRequestForm();
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            if($model->sendEmail())
            {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions. Keep in mind that the token will expire after 24 hours.');

                return $this->goHome();
            }
            else
            {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset the password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try
        {
            $model=new ResetPasswordForm($token);
        }
        catch(InvalidArgumentException $e)
        {
            throw new BadRequestHttpException($e->getMessage());
        }

        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword())
        {
            if(Yii::$app->user->login($model->player))
            {
              Yii::$app->session->setFlash('success', 'New password saved.');
            }
            else
            {
              Yii::$app->session->setFlash('warning', 'New password saved but failed to auto sign-in.');
            }

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail($token)
    {
        try
        {
            $model=new VerifyEmailForm($token);
        }
        catch(InvalidArgumentException $e)
        {
            throw new BadRequestHttpException($e->getMessage());
        }
        $post=Yii::$app->request->post('VerifyEmailForm');
        $value=ArrayHelper::getValue($post, 'token');

        if($value !== $token)
        {
            return $this->render('verify-email', ['model'=>$model, 'token'=>$token]);
        }
        $transaction=Yii::$app->db->beginTransaction();
        try
        {
          if($user=$model->verifyEmail())
          {
              if(Yii::$app->user->login($user))
              {
                  $transaction->commit();
                  Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                  return $this->redirect(['/profile/me']);
              }
          }
        }
        catch(\Exception $e)
        {
          $transaction->rollBack();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model=new ResendVerificationEmailForm();
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            if($model->sendEmail())
            {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
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

        return $this->goHome();
    }

    public function actionChangelog()
    {
      $changelog=@file_get_contents('../Changelog.md');
      $todo=@file_get_contents('../TODO.md');
      return $this->render('changelog', [
        'changelog'=>$changelog,
        'todo'=>$todo
      ]);
    }
}
