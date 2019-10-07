<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151130_054002_drycalendar_Subsets extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		
		// Create the craft_drycalendar_Subsets table
		craft()->db->createCommand()->createTable('drycalendar_Subsets', array(
				'handle'              => array(),
				'title'               => array(),
				'categoriesToInclude' => array(),
				'categoriesToExclude' => array(),
		), null, true);

		// Add indexes to craft_drycalendar_Subsets
		craft()->db->createCommand()->createIndex('drycalendar_Subsets', 'handle', true);


		// Create the craft_drycalendar_Views table
		craft()->db->createCommand()->createTable('drycalendar_Views', array(
				'subsetId'     => array(),
				'startDateYmd' => array(),
				'endDateYmd'   => array(),
				'htmlBefore'   => array(),
				'htmlAfter'    => array(),
		), null, true);

		// Add indexes to craft_drycalendar_Views
		craft()->db->createCommand()->createIndex('drycalendar_Views', 'subsetId,startDateYmd', false);



		return true;
	}
}
