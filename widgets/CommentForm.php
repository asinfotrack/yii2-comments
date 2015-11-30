<?php
namespace asinfotrack\yii2\comments\widgets;

use Yii;
use yii\bootstrap\ActiveForm;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use asinfotrack\yii2\toolbox\helpers\ComponentConfig;
use asinfotrack\yii2\comments\models\Comment;
use asinfotrack\yii2\comments\behaviors\CommentsBehavior;

/**
 * Renders the form to submit comments to a model
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license MIT
 */
class CommentForm extends \yii\base\Widget
{

	/**
	 * @var \yii\db\ActiveRecord|\asinfotrack\yii2\comments\behaviors\CommentsBehavior the model to create the comment.
	 * for. Make sure the comment has CommentsBehavior attached.
	 */
	public $subject;

	/**
	 * @var \asinfotrack\yii2\comments\models\Comment the comment model
	 */
	public $commentModel;

	/**
	 * @var array options for the text-area
	 */
	public $textAreaOptions = ['rows'=>5];

	/**
	 * @var string the label for the submit button (defaults to 'save')
	 */
	public $buttonLabel;

	/**
	 * @var array options for the submit button
	 */
	public $buttonOptions = ['class'=>'btn btn-primary'];

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		//assert proper model is set
		if ($this->subject === null || !ComponentConfig::isActiveRecord($this->subject, false)) {
			$msg = Yii::t('app', 'Setting the model property of type ActiveRecord is mandatory');
			throw new InvalidConfigException($msg);
		}
		ComponentConfig::hasBehavior($this->subject, CommentsBehavior::className(), true);

		//assert proper comment model
		if ($this->commentModel === null || !($this->commentModel instanceof Comment)) {
			$msg = Yii::t('app', 'No proper comment model set');
			throw new InvalidConfigException($msg);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		$form = ActiveForm::begin();
		echo $form->errorSummary($this->commentModel);
		echo $form->field($this->commentModel, 'title')->textInput(['maxlength'=>true]);
		echo $form->field($this->commentModel, 'content')->textarea($this->textAreaOptions);
		echo Html::submitButton(empty($this->buttonLabel) ? Yii::t('app', 'Save') : $this->buttonLabel, $this->buttonOptions);
		ActiveForm::end();
	}

}
