<?php
namespace asinfotrack\yii2\comments\widgets;

use yii\bootstrap\ActiveForm;
use yii\base\InvalidConfigException;
use asinfotrack\yii2\comments\helpers\CommentsHelper;
use asinfotrack\yii2\comments\models\Comment;
use yii\helpers\Html;

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
	 * @inheritdoc
	 */
	public function init()
	{
		//assert proper model is set
		if ($this->subject === null || !CommentsHelper::isActiveRecord($this->subject, false)) {
			$msg = Yii::t('app', 'Setting the model property of type ActiveRecord is mandatory');
			throw new InvalidConfigException($msg);
		}
		CommentsHelper::hasCommentsBehavior($this->subject, true);

		//assert proper comment model
		if ($this->commentModel === null || !($this->commentModel instanceof Comment)) {
			$msg = Yii::t('app', 'No proper comment model set');
			throw new InvalidConfigException($msg);
		}
	}

	public function run()
	{
		$form = ActiveForm::begin();
		echo $form->errorSummary($this->commentModel);
		echo $form->field($this->commentModel, 'title')->textInput(['maxlength'=>true]);
		echo $form->field($this->commentModel, 'content')->textarea();
		echo Html::submitButton('save');
		ActiveForm::end();
	}


}
