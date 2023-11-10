<?php
namespace kr37\drycalendar\records;

use Craft;
use craft\db\ActiveRecord;

class Drycalendar extends ActiveRecord
{
    public static function getTableName() :string
    {
		return '{{%drycalendar}}';
	}

	protected function defineAttributes() {
		return array(
			// Craft automatically creates 'id' as an autoincrement
			'event_id'   => AttributeType::Number,
			'dateYmd'    => AttributeType::String,
			'timestr'    => AttributeType::String,
			'alt_text'   => AttributeType::String,
			'css_class'  => AttributeType::String,
			'userJson'   => AttributeType::String,
		);
	}

	public function defineIndexes() {
		return array(
			array('columns' => array('event_id', 'dateYmd'), 'key' => true),
		);
	}

}
