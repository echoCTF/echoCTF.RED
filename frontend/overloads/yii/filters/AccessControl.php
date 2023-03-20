<?php
namespace app\overloads\yii\filters;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * AccessControl Overloads the Yii2 default so that we can control the default error message
 *
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @since 0.23.0
 */
class AccessControl extends \yii\filters\AccessControl
{
    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     * @param User|false $user the current user or boolean `false` in case of detached User component
     * @throws ForbiddenHttpException if the user is already logged in or in case of detached User component.
     */
    protected function denyAccess($user)
    {
        if ($user !== false && $user->getIsGuest()) {
            $user->loginRequired();
        } else {
            //Yii::$app->session->setFlash('warning',Yii::t('yii', 'You are not allowed to perform this action.'));
            //Yii::$app->response->redirect(Yii::$app->request->referrer ?: [Yii::$app->sys->default_homepage])->send();
            //return;
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }
}