<?php
namespace asinfotrack\yii2\comments\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use asinfotrack\yii2\comments\behaviors\CommentsBehavior;
use asinfotrack\yii2\toolbox\helpers\ComponentConfig;

/**
 * This widget renders the comments of a model implementing CommentsBehavior
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license MIT
 */
class Comments extends \yii\base\Widget
{

	/**
	 * @var \asinfotrack\yii2\comments\models\Comment[] the loaded comment models
	 */
	protected $comments;

	/**
	 * @var \yii\db\ActiveRecord|\asinfotrack\yii2\comments\behaviors\CommentsBehavior the model to fetch the comments for.
	 * Make sure the model has CommentsBehavior attached.
	 */
	public $subject;

	/**
	 * @var array the options for the container tag of the widget
	 */
	public $options = [];

	/**
	 * @var array|\Closure the options for the comment-containers or a closure retuning an array
	 * of options (format: function($model))
	 */
	public $commentOptions = [];

	/**
	 * @var bool whether or not to render newest or oldest first (default: true)
	 */
	public $newestFirst = true;

	/**
	 * @var string optional view-alias which will be used to render comments if set.
	 * Within the view the comment-model is available via `$model` variable and the comment
	 * options via `$options`.
	 */
	public $commentView;

	/**
	 * @var string the title tag to use within a single comment
	 */
	public $commentTitleTag = 'h4';

	/**
	 * @var bool whether or not to encode the title of a comment
	 */
	public $encodeCommentTitle = true;

	/**
	 * @var bool whether or not to encode the content of a comment
	 */
	public $encodeCommentContents = true;

	/**
	 * @var bool whether or not to use the bootstrap media object classes within the comments. This
	 * flag is only relevant if no custom commentView is set.
	 */
	public $useBootstrapClasses = true;

	/**
	 * @var \Closure if set this anonymous function will be called to render the author of a comment.
	 * Make sure the callback has the signature `function ($user_id)` and returns a string. The string
	 * will not be encoded and can contain a hyperlink to the authors profile
	 */
	public $authorCallback;

	/**
	 * @inheritdoc
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public function init()
	{
		//assert proper model is set
		if ($this->subject === null || !ComponentConfig::isActiveRecord($this->subject, false)) {
			$msg = Yii::t('app', 'Setting the model property of type ActiveRecord is mandatory');
			throw new InvalidConfigException($msg);
		}
		ComponentConfig::hasBehavior($this->subject, CommentsBehavior::className(), true);

		//load the comments
		$this->loadComments();

		//prepare options
		Html::addCssClass($this->options, 'widget-comments');
		Html::addCssClass($this->commentOptions, 'comment');
	}

	/**
	 * Loads the comments
	 */
	protected function loadComments()
	{
		$this->comments = $this->subject->getComments($this->newestFirst);
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		echo Html::beginTag('div', $this->options);

			if (count($this->comments) === 0) {
				echo Html::tag('span', Yii::t('app', 'No comments yet!'), ['class'=>'no-comments']);
			} else {
				foreach ($this->comments as $comment) {
					if ($this->commentView === null) {
						$this->outputComment($comment);
					} else {
						echo $this->render($this->commentView, ['model' => $comment, 'options' => $this->commentOptions]);
					}
				}
			}

		echo Html::endTag('div');
	}

	/**
	 * Outputs a single comment
	 *
	 * @param $comment \asinfotrack\yii2\comments\models\Comment the comment model
	 */
	protected function outputComment($comment)
	{
		if ($this->commentOptions instanceof \Closure) {
			$options = call_user_func($this->commentOptions, $comment);
		} else {
			$options = $this->commentOptions;
		}
		$options = ArrayHelper::merge($options, ['data-comment-id'=>$comment->id]);
		if ($this->useBootstrapClasses) Html::addCssClass($options, 'media');

		//render comment
		echo Html::beginTag('div', $options);

			//body
			$wrapperOptions = ['class'=>'comment-wrapper'];
			if ($this->useBootstrapClasses) Html::addCssClass($wrapperOptions, 'media-body');
			echo Html::beginTag('div', $wrapperOptions);

				//title
				if (!empty($comment->title)) {
					$titleOptions = ['class'=>'comment-title'];
					if ($this->useBootstrapClasses) Html::addCssClass($titleOptions, 'media-heading');
					$title = $this->encodeCommentTitle ? Html::encode($comment->title) : $comment->title;
					echo Html::tag($this->commentTitleTag, $title, $titleOptions);
				}

				//content
				$content = $this->encodeCommentContents ? Html::encode($comment->content) : $comment->content;
				echo Html::tag('div', $content, ['class'=>'comment-content']);

				//meta
				echo Html::beginTag('dl', ['class'=>'comment-meta']);
				echo Html::tag('dt', Yii::t('app', 'Created'));
				echo Html::tag('dd', Yii::$app->formatter->asDatetime($comment->created));
				if (!empty($comment->updated) && $comment->updated != $comment->created) {
					echo Html::tag('dt', Yii::t('app', 'Updated'));
					echo Html::tag('dd', Yii::$app->formatter->asDatetime($comment->updated));
				}
				if (!empty($comment->user_id)) {
					$author = $this->authorCallback === null ? $comment->created_by : call_user_func($this->authorCallback, $comment->created_by);
					echo Html::tag('dt', Yii::t('app', 'Author'));
					echo Html::tag('dd', $author);
				}
				echo Html::endTag('dl');

			echo Html::endTag('div');
		echo Html::endTag('div');
	}

}
