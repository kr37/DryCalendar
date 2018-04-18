<?php
namespace kr37\drycalendar\variables;

use Craft;

use kr37\drycalendar\DryCalendar as Plugin;
use kr37\drycalendar\services\DryCalendarService;

class DryCalendarVariable
{
    public function getMySetting() {
        //return craft()->drycalendar_mySettings->getMySetting();
        return Plugin::$plugin->drycalendar->drycalendar_mySettings->getMySetting();
    }

    public function getCpCssFile() {
        return craft()->drycalendar_mySettings->getCpCssFile();
    }

    public function getEventClickDestination() {
        return craft()->drycalendar_mySettings->getEventClickDestination();
    }

    public function getStyleFromCategory() {
        return craft()->drycalendar_mySettings->getStyleFromCategory();
    }

    public function calendar_full($fromDateYmd = null, $toDateYmd = null, $atts = array()) {
        $cal = craft()->drycalendar->initCal($fromDateYmd, $toDateYmd, $atts);
        return craft()->drycalendar->calendar_full($cal);
    }

    // *** CalUpdate stuff ***

    public function calupdate() {
        return craft()->drycalendar->calupdate();
    }

    public function events($fromDateYmd = null, $toDateYmd = null) {
        return craft()->drycalendar->eventsArray($fromDateYmd, $toDateYmd);
    }

    public function calUpdateEventsOptions(DryCalendar_CalendarModel $cal) {
        return craft()->drycalendar->calUpdateEventsOptions($cal);
    }

    public function calUpdateAvailableTimes() {
        return craft()->drycalendar_mySettings->getAvailableTimes();
    }

    public function calUpdateCalendarFull(DryCalendar_CalendarModel $cal) {
        return craft()->drycalendar->calendar_full($cal);
    }

    public function htmlBefore($startYmd = null, $endYmd = null, $subsetId = null) {
        return craft()->drycalendar->htmlBefore($startYmd, $endYmd, $subsetId);
    }

    public function htmlAfter($startYmd = null, $endYmd = null, $subsetId = null) {
        return craft()->drycalendar->htmlAfter($startYmd, $endYmd, $subsetId);
    }

    public function desiredStartYmd(DryCalendar_CalendarModel $cal) {
        return $cal->desiredStartYmd();
    }

    public function desiredEndYmd(DryCalendar_CalendarModel $cal) {
        return $cal->desiredEndYmd();
    }

    // *** Main Calendar Stuff ***

    public function init($fromDateYmd = null, $toDateYmd = null, $atts) {
        return craft()->drycalendar->initCal($fromDateYmd, $toDateYmd, $atts);
    }

    public function miniCalInit(&$fromDateYmd = null, &$toDateYmd = null, $atts = null) {
        $fromDateYmd = ($fromDateYmd > 0) ? $fromDateYmd : date("Y-m-d");
        $toDateYmd   = ($toDateYmd > 0)   ? $toDateYmd : date("Y-m-d", strtotime($fromDateYmd." +1day"));
        $atts['showTails'] = false;
        return Plugin::$plugin->services->initCal($fromDateYmd, $toDateYmd, $atts);
    }

    public function dump($cal) {
        return "<pre>\n" . print_r($cal->occurrence, true) . "\n</pre>";
    }

    public function oneDay($reqDateYmd = null, $atts = null, $cal = null) {
        //$plugin = Plugin::$plugin->drycalendar;
        $settings = Plugin::$plugin->getSettings();
        date_default_timezone_set($settings->timezone);
        $reqDateYmd = ($reqDateYmd) ?: date('Y-m-d');
        $cal = (isset($cal)) ? $cal : $this->miniCalInit($reqDateYmd, $reqDateYmd, $atts);
        return Plugin::$plugin->services->events1day_all($cal, strtotime($reqDateYmd), 'miniCal');
    }

    // *** Occurrences Field stuff ***

    public function miniCal($fromDateYmd = null, $toDateYmd = null) {
        $cal = $this->miniCalInit($fromDateYmd, $toDateYmd);
        $out = '';
        for ($dateNum = strtotime($fromDateYmd); $dateNum <= strtotime($toDateYmd); $dateNum = strtotime("+1 day", $dateNum) ) {
            $out .= craft()->drycalendar->events1day_all($cal, $dateNum, 'miniCal');
        }
        return $out;
    }

}
