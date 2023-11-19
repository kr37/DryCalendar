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

    public $entryCalendarTextFieldHandle;
    public $cssFieldHandle;
    public $startDateFieldHandle;
    public $imageFieldHandle;
    public $entryStreamedFieldHandle;
    public $urlFieldHandle;

    public $categoryFieldHandle;
    public $categoryStreamedFieldHandle;

    public $timezone = "america/denver";

    public function rules(): array
    {
        return [
            [['occurrenceFormat', 'availableTimes', 'status',
            'dateformat1st', 'categoryFieldHandle', 'entryCalendarTextFieldHandle',
            'cssFieldHandle', 'startDateFieldHandle'], 'required'],
        ];
    }
}
