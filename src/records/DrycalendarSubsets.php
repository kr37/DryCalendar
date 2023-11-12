<?php
namespace kr37\drycalendar\records;

use Craft;
use craft\db\ActiveRecord;

class DrycalendarSubsets extends ActiveRecord
{
    public static function getTableName() :string
    {
		return '{{%drycalendar_subsets}}';
	}

/*class DryCalendar_SubsetsRecord extends BaseRecord
{
	public function getTableName() {
		return 'drycalendar_subsets';
	}
*/

	protected function defineAttributes() { 
		return array(
			// Craft automatically creates 'id' as an autoincrement
			'handle'              => AttributeType::String,
			'title'               => AttributeType::String,
			'categoriesToInclude' => AttributeType::String,
			'categoriesToExclude' => AttributeType::String,
			);
	}
	
	public function defineIndexes() {
		return array(
			array('columns' => array('handle'), 'unique' => true),
		);
	}
	

}
