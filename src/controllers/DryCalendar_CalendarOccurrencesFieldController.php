<?php
namespace Craft;

class DryCalendar_CalendarOccurrencesFieldController extends BaseController
{

	public function actionGenerateAjaxMiniCalendar() {

		//$this->requireAjaxRequest();
		
		// Create the mini-calendar object
		$miniCal = new DryCalendar_CalendarOccurrencesFieldDisplayModel;

		// Get the submitted parameters
		//-----------------------------
		$allTheQueryParams  = $_POST;

		$miniCal->entry_id   = craft()->request->getParam('entry_id');
		$miniCal->start_15th = strtotime( strtok( craft()->request->getParam('start_15th'), '(' ) );
		$miniCal->end_15th   = strtotime( strtok( craft()->request->getParam('end_15th'), '(' ) );
		$miniCal->calendar_text = craft()->request->getParam('calendar_text');
		
		// Initialize the mini-calendar(s)
		$ajaxMiniCal = craft()->drycalendar_calendarOccurrencesField->miniCalInit($miniCal);

		// Reply with the mini-calendar(s)
		$response = array('response' => $ajaxMiniCal, 'requeest' => $allTheQueryParams);
		$this->returnJson($response);	
	}


	public function actionDeleteOccurrence() {
	//AJAX server-side update of one item in cell on the mini-calendar in an Occurrences Field

		$occurrence_id = craft()->request->getParam( 'occurrence_id' );
		$result = craft()->drycalendar_calendarOccurrencesField->deleteOccurrence($occurrence_id);
		$response['success'] = ($result===true) ? 'Deleted' : $result;

		// Generate the response: a (probably empty) array of all the occurrences on this day
		$event_id = craft()->request->getParam( 'entry_id');
		$dateYmd  = craft()->request->getParam( 'date');
		$rs = craft()->drycalendar_calendarOccurrencesField->getOneDaysOccurrences($event_id, $dateYmd);
		$response['returnData'] = array('count'=>count($rs), 'rows'=>$rs);
		$response['message']    = '';
		$this->returnJson($response);	

	} // function actionDeleteOccurrence

	
	public function actionAddOccurrence() {
	// AJAX server-side update of one item in cell on the mini-calendar of an Occurrences Field

		// Get the submitted parameters
		//-----------------------------
		$instance = new DryCalendarRecord;
		$instance->event_id = craft()->request->getParam( 'entry_id');
		$instance->dateYmd  = craft()->request->getParam( 'dateYmd');
		$instance->timestr  = craft()->request->getParam( 'timestr');
		$instance->alt_text = craft()->request->getParam( 'alt_text');
		$result = craft()->drycalendar_calendarOccurrencesField->addOccurrence($instance);
		$response['success'] = ($result===true) ? 'Added' : $result;
		
		// Generate the response: an array of all the occurrences on this day
		$resultingOccurrences = craft()->drycalendar_calendarOccurrencesField->getOneDaysOccurrences($instance->event_id, $instance->dateYmd);
		$count = count($resultingOccurrences);
		$response['returnData'] = array('count'=>$count, 'rows'=>$resultingOccurrences);
		$response['message']    = "There are now $count occurrences that day.";
		$this->returnJson($response);	

	} // function actionAddOccurrence
	
	
}