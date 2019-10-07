<?php
namespace kr37\drycalendar;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class DryCalendarBundle extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@kr37/drycalendar/resources";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'occurrencesField.js',
        ];

        $this->css = [
            'calendar.css',
            'calupdate.css',
            'occurrencesField.css',
        ];

        parent::init();
    }
}
