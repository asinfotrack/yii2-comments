<?php
namespace asinfotrack\yii2\comments\models;

use Yii;
use yii\base\ErrorException;
use yii\base\InvalidCallException;
use asinfotrack\yii2\comments\behaviors\CommentsBehavior;
use asinfotrack\yii2\toolbox\helpers\ComponentConfig;
use asinfotrack\yii2\toolbox\helpers\PrimaryKey;

use yii\base\InvalidParamException;
use yii\helpers\Json;

/**
 * The actual model of a single comment
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license MIT
 *
 * @property integer $id
 * @property string $model_class
 * @property mixed[] $foreign_pk
 * @property integer $created
 * @property integer $created_by
 * @property integer $updated
 * @property integer $updated_by
 * @property string $title
 * @property string $content
 * @property bool $is_published
 *
 * @property \yii\db\ActiveRecord|\asinfotrack\yii2\comments\behaviors\CommentsBehavior $subject
 */
class Comment extends \yii\db\ActiveRecord
{

	/**
	 * @var \yii\db\ActiveRecord|\asinfotrack\yii2\comments\behaviors\CommentsBehavior the model
	 * to which the comment belongs. This must be set manually, when the comment is created!
	 */
	protected $subject;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'comment';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'timestamp'=>[
				'class'=>'\yii\behaviors\TimestampBehavior',
				'createdAtAttribute'=>'created',
				'updatedAtAttribute'=>'updated',
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['subject'], 'required', 'when'=>function ($model, $attribute) { return $model->isNewRecord; }],

			[['created_by'], 'default', 'value'=>function ($model, $attribute) {
				return Yii::$app->user->isGuest ? null : Yii::$app->user->id;
			}],

			[['is_published'], 'default', 'value'=>1],

			[['model_class','foreign_pk','content','is_published'], 'required'],

			[['model_class','title'], 'string', 'max'=>255],
			[['created','updated'], 'integer'],
			[['is_published'], 'boolean'],

			[['content'], 'safe'],

			[['created_by', 'updated_by'], 'exist', 'targetClass'=>Yii::$app->user->identity->className(), 'targetAttribute'=>'id'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'=>Yii::t('app', 'ID'),
			'model_class'=>Yii::t('app', 'Model class'),
			'foreign_pk'=>Yii::t('app', 'Foreign PK'),
			'created'=>Yii::t('app', 'Created'),
			'created_by'=>Yii::t('app', 'Created by'),
			'updated'=>Yii::t('app', 'Updated'),
			'updated by'=>Yii::t('app', 'Updated by'),
			'title'=>Yii::t('app', 'Title'),
			'content'=>Yii::t('app', 'Comment content'),
			'is_published'=>Yii::t('app', 'Is published'),

			'subject'=>Yii::t('app', 'Subject'),
		];
	}

	/**
	 * @inheritdoc
	 *
	 * @return \asinfotrack\yii2\comments\models\CommentQuery
	 */
	public static function find()
	{
		return new CommentQuery(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function afterFind()
	{
		$this->foreign_pk = Json::decode($this->foreign_pk);
		parent::afterFind();
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if (!parent::beforeSave($insert)) {
			return false;
		}
		$this->foreign_pk = Json::encode($this->foreign_pk);
		return true;
	}


	/**
	 * Getter for the subject model
	 *
	 * @return \yii\db\ActiveRecord the subject of this comment
	 * @throws \yii\base\ErrorException
	 */
	public function getSubject()
	{
		if (!$this->isNewRecord && $this->subject === null) {
			$this->subject = call_user_func([$this->model_class, 'findOne'], $this->foreign_pk);
			if ($this->subject === null) {
				$msg = Yii::t('app', 'Could not find model for attachment `{attachment}`', [
					'attachment'=>$this->id
				]);
				throw new ErrorException($msg);
			}
		}
		return $this->subject;
	}

	/**
	 * Sets the subject-model for this comment
	 *
	 * @param \yii\db\ActiveRecord $subject the subject model
	 */
	public function setSubject($subject)
	{
		self::validateSubject($subject, true);

		$this->model_class = $subject->className();
		$this->foreign_pk = $subject->getPrimaryKey(true);
		$this->subject = $subject;
	}

	/**
	 * Validates if the model is an active record, has the comments behavior, has a primary key and is not a new record.
	 *
	 * @param \yii\db\ActiveRecord $subject the subject model
	 * @param bool $throwException
	 * @return bool false means this subject is invalid
	 * @throws \yii\base\InvalidConfigException|\yii\base\InvalidParamException
	 */
	public static function validateSubject($subject, $throwException = true)
	{
		if (!ComponentConfig::isActiveRecord($subject, $throwException)) return false;
		if (!ComponentConfig::hasBehavior($subject, CommentsBehavior::className(), $throwException)) return false;

		if (count($subject->primaryKey) === 0) {
			if (!$throwException) return false;
			$msg = Yii::t('app', 'The model needs a valid primary key');
			throw new InvalidParamException($msg);
		}

		if ($subject->isNewRecord) {
			if (!$throwException) return false;
			$msg = Yii::t('app', 'Commenting is not possible on unsaved models');
			throw new InvalidParamException($msg);
		}

		return true;
	}
}
