<?php
namespace kr37\drycalendar\services;

use Craft;
use craft\base\Component;

use kr37\drycalendar\DryCalendar as Plugin;

class SettingsService extends Component
{

	public static function getMySetting() {
		return Plugin::$plugin->getSettings()->mySetting;
	}

	public static function getCpCssFile() {
		return Plugin::$plugin->getSettings()->cpCssFile;
	}

	public function getCategoryFieldHandle() {
		return Plugin::$plugin->getSettings()->categoryFieldHandle;
	}

	public function getStartDateFieldHandle() {
		return Plugin::$plugin->getSettings()->startDateFieldHandle;
	}

	public function getImageFieldHandle() {
		return Plugin::$plugin->getSettings()->imageFieldHandle;
	}

	public function getCssFieldHandle() {
		return Plugin::$plugin->getSettings()->cssFieldHandle;
	}

  	public function getAvailableTimes() {
		return Plugin::$plugin->getSettings()->availableTimes;
	}

	public function getStatus() {
		return Plugin::$plugin->getSettings()->status;
	}

	public function getCategoriesToExclude() {
		return Plugin::$plugin->getSettings()->categoriesToExclude;
	}

	public function getCategoriesToInclude() {
		return Plugin::$plugin->getSettings()->categoriesToInclude;
	}

	public function getTitle() {
		return Plugin::$plugin->getSettings()->title;
	}

	public function getShowTails() {
		return Plugin::$plugin->getSettings()->showTails;
	}

	public function getFiller1() {
		return Plugin::$plugin->getSettings()->filler1;
	}

	public function getFiller2() {
		return Plugin::$plugin->getSettings()->filler2;
	}

	public function getRowOfDaysFormat() {
		return Plugin::$plugin->getSettings()->rowOfDaysFormat;
	}

	public function getNodate() {
		return Plugin::$plugin->getSettings()->nodate;
	}

	public function getDateformat() {
		return Plugin::$plugin->getSettings()->dateformat . 'D j';
	}

	public function getDateformat1st() {
		return Plugin::$plugin->getSettings()->dateformat1st;
	}

	public function getOccurrenceFormat() {
		return Plugin::$plugin->getSettings()->occurrenceFormat;
	}

	public function getUrlFieldHandle() {
		return Plugin::$plugin->getSettings()->urlFieldHandle;
	}

	public function getEntryCalendarTextFieldHandle() {
		return Plugin::$plugin->getSettings()->entryCalendarTextFieldHandle;
	}

	public function getTimezone() {
		return Plugin::$plugin->getSettings()->timezone;
	}

	public function getSections() {
		return Plugin::$plugin->getSettings()->sections;
	}

	}
