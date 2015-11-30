<?php
namespace asinfotrack\yii2\comments\helpers;

use Yii;

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

}
