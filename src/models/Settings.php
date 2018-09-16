<?php
namespace kr37\drycalendar\models;

use craft\base\Model;

class Settings extends Model
{
    public $cpCssFile;
    public $occurrenceFormat;
    public $availableTimes;
    public $status;
    public $categoriesToExclude;
    public $categoriesToInclude;
    public $title;
    public $showTails;
    public $filler1;
    public $filler2;
    public $rowOfDaysFormat;
    public $nodate;
    public $dateformat;
    public $dateformat1st;

    public $categoryFieldHandle;
    public $entryCalendarTextFieldHandle;
    public $imageFieldHandle;
    public $cssFieldHandle;
    public $startDateFieldHandle;
    public $urlFieldHandle;

    public $timezone = "america/denver";

    public function rules()
    {
        return [
            [['occurrenceFormat', 'availableTimes', 'status',
            'dateformat1st', 'categoryFieldHandle', 'entryCalendarTextFieldHandle',
            'cssFieldHandle', 'startDateFieldHandle'], 'required'],
        ];
    }
}
