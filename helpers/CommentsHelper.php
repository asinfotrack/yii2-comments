<?php
namespace asinfotrack\yii2\comments\helpers;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use asinfotrack\yii2\comments\behaviors\CommentsBehavior;
use asinfotrack\yii2\comments\behaviors\CommentsQueryBehavior;

/**
 * Helper class for basic operations with the comment extension
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license MIT
 */
class CommentsHelper
{

	/**
	 * Handles a comment model when submitted
	 *
	 * @param \asinfotrack\yii2\comments\models\Comment $model
	 * @return bool true if created
	 * @throws \yii\db\Exception
	 */
	public static function handle($model)
	{
		if (!$model->load(Yii::$app->request->post())) return false;
		return $model->save();
	}

	/**
	 * Asserts if a class if of type ActiveRecord
	 *
	 * @param mixed $model the model to check
	 * @param bool $throwException if set to true, an exception will be thrown if not
	 * @return bool true if ok
	 * @throws \yii\base\InvalidParamException
	 */
	public static function isActiveRecord($model, $throwException=false)
	{
		if (!($model instanceof \yii\db\ActiveRecord)) {
			if ($throwException) {
				$msg = Yii::t('app', 'Only objects of type ActiveRecord allowed');
				throw new InvalidParamException($msg);
			} else {
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks if a class has the CommentsBehavior attached
	 *
	 * @param \yii\base\Component $component the component to check
	 * @param bool $throwException if set to true, an exception will be thrown if not
	 * @return bool true if ok
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function hasCommentsBehavior($component, $throwException=false)
	{
		$found = false;
		foreach ($component->behaviors() as $name=>$config) {
			$className = is_array($config) ? $config['class'] : $config;
			if (strcmp($className, CommentsBehavior::className()) === 0) {
				$found = true;
				break;
			}
		}

		if ($found) {
			return true;
		} else if (!$throwException) {
			return false;
		} else {
			$msg = Yii::t('app', 'The component {component} does not have the CommentsBehavior', ['component'=>$component->className()]);
			throw new InvalidConfigException($msg);
		}
	}

	/**
	 * Checks if a class has the CommentsQueryBehavior attached
	 *
	 * @param \yii\base\Component $component the component to check
	 * @param bool $throwException if set to true, an exception will be thrown if not
	 * @return bool true if ok
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function hasCommentsQueryBehavior($component, $throwException=false)
	{
		$found = false;
		foreach ($component->behaviors() as $name=>$config) {
			$className = is_array($config) ? $config['class'] : $config;
			if (strcmp($className, CommentsQueryBehavior::className()) === 0) {
				$found = true;
				break;
			}
		}

		if ($found) {
			return true;
		} else if (!$throwException) {
			return false;
		} else {
			$msg = Yii::t('app', 'The component {component} does not have the CommentsQueryBehavior', ['component'=>$component->className()]);
			throw new InvalidConfigException($msg);
		}
	}

}
