<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use app\overloads\yii\filters\AccessControl;
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
        $parent=parent::behaviors();
        unset($parent['access']['rules']['teamsAccess']);
        return ArrayHelper::merge($parent,[
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index','login','logout', 'changelog', 'register', 'request-password-reset', 'verify-email', 'resend-verification-email',  'captcha'],
                'rules' => [
                    'disabledRegs'=>[
                        'actions'=>['register'],
                        'allow'=>false,
                        'roles'=>['*'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->sys->disable_registration!==false;
                        },
                        'denyCallback' => function ($rule, $action) {
                            return \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage],303);
                        },
                    ],
                    'indexAuth'=>[
                        'actions'=>['index'],
                        'allow'=>false,
                        'roles'=>['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->sys->default_homepage!==false && Yii::$app->sys->default_homepage!=="";
                        },
                        'denyCallback' => function ($rule, $action) {
                            return \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage],303);
                        },
                    ],
                    'eventStartEnd'=>[
                        'actions' => [  'register',  ],
                    ],
                    'eventStart'=>[
                        'actions' => [  'register', ],
                    ],
                    'eventEnd'=>[
                        'actions' => [  'register', 'request-password-reset', 'verify-email', 'resend-verification-email', ],
                    ],
                    'eventActive'=>[
                      'actions' => ['register', 'verify-email', 'resend-verification-email'],
                    ],
                    [
                        'actions' => ['logout','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    'denyAuthAccessToGuest'=>[
                        'actions' => ['register','login','verify-email', 'resend-verification-email','request-password-reset', 'captcha'],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            \Yii::$app->session->setFlash('warning', \Yii::t('app','Only guests can access this area.'));
                            return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage],303);
                          },
                    ],
                    'checkDisabledRegs'=>[
                        'actions' => ['register'],
                        'allow' => false,
                        'matchCallback' => function ($rule, $action) {
                          return Yii::$app->sys->disable_registration===true;
                        },
                        'denyCallback' => function ($rule, $action) {
                          Yii::$app->session->setFlash('info', \Yii::t('app','Registrations are disabled on this competition'));
                          return  \Yii::$app->getResponse()->redirect(['/site/login']);
                        },
                    ],
                    'registrationsStart'=>[
                        'actions' => ['register','request-password-reset', 'captcha'],
                        'allow' => false,
                        'roles' => ['?'],
                        'matchCallback' => function ($rule, $action) {
                          return Yii::$app->sys->registrations_start!==false && time()<=Yii::$app->sys->registrations_start;
                        },
                        'denyCallback' => function ($rule, $action) {
                          if(time()<(int)Yii::$app->sys->registrations_start)
                            Yii::$app->session->setFlash('info', \Yii::t('app',"Registrations haven't started yet."));
                          return  \Yii::$app->getResponse()->redirect(['/site/login']);

                        },
                    ],
                    'registrationsEnd'=>[
                        'actions' => ['register', 'captcha',],
                        'allow' => false,
                        'roles' => ['?'],
                        'matchCallback' => function ($rule, $action) {
                          return Yii::$app->sys->registrations_end!==false && time()>=Yii::$app->sys->registrations_end;
                        },
                        'denyCallback' => function ($rule, $action) {
                        if(time()<(int)Yii::$app->sys->registrations_start)
                            Yii::$app->session->setFlash('info', \Yii::t('app','Registrations are no longer accepted ended.'));
                          return  \Yii::$app->getResponse()->redirect(['/site/login']);

                        },
                    ],
                    'allowGuestUsers'=>[
                      'actions' => ['login','index','register','verify-email','resend-verification-email', 'request-password-reset','captcha', ],
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
                'fixedVerifyCode' => (YII_ENV_TEST || YII_ENV_DEV) ? 'testme' : null,
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
      {
        return $this->redirect([Yii::$app->sys->default_homepage]);
      }

      return $this->render('index');
    }

    /**
     * Displays Maintenance page. If a file exists at `@app/web/dt.html` render it,
     * otherwise render the maintenance view file.
     * Both pages are full HTML, no layouts are applied from the application.
     *
     * @return string
     */
    public function actionMaintenance()
    {
      Yii::$app->response->statusCode = 503;

      if(file_exists(Yii::getAlias('@app/web/dt.html'))!==false)
        return \Yii::$app->view->renderFile(Yii::getAlias('@app/web/dt.html'));

      return $this->renderPartial('maintenance');
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
              if(($player=$model->signup())!==null)
              {
                $transaction->commit();
                if(Yii::$app->sys->require_activation===true)
                {
                  Yii::$app->session->setFlash('success', \Yii::t('app','Thank you for registering. Please check your inbox for the verification email. <small>Make sure you also check the spam or junk folders.</small>'));
                }
                elseif (Yii::$app->user->login($player)) {
                  Yii::$app->session->setFlash('success', \Yii::t('app','Thank you for registering. Your account is activated feel free to login.'));
                }
              }
              return $this->goHome();
          }
        }
        catch(\Exception $e)
        {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            Yii::$app->session->setFlash('error', \Yii::t('app','Registration failed.'));
        }
        catch(\Throwable $e)
        {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            Yii::$app->session->setFlash('error', \Yii::t('app','Registration failed.'));
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
                Yii::$app->session->setFlash('success', \Yii::t('app','Check your email for further instructions. Keep in mind that the token will expire after 24 hours.'));

                return $this->goHome();
            }
            else
            {
                Yii::$app->session->setFlash('error', \Yii::t('app','Sorry, we are unable to reset the password for the provided email address.'));
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
            Yii::$app->session->setFlash('warning', \Yii::t('app','Password reset token not found! If you have changed your password already try to sign-in.'));
            return $this->redirect(['/site/login']);
        }

        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword())
        {
            if(Yii::$app->user->login($model->player))
            {
              Yii::$app->session->setFlash('success', \Yii::t('app','New password saved.'));
            }
            else
            {
              Yii::$app->session->setFlash('warning', Yii::t('app','New password saved but failed to auto sign-in.'));
            }

            return $this->redirect(['/']);
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
            Yii::$app->session->setFlash('warning', \Yii::t('app','Verification token not found! Try to login if you have verified your email already.'));
            return $this->redirect(['/site/login']);
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
                  Yii::$app->session->setFlash('success', \Yii::t('app','Your email has been confirmed!'));
                  return $this->redirect(['/profile/me']);
              }
          }
        }
        catch(\Exception $e)
        {
          $transaction->rollBack();
          die(var_dump($e->getMessage()));
        }

        Yii::$app->session->setFlash('error', \Yii::t('app','Sorry, we are unable to verify an account with the provided token.'));
        return $this->redirect(['/']);
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
                Yii::$app->session->setFlash('success', \Yii::t('app','Check your email for further instructions.'));
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', \Yii::t('app','Sorry, we are unable to resend verification email for the provided address.'));
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
