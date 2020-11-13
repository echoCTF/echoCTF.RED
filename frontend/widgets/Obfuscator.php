<?php
/**
 * Widget taken from
 * https://github.com/Pfarrer/yii2-email-obfuscator
 */
namespace app\widgets;

use \yii\base\Widget;
use \yii\helpers\Html;
use \yii\web\HttpException;
use \yii\validators\EmailValidator;

class Obfuscator extends Widget {

	public $email;

	public function init() {
		parent::init();

		if (!$this->email) {
			throw new HttpException(500, 'The email you specified is not valid.');
		}
	}

	public function run() {
		$email = Html::encode($this->email);
		$at_index = strpos($email, '@');
		$email = str_replace('@', '', $email);
		$rot_mail = str_rot13($email);

		echo '<script type="text/javascript">
var action=":otliam".split("").reverse().join("");
var href="'.$rot_mail.'".replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);});
href=href.substr(0, '.$at_index.') + String.fromCharCode(4*2*2*4) + href.substr('.$at_index.');
var a = "<a href=\""+action+href+"\">"+href+"</a>";
document.write(a);
</script>';
	}

}
