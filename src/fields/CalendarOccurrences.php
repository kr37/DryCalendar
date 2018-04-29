<?php
namespace kr37\drycalendar\fields;

use Craft;
use craft\base\Field;
use craft\base\ElementInterface;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use yii\web\View;

use kr37\drycalendar\DryCalendar as Plugin;
use kr37\drycalendar\services\CalendarOccurrencesFieldService as FieldService;

class CalendarOccurrences extends Field
{
	public static function getName() {
		return Craft::t('DryCalendar', 'Calendar Occurrences');
	}


	public function getInputHtml($value, ElementInterface $element = null): string
	{

		// Include our Javascript
		Craft::$app->View->registerJsFile('drycalendar/occurrencesField.js');
		$settings = Plugin::$plugin->getSettings();
		$startDateFieldHandle = Craft::$app->View->namespaceInputId($settings['startDateFieldHandle']) . '-date';
		$calendarText = Craft::$app->View->namespaceInputId($settings['entryCalendarTextFieldHandle']);
		$js = "var startDateFieldHandle = '$startDateFieldHandle';\n"
			. "var entryCalendarTextFieldHandle = '$calendarText';";
		Craft::$app->View->registerJs($js);
		
		// Include CSS
		Craft::$app->View->registerCssFile('drycalendar/occurrencesField.css');

		// Find out how many times this post is already in the calendar (for the button caption)
		$count = FieldService::eventOccurrencesCount($element->id); 

		return Craft::$app->getView()->renderTemplate('drycalendar/calendaroccurrences/input1', [
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
