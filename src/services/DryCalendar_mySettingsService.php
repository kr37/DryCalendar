<?php
namespace Craft;

class DryCalendar_mySettingsService extends BaseApplicationComponent
{

	public function getMySetting() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->mySetting;
	}

	public function getCpCssFile() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->cpCssFile;
	}

	public function getCategoryFieldHandle() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->categoryFieldHandle;
	}

	public function getStartDateFieldHandle() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->startDateFieldHandle;
	}

	public function getImageFieldHandle() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->imageFieldHandle;
	}

	public function getCssFieldHandle() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->cssFieldHandle;
	}

  	public function getAvailableTimes() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->availableTimes;
	}

	public function getStatus() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->status;
	}

	public function getCategoriesToExclude() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->categoriesToExclude;
	}

	public function getCategoriesToInclude() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->categoriesToInclude;
	}

	public function getTitle() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->title;
	}

	public function getShowTails() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->showTails;
	}

	public function getFiller1() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->filler1;
	}

	public function getFiller2() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->filler2;
	}

	public function getRowOfDaysFormat() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->rowOfDaysFormat;
	}

	public function getNodate() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->nodate;
	}

	public function getDateformat() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->dateformat . 'D j';
	}

	public function getDateformat1st() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->dateformat1st;
	}

	public function getOccurrenceFormat() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->occurrenceFormat;
	}

	public function getUrlFieldHandle() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->urlFieldHandle;
	}

	public function getEntryCalendarTextFieldHandle() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->entryCalendarTextFieldHandle;
	}

	public function getTimezone() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->timezone;
	}
	
	public function getSections() {
		$plugin = craft()->plugins->getPlugin('DryCalendar');
		$settings = $plugin->getSettings();
		return $settings->sections;
	}
	

	
	}