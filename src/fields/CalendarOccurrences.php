<?php
namespace kr37\drycalendar\fields;

use Craft;
use craft\base\Field;
use craft\base\ElementInterface;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use yii\web\View;

use kr37\drycalendar\DryCalendar as Plugin;
use kr37\drycalendar\DryCalendarBundle;
use kr37\drycalendar\services\CalendarOccurrencesFieldService as FieldService;

class CalendarOccurrences extends Field
{
    public static function getName() {
        return Craft::t('DryCalendar', 'Calendar Occurrences');
    }


    public function getInputHtml($value, ElementInterface $element = null): string
    {

        $view = Craft::$app->View;

        // Include our Javascript
        $view->registerAssetBundle(DryCalendarBundle::class);
        $settings = Plugin::$plugin->getSettings();
        $startDateFieldHandle = Craft::$app->View->namespaceInputId($settings['startDateFieldHandle']) . '-date';
        $startDateFieldHandle = Craft::$app->View->namespaceInputId($settings['startDateFieldHandle']);
        $calendarText = Craft::$app->View->namespaceInputId($settings['entryCalendarTextFieldHandle']);
        $params = json_encode([
                'startDateFieldHandle' => $startDateFieldHandle,
                'entryCalendarTextFieldHandle' => $calendarText,
            ]);
        Craft::$app->getView()->registerJs("new Garnish.calendarOccurrencesField('cal37_choose_dates_button', $params)");       

        // Find out how many times this post is already in the calendar (for the button caption)
        $count = FieldService::eventOccurrencesCount($element->canonicalId); 

        return $view->renderTemplate('drycalendar/calendaroccurrences/input1', [
            'field' => $this,
            'value' => $value,
            'count' => $count,
            'event_times' => $settings->availableTimes
        ]);   
    }

    public function defineContentAttribute() {
        // This field does not store anything in the 'content' database table,
        // because the info is stored in a separate table.
        return false;
    }

    public function prepValue($value) {
        // Modify $value here...

        return $value;
    }
}
