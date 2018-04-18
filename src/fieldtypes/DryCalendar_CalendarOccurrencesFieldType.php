<?php
namespace Craft;

class DryCalendar_CalendarOccurrencesFieldType extends BaseFieldType
{
	public function getName() {
		return Craft::t('Calendar Occurrences');
	}


	public function getInputHtml($name, $value) {

		// Include our Javascript
		craft()->templates->includeJsResource('drycalendar/occurrencesField.js');
		$settings = craft()->plugins->getPlugin('drycalendar')->getSettings();
		$startDateFieldHandle = craft()->templates->namespaceInputId($settings['startDateFieldHandle']) . '-date';
		$calendarText = craft()->templates->namespaceInputId($settings['entryCalendarTextFieldHandle']);
		$js = "var startDateFieldHandle = '$startDateFieldHandle';\n"
			. "var entryCalendarTextFieldHandle = '$calendarText';";
		craft()->templates->includeJs($js);
		
		// Include CSS
		craft()->templates->includeCssResource('drycalendar/occurrencesField.css');

		// Find out how many times this post is already in the calendar (for the button caption)
		$count = craft()->drycalendar_calendarOccurrencesField->eventOccurrencesCount($this->element->id); 

		return craft()->templates->render('drycalendar/calendaroccurrences/input1', array(
			'name'  => $name,
			'value' => $value,
			'count' => $count,
			'event_times' => craft()->drycalendar_mySettings->getAvailableTimes()
		));   
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