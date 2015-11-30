<?php
namespace asinfotrack\yii2\comments\models;

use Yii;
use yii\base\InvalidCallException;
use asinfotrack\yii2\comments\behaviors\CommentsBehavior;
use asinfotrack\yii2\toolbox\helpers\ComponentConfig;
use asinfotrack\yii2\toolbox\helpers\PrimaryKey;

/**
 * The actual model of a single comment
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license MIT
 *
 * @property integer $id
 * @property string $model_class
 * @property string $foreign_pk
 * @property integer $created
 * @property integer $created_by
 * @property integer $updated
 * @property integer $updated_by
 * @property string $title
 * @property string $content
 * @property bool $is_published
 */
class Comment extends \yii\db\ActiveRecord
{

	/**
	 * @var \yii\db\ActiveRecord|\asinfotrack\yii2\comments\behaviors\CommentsBehavior the model
	 * to which the comment belongs. This must be set manually, when the comment is created!
	 */
	protected $subjectModel;

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
			[['subjectModel'], 'required', 'when'=>function ($model, $attribute) { return $model->isNewRecord; }],

			[['created_by'], 'default', 'value'=>function ($model, $attribute) {
				return Yii::$app->user->isGuest ? null : Yii::$app->user->id;
			}],

			[['is_published'], 'default', 'value'=>1],

			[['model_class','foreign_pk','content','is_published'], 'required'],

			[['model_class','foreign_pk','title'], 'string', 'max'=>255],
			[['content'], 'string', 'max'=>255],
			[['created','updated'], 'integer'],
			[['is_published'], 'boolean'],

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
			'content'=>Yii::t('app', 'Content'),
			'is_published'=>Yii::t('app', 'Is published'),
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
	 * Returns the subject model
	 *
	 * @return \asinfotrack\yii2\comments\behaviors\CommentsBehavior|\yii\db\ActiveRecord
	 */
	public function getSubjectModel()
	{
		return $this->subjectModel;
	}

	/**
	 * Sets the subject model
	 *
	 * @param \yii\db\ActiveRecord $subjectModel
	 */
	public function setSubjectModel($subjectModel)
	{
		//validate subject model
		ComponentConfig::isActiveRecord($subjectModel, true);
		ComponentConfig::hasBehavior($subjectModel, CommentsBehavior::className(), true);

		//only on unsaved comments
		if (!$this->isNewRecord) {
			$msg = Yii::t('app', 'The subject model can only be set manually on unsaved comments');
			throw new InvalidCallException($msg);
		}

		//set values from subject model
		$this->model_class = $subjectModel->className();
		$this->foreign_pk = PrimaryKey::asJson($subjectModel);

		$this->subjectModel = $subjectModel;
	}

}
