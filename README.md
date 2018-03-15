# Yii2-comments
Yii2-comments is a behavior and a set of widgets to enable commenting on Yii2-ActiveRecord-Models

## Advantages
This is not the first commenting extension. So why use this one? Those are some of the major advantages:

* this extension works with composite primary keys
* the handling and configuration couldn't be simpler
* awesome widgets to show, list and add comments

## Installation
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require asinfotrack/yii2-comments
```

or add

```
"asinfotrack/yii2-comments": "~0.8.0"
```

to the `require` section of your `composer.json` file.


## Migration
	
After downloading everything you need to apply the migration creating the audit trail entry table:

	yii migrate --migrationPath=@vendor/asinfotrack/yii2-comments/migrations
	
To remove the table just do the same migration downwards.

## Usage

#### Behavior
Attach the behavior to your model and you're done:

```php
public function behaviors()
{
    return [
    	// ...
    	'comments'=>[
    		'class'=>CommentBehavior::className(),
    		
    		//TODO: comment this
    	],
    	// ...
    ];
}
```

### Widget
The widget is also very easy to use. Just provide the model to get the audit trail for:

```php
<?= Comments::widget([
	'model'=>$model,
	
	// some of the optional configurations
	//TODO: comment this
]) ?>
```
