<?php
namespace asinfotrack\yii2\comments\models;

use asinfotrack\yii2\comments\helpers\CommentsHelper;
use asinfotrack\yii2\toolbox\helpers\PrimaryKey;

/**
 * Query class for the comment model
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license MIT
 */
class CommentQuery extends \yii\db\ActiveQuery
{

	/**
	 * Named scope to get entries for a certain model
	 *
	 * @param \yii\db\ActiveRecord $model the model to get the audit trail for
	 * @return \asinfotrack\yii2\comments\models\CommentQuery
	 * @throws \yii\base\InvalidParamException if the model is not of type ActiveRecord
	 * @throws \yii\base\InvalidConfigException if the models pk is empty or invalid
	 */
	public function subject($model)
	{
		CommentsHelper::isActiveRecord($model, true);
		static::validateModel($model);

		$this->modelClass($model::className());
		$this->andWhere(['foreign_pk'=>static::createPrimaryKeyJson($model)]);

		return $this;
	}

	/**
	 * Named scope to filter entries by their model type
	 *
	 * @param string $modelClass full class name of the model
	 * @return \asinfotrack\yii2\comments\models\CommentQuery
	 */
	public function modelClass($modelClass)
	{
		$this->andWhere(['model_class'=>$modelClass]);

		return $this;
	}

	/**
	 * Named scope to sort by either newest (default) or oldest first
	 *
	 * @param bool $isNewestFirst if set to false, sorts by oldest first
	 * @return \asinfotrack\yii2\comments\models\CommentQuery
	 */
	public function orderNewestFirst($isNewestFirst=true)
	{
		$this->orderBy(['created'=>$isNewestFirst ? SORT_DESC : SORT_ASC]);

		return $this;
	}

	/**
	 * Creates the json-representation of the pk (array in the format attribute=>value)
	 * @see \asinfotrack\yii2\toolbox\helpers\PrimaryKey::asJson()
	 *
	 * @param \yii\db\ActiveRecord $model the model to create the pk for
	 * @return string json-representation of the pk-array
	 */
	protected static function createPrimaryKeyJson($model)
	{
		return PrimaryKey::asJson($model);
	}

	/**
	 * Validates that the model is not a new record and has a valid primary key
	 *
	 * @param mixed $model the model to check
	 * @throws \asinfotrack\yii2\comments\models\InvalidParamException
	 */
	protected static function validateModel($model)
	{
		if ($model->isNewRecord) {
			$msg = Yii::t('app', 'Commenting is not possible on unsaved models');
			throw new InvalidParamException($msg);
		}
		if (count($model->primaryKey) === 0) {
			$msg = Yii::t('app', 'The model needs a valid primary key');
			throw new InvalidParamException($msg);
		}
	}

}
