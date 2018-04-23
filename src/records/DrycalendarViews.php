<?php
namespace kr37\drycalendar\records;

use Craft;
use craft\db\ActiveRecord;
use craft\db\ActiveQuery;

class DrycalendarViews extends ActiveRecord
{
	public function getTableName() {
		return '{{%drycalendar_Views}}';
	}

	protected function defineAttributes() {
		return array(
			// Craft automatically creates 'id' as an autoincrement
			'subsetId'     => AttributeType::String,
			'startDateYmd' => AttributeType::String,
			'endDateYmd'   => AttributeType::String,
			//'htmlBefore'   => [AttributeType::String, 'column' => ColumnType::Text, 'required' => false],
			//'htmlAfter'    => [AttributeType::String, 'column' => ColumnType::Text, 'required' => false],
			'htmlBefore'   => AttributeType::String,
			'htmlAfter'    => AttributeType::String,
		);
	}

	public function defineIndexes() {
		return array(
			array('columns' => array('subsetId', 'startDateYmd' ), 'key' => true),
		);
	}

}
