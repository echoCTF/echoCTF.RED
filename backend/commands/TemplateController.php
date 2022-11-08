<?php
/**
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @copyright 2022
 * @since 0.1
 */

namespace app\commands;

use Yii;
use yii\console\Exception as ConsoleException;
use yii\helpers\Console;
use yii\console\Controller;
use app\modules\models\EmailTemplate;

/**
 * Manages backend templates.
 *
 * @author proditis
 */
class TemplateController extends Controller
{

    /**
     * User list.
     * @param string $filter filter: all, enabled, disabled, pending.
     */
    public function actionEmails()
    {
       $templates=\app\modules\content\models\EmailTemplate::find();
       $baseDir=\Yii::getAlias('@app/mail/');
       foreach($templates->all() as $tmpl)
       {
            file_put_contents($baseDir.$tmpl->name.'-html.php',$tmpl->html);
            file_put_contents($baseDir.$tmpl->name.'-text.php',$tmpl->txt);
       }

    }

}
