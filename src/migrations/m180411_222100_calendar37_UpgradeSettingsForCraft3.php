<?php
namespace kr37\drycalendar\migrations;

use craft\db\Migration;

class Install extends Migration
{
	public function safeUp()
	{
		if ($this->_upgradeFromCraft2()) {
			return;
		}

		// Fresh install code goes here...
	}

	private function _upgradeFromCraft2()
	{
		// Fetch the old plugin row, if it was installed
		$row = (new \craft\db\Query())
			->select(['id', 'settings'])
			->from(['{{%plugins}}'])
			->where(['in', 'handle', ['drycalendar', 'drycalendar']])
			->one();

		if (!$row) {
		   	return false;
	    }

		// Update this one's settings to old values

		$this->update('{{%plugins}}', [
			'settings' => $row['settings']
	    ], ['handle' =>  'drycalendar']);

		// Delete the old row

		$this->delete('{{%plugins}}', ['id' => $row['id']]);

		// Any additional upgrade code goes here...

		return true;
	}

	public function safeDown()
	{
		//
    }
}
