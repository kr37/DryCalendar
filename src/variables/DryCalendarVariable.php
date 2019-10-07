<?php
namespace kr37\drycalendar\variables;

use Craft;

use kr37\drycalendar\DryCalendar as Plugin;
use kr37\drycalendar\services\MainService as Service;
use kr37\drycalendar\services\SettingsService as Settings;
use kr37\drycalendar\models\CalendarModel;

class DryCalendarVariable
{
    public function getMySetting() {
        return Plugin::$plugin->drycalendar->SettingsService->getMySetting();
    }

    public function getCpCssFile() {
        return Settings::getCpCssFile();
    }

    public function getEventClickDestination() {
        return Settings::getEventClickDestination();
    }

    public function getStyleFromCategory() {
        return Settings::getStyleFromCategory();
    }

    public function calendar_full($fromDateYmd = null, $toDateYmd = null, $atts = array()) {
        $service = new Service;
        $cal = $service->initCal($fromDateYmd, $toDateYmd, $atts);
        return $service->calendar_full($cal);
        //return Plugin::$plugin->calendar_full($cal);
    }

    // *** CalUpdate stuff ***

  /*  public function calupdate() {
        return Service::calupdate();
  } */

    public function events($fromDateYmd = null, $toDateYmd = null) {
        $service = new Service;
        return $service->eventsArray($fromDateYmd, $toDateYmd);
    }

    public function calUpdateEventsOptions(CalendarModel $cal) {
        $service = new Service;
        return $service->calUpdateEventsOptions($cal);
    }

    public function calUpdateAvailableTimes() {
        $settings = new Settings;
        return $settings->getAvailableTimes();
    }

    public function calUpdateCalendarFull(CalendarModel $cal) {
        $service = new Service;
        return $service->calendar_full($cal);
    }

    public function htmlBefore($startYmd = null, $endYmd = null, $subsetId = null) {
        $service = new Service;
        return $service->htmlBefore($startYmd, $endYmd, $subsetId);
    }

    public function htmlAfter($startYmd = null, $endYmd = null, $subsetId = null) {
        $service = new Service;
        return $service->htmlAfter($startYmd, $endYmd, $subsetId);
    }

    public function desiredStartYmd(CalendarModel $cal) {
        return $cal->desiredStartYmd();
    }

    public function desiredEndYmd(CalendarModel $cal) {
        return $cal->desiredEndYmd();
    }

    // *** Main Calendar Stuff ***

    public function init($fromDateYmd = null, $toDateYmd = null, $atts) {
        return Plugin::$plugin->services->initCal($fromDateYmd, $toDateYmd, $atts);
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
        $settings = Plugin::$plugin->getSettings();
        date_default_timezone_set($settings->timezone);
        $reqDateYmd = ($reqDateYmd) ?: date('Y-m-d');
        // If not passed a $cal object, then create a new one.
        $cal = (isset($cal)) ? $cal : $this->miniCalInit($reqDateYmd, $reqDateYmd, $atts);
        return Plugin::$plugin->services->events1day_all($cal, strtotime($reqDateYmd), 'miniCal');
    }

    // *** Occurrences Field stuff ***

    public function miniCal($fromDateYmd = null, $toDateYmd = null) {
        $cal = $this->miniCalInit($fromDateYmd, $toDateYmd);
        $out = '';
        for ($dateNum = strtotime($fromDateYmd); $dateNum <= strtotime($toDateYmd); $dateNum = strtotime("+1 day", $dateNum) ) {
            $out .= Plugin::$plugin->drycalendar->drycalendar->events1day_all($cal, $dateNum, 'miniCal');
        }
        return $out;
    }

}
