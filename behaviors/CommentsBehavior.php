<?php
namespace asinfotrack\yii2\comments\behaviors;

use asinfotrack\yii2\comments\models\Comment;
use asinfotrack\yii2\toolbox\helpers\ComponentConfig;
/**
 * The behavior which will be attached to the actual model class in order to
 * enable commenting functionality on it
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license MIT
 *
 * @property \yii\db\ActiveRecord $owner
 *
 * @property \asinfotrack\yii2\comments\models\Comment[] $comments
 * @property bool $hasComments
 * @property integer $numComments
 */
class CommentsBehavior extends \yii\base\Behavior
{

	/**
	 * @var bool if set to false, the oldest comments will be displayed on top
	 */
	public $newestFirst = true;

	/**
	 * @inheritdoc
	 */
	public function attach($owner)
	{
		//validate proper owner config
		ComponentConfig::isActiveRecord($owner, true);

		parent::attach($owner);
	}

	/**
	 * Fetches comments related to the owner of this behavior
	 *
	 * @param bool $newestFirst if true, the newest records will be first (default: true)
	 * @param integer $limit the max number of comments to return (default: all)
	 * @param integer $offset the offset to start from (default: no offset)
	 * @return \asinfotrack\yii2\comments\models\Comment[] the comments
	 */
	public function getComments($newestFirst=true, $limit=null, $offset=null)
	{
		$query = Comment::find()->subject($this->owner);

		$query->orderNewestFirst($newestFirst);
		if ($limit !== null) $query->limit($limit);
		if ($offset !== null) $query->offset($offset);

		return $query->all();
    }

	/**
	 * Returns if the subject has comments
	 *
	 * @return bool true if there are comments
	 */
	public function getHasComments()
	{
		return Comment::find()->subject($this->owner)->exists();
	}

	/**
	 * Returns the number of comments the subject has
	 *
	 * @return int|string
	 */
	public function getNumComments()
	{
		return Comment::find()->subject($this->owner)->count();
	}

}
