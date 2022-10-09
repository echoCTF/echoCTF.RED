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
       foreach($templates as $tmpl)
       {
            file_put_contents($tmpl->name.'-html.php',$tmpl->html);
            file_put_contents($tmpl->name.'-text.php',$tmpl->text);
       }

    }

}
