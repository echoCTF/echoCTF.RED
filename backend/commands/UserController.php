<?php
/**
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @copyright 2019
 * @since 0.1
 */

namespace app\commands;

use Yii;
use yii\console\Exception as ConsoleException;
use yii\helpers\Console;
use yii\console\Controller;
use app\models\User;

/**
 * Manages backend users.
 *
 * @author proditis
 */
class UserController extends Controller
{

    /**
     * User list.
     * @param string $filter filter: all, enabled, disabled, pending.
     */
    public function actionIndex($filter='all')
    {
        $filters=['all', 'enabled', 'disabled'];
        if(!in_array($filter, $filters))
        {
            throw new ConsoleException(Yii::t('app', 'Filter accepts values: {values}', ['values' => implode(',', $filters)]));
        }

        $users=User::find();
        switch($filter) {
            case 'enabled':
                $users->where(['status' => User::STATUS_ACTIVE]);
                break;

            case 'disabled':
                $users->where(['status' => User::STATUS_INACTIVE]);
                break;

        }

        $this->userList($users->all());
    }

    /**
     * Finds user by email or user name.
     * @param string $pattern search pattern.
     */
    public function actionFind($pattern)
    {
        $users=User::find()
            ->where(['like', 'email', $pattern])
            ->where(['like', 'name', $pattern])
            ->all();
        $this->userList($users);
    }

    /**
     * Creates a new user.
     * @param string $name user name
     * @param string $email user email
     * @param string $password uncrypted password, if skipped random password will be generated.
     */
    public function actionCreate($name, $email, $password='',$admin=true)
    {
        if(empty($password))
        {
          $random=Yii::$app->security->generateRandomString(8);
        }
        else
        {
          $random=$password;
        }
        $user=new User();
        $user->username=$name;
        $user->email=$email;
        $user->admin=$admin;
        $user->status=User::STATUS_ACTIVE;
        $user->generateAuthKey();
        $user->setPassword($random);
        if($user->save())
        {
            $this->p('User "{name}" has been created.', ['name' => $user->username]);
            if(empty($password))
            {
                $this->p('Random password "{password}" has been generated.', ['password' => $random]);
            }
        }
        else
        {
            $this->err('Couldn\'t create user.');
            foreach($user->getErrors() as $attribute => $error)
            {
                print reset($error).PHP_EOL;
            }
        }
    }

    /**
     * Delete a user.
     * @param string $email user email
     */
    public function actionDelete($email)
    {
        $user=$this->findUser($email);
        if(!$this->confirm('Are you sure to delete user "'.$user->email.'"'))
        {
            return;
        }
        if($user->delete() !== false)
        {
            $this->p('User deleted.');
        }
        else
        {
            $this->err('Couldn\'t delete user.');
        }
    }
    /**
     * Deleted user.
     * @param string $email user email
     */
    public function actionDeleted($email)
    {
        $user=$this->findUser($email);
        if($user->status === User::STATUS_DELETED)
        {
            throw new ConsoleException(Yii::t('app', 'User "{email}" already deleted.', compact('email')));
        }
        $user->status=User::STATUS_DELETED;
        if($user->save())
        {
            $this->p('User "{email}" deleted.', compact('email'));
        }
    }

    /**
     * Disable user.
     * @param string $email user email
     */
    public function actionDisable($email)
    {
        $user=$this->findUser($email);
        if($user->status === User::STATUS_INACTIVE)
        {
            throw new ConsoleException(Yii::t('app', 'User "{email}" already disabled.', compact('email')));
        }
        $user->status=User::STATUS_INACTIVE;
        if($user->save())
        {
            $this->p('User "{email}" disabled.', compact('email'));
        }
    }

    /**
     * Enable user.
     * @param string $email user email
     */
    public function actionEnable($email)
    {
        $user=$this->findUser($email);
        if($user->status === User::STATUS_ACTIVE)
        {
            throw new ConsoleException(Yii::t('app', 'User "{email}" already enabled.', compact('email')));
        }
        $user->status=User::STATUS_ACTIVE;
        if($user->save())
        {
            $this->p('User "{email}" enabled.', compact('email'));
        }
    }

    /**
     * Change user password.
     * @param string $email user email
     * @param string $new_password uncrypted password, if skipped random password will be generated.
     */
    public function actionPassword($email, $new_password='')
    {
        $user=$this->findUser($email);
        if(empty($new_password))
        {
          $random=Yii::$app->security->generateRandomString(8);
        }
        else
        {
          $random=$new_password;
        }
        $user->setPassword($random);
        if($user->save())
        {
          if(empty($new_password))
          {
            $this->p('Password has been changed to random "{random}"', compact('random'));
          }
          else
          {
            $this->p('Password has been changed.');
          }
        }
    }

    /**
     * Get User model.
     * @param string $email
     * @return User
     * @throws \yii\console\Exception
     */
    protected function findUser($email)
    {
        if(!($user=User::findOne(['email'=>$email])))
        {
            throw new ConsoleException(Yii::t('app', 'User not found.'));
        }
        return $user;
    }

    /**
     * @param User[] $users
     */
    protected function userList(array $users)
    {
        if(empty($users))
        {
            $this->p('No users found.');
            return;
        }

        $this->stdout(sprintf("%4s %-32s %-24s %-16s %-8s\n", 'ID', 'Email address', 'User name', 'Created', 'Status'), Console::BOLD);
        $this->stdout(str_repeat('-', 94).PHP_EOL);

        foreach($users as $user)
        {
            printf("%4d %-32s %-24s %-16s %-8s\n",
                    $user->id,
                    $user->email,
                    $user->username,
                    date('Y-m-d H:i', $user->created_at),
                    $user->getStatusLabel()
            );
        }
    }
    public function p($message, array $params=[])
    {
        $this->stdout(Yii::t('app', $message, $params).PHP_EOL);
    }

    public function err($message, array $params=[])
    {
        $this->stderr(Yii::t('app', $message, $params).PHP_EOL);
    }
}
