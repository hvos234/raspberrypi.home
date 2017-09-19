<?php

namespace app\models;

use Yii;
use yii\base\Model;

// Models
use app\models\Rule;
use app\models\Condition;
use app\models\Setting;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

class RuleDate extends Model {
	
	public static function models(){
		// the array key must be the same as the id
		$models = [
			'd' => (object) ['id' => 'd', 'name' => 'Day of the month, 2 digits with leading zeros (01 to 31)'],
			//'date(\'j\')' => 'Day of the month without leading zeros (1 to 31)',
			'D' => (object) ['id' => 'D', 'name' => 'A textual representation of a day, three letters (Mon through Sun)'],
			'l' => (object) ['id' => 'l', 'name' => 'A full textual representation of the day of the week (Sunday through Saturday)'],
			'N' => (object) ['id' => 'N', 'name' => 'ISO-8601 numeric representation of the day of the week (1 (for Monday) through 7 (for Sunday))'],
			//'date(\'w\')' => 'Numeric representation of the day of the week (0 (for Sunday) through 6 (for Saturday))',
			//'date(\'z\')' => 'The day of the year (starting from 0 through 365)',
			'W' => (object) ['id' => 'W', 'name' => 'ISO-8601 week number of year, weeks starting on Monday (42 is the 42nd week in the year)'],
			'm' => (object) ['id' => 'm', 'name' => 'Numeric representation of a month, with leading zeros (01 through 12)'],
			//'date(\'n\')' => 'Numeric representation of a month, without leading zeros (1 through 12)',
			'F' => (object) ['id' => 'F', 'name' => 'A full textual representation of a month (January through December)'],
			'M' => (object) ['id' => 'M', 'name' => 'A short textual representation of a month, three letters (Jan through Dec)'],
			///'date(\'t\')' => 'Number of days in the given month (28 through 31)',
			'Y' => (object) ['id' => 'Y', 'name' => 'A full numeric representation of a year, 4 digits (1999 or 2003)'],
			//'date(\'y\')' => 'A two digit representation of a year (99 or 03)',
			///'date(\'L\')' => 'Whether it's a leap year (1 if it is a leap year, 0 otherwise)',
			///'date(\'o\')' => 'ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead (1999 or 2003)',
			///'date(\'a\')' => 'Lowercase Ante meridiem and Post meridiem (am or pm)',
			///'date(\'A\')' => 'Uppercase Ante meridiem and Post meridiem (AM or PM)',
			///'date(\'B\')' => 'Swatch Internet time (000 through 999)',
			//'date(\'g\')' => '12-hour format of an hour without leading zeros (1 through 12)',
			//'date(\'G\')' => '24-hour format of an hour without leading zeros (0 through 23)',
			//'date(\'h\')' => '12-hour format of an hour with leading zeros (01 through 12)',
			///'date(\'u\')' => 'Microseconds (654321)',
			//'s' => (object) ['id' => 's', 'name' => 'Seconds, with leading zeros (00 through 59)'],
			'i' => (object) ['id' => 'i', 'name' => 'Minutes with leading zeros (00 to 59)'],
			//'i:s' => (object) ['id' => 'i:s', 'name' => 'Minutes (00 to 59) : Seconds (00 through 59)'],
			'H' => (object) ['id' => 'H', 'name' => '24-hour format of an hour with leading zeros (00 through 23)'],
			'H:i' => (object) ['id' => 'H:i', 'name' => '24-hour (00 through 23) : Minutes (00 to 59)'],
			//'H:i:s' => (object) ['id' => 'H:i:s', 'name' => '24-hour (00 through 23) : Minutes (00 to 59) : Seconds (00 through 59)'],
			'd H' => (object) ['id' => 'd H', 'name' => 'Day (01 to 31) 24-hour (00 through 23)'],
			'd H:i' => (object) ['id' => 'd H:i', 'name' => 'Day (01 to 31) 24-hour (00 through 23) : Minutes (00 to 59)'],
			//'d H:i:s' => (object) ['id' => 'd H:i:s', 'name' => 'Day of the month (01 to 31) 24-hour (00 through 23) : Minutes (00 to 59) : Seconds (00 through 59)'],
			'm-d' => (object) ['id' => 'm-d', 'name' => 'Month (01 through 12) - Day (01 to 31)'],
			'm-d H' => (object) ['id' => 'm-d H', 'name' => 'Month (01 through 12) - Day (01 to 31) 24-hour (00 through 23)'],
			'm-d H:i' => (object) ['id' => 'm-d H:i', 'name' => 'Month (01 through 12) - Day (01 to 31) 24-hour (00 through 23) : Minutes (00 to 59)'],
			//'m-d H:i:s' => (object) ['id' => 'm-d H:i:s', 'name' => 'Month (01 through 12) - Day (01 to 31) 24-hour (00 through 23) : Minutes (00 to 59) : Seconds (00 through 59)'],
			'Y-m-d' => (object) ['id' => 'Y-m-d', 'name' => 'Year (1999 or 2003) - Month (01 through 12) - Day (01 to 31)'],
			'Y-m-d H' => (object) ['id' => 'Y-m-d H', 'name' => 'Year (1999 or 2003) - Month (01 through 12) - Day (01 to 31) 24-hour (00 through 23)'],
			'Y-m-d H:i' => (object) ['id' => 'Y-m-d H:i', 'name' => 'Year (1999 or 2003) - Month (01 through 12) - Day (01 to 31) 24-hour (00 through 23) : Minutes (00 to 59)'],
			//'Y-m-d H:i:s' => (object) ['id' => 'Y-m-d H:i:s', 'name' => 'Year (1999 or 2003) - Month (01 through 12) - Day (01 to 31) 24-hour (00 through 23) : Minutes (00 to 59) : Seconds (00 through 59)'],
		];
		
		// add example of the date
		foreach ($models as $key => $model){
			$models[$key]->name = '(' . date($key) . '), ' . $model->name;
		}
		
		return $models;
	}
	
	public static function all(){
			return RuleDate::models();
	}
	
	public static function one($id){
		$models = RuleDate::all();
		foreach($models as $model){
			if($model->id == $id){
				return $model;
			}
		}
		return false;
	}
	
	/*public static function getAllIdName(){
		return ArrayHelper::map(RuleDate::all(), 'id', 'name');
	}*/
    
    public static function ids(){
        $ids = RuleDate::all();
        
        return ArrayHelper::map($ids, 'id', 'name');
    }
	
	public static function execute($id){
		$model = RuleDate::one($id);
		
		Yii::info('date($model->id): ' . json_encode(date($model->id)), 'RuleDate');
		echo('date($model->id): ' . json_encode(date($model->id))) . '<br/>' . PHP_EOL;
		
		return date($model->id);
	}
	
	public static function ruleCondition($id){
		return RuleDate::ruleExecute($id);
	}
	
	public static function ruleAction($id){
		return RuleDate::ruleExecute($id);
	}
	
	public static function ruleExecute($id){
		return HelperData::dataExplode(RuleDate::execute($id));
	}
}