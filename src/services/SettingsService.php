<?php
namespace kr37\drycalendar\services;

use Craft;
use craft\base\Component;

use kr37\drycalendar\DryCalendar as Plugin;

class SettingsService extends Component
{

	public function getMySetting() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->mySetting;
	}

	public function getCpCssFile() {
		//
		$settings = Plugin::$plugin->getSettings();
		return $settings->cpCssFile;
	}

	public function getCategoryFieldHandle() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->categoryFieldHandle;
	}

	public function getStartDateFieldHandle() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->startDateFieldHandle;
	}

	public function getImageFieldHandle() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->imageFieldHandle;
	}

	public function getCssFieldHandle() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->cssFieldHandle;
	}

  	public function getAvailableTimes() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->availableTimes;
	}

	public function getStatus() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->status;
	}

	public function getCategoriesToExclude() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->categoriesToExclude;
	}

	public function getCategoriesToInclude() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->categoriesToInclude;
	}

	public function getTitle() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->title;
	}

	public function getShowTails() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->showTails;
	}

	public function getFiller1() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->filler1;
	}

	public function getFiller2() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->filler2;
	}

	public function getRowOfDaysFormat() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->rowOfDaysFormat;
	}

	public function getNodate() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->nodate;
	}

	public function getDateformat() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->dateformat . 'D j';
	}

	public function getDateformat1st() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->dateformat1st;
	}

	public function getOccurrenceFormat() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->occurrenceFormat;
	}

	public function getUrlFieldHandle() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->urlFieldHandle;
	}

	public function getEntryCalendarTextFieldHandle() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->entryCalendarTextFieldHandle;
	}

	public function getTimezone() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->timezone;
	}

	public function getSections() {

		$settings = Plugin::$plugin->getSettings();
		return $settings->sections;
	}

	}
