<?php

use yii\console\Exception;
use yii\db\Schema;
use yii\db\Expression;

/**
 * Migration to create or remove comment table
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license MIT
 */
class m150617_175717_comment_table extends \yii\db\Migration
{

	/**
	 * @inheritdoc
	 */
	public function up()
	{
		$this->createTable('{{%comment}}', [
			'id'=>$this->primaryKey(),
			'model_class'=>$this->string()->notNull(),
			'foreign_pk'=>$this->string()->notNull(),
			'created'=>$this->integer()->notNull(),
			'created_by'=>$this->integer(),
			'updated'=>$this->integer(),
			'updated_by'=>$this->integer(),
			'title'=>$this->string(),
			'content'=>$this->text()->notNull(),
			'is_published'=>'TINYINT(1) NOT NULL',
		]);
		$this->createIndex('IN_comment_fast_access', '{{%comment}}', [
			new Expression('`model_class` ASC'),
			new Expression('`foreign_pk` ASC'),
			new Expression('`created` DESC'),
		]);
		$this->addForeignKey('FK_comment_user_created',	'{{%comment}}', ['created_by'], '{{%user}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK_comment_user_updated',	'{{%comment}}', ['updated_by'], '{{%user}}', ['id'], 'SET NULL', 'CASCADE');
	}

	/**
	 * @inheritdoc
	 */
	public function down()
	{
		$this->dropTable('{{%comment}}');
	}

}
