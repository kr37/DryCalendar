<?php
namespace kr37\drycalendar\records;

use Craft;
use craft\db\ActiveRecord;

class DryCalendar_ViewsRecord
{
	public function getTableName() {
		return 'drycalendar_Views';
	}

	protected function defineAttributes() {
		return array(
			// Craft automatically creates 'id' as an autoincrement
			'subsetId'     => AttributeType::String,
			'startDateYmd' => AttributeType::String,
			'endDateYmd'   => AttributeType::String,
			'htmlBefore'   => array(AttributeType::String, 'column' => ColumnType::Text, 'required' => false),
			'htmlAfter'    => array(AttributeType::String, 'column' => ColumnType::Text, 'required' => false),
		);
	}

	public function defineIndexes() {
		return array(
			array('columns' => array('subsetId', 'startDateYmd' ), 'key' => true),
		);
	}

}
