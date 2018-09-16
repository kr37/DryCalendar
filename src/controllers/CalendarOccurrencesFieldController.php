<?php
namespace kr37\drycalendar\controllers;

use Craft;
//use yii\web\Controller;
use yii\web\Response;
use craft\web\Controller;
use craft\web\Request;

use kr37\drycalendar\DryCalendar as Plugin;
use kr37\drycalendar\models\CalendarOccurrencesFieldDisplayModel;
use kr37\drycalendar\services\CalendarOccurrencesFieldService as FService;

class CalendarOccurrencesFieldController extends Controller
{

    public function actionGenerateAjaxMiniCalendar() {

        //$this->requireAjaxRequest();
        
        // Create the mini-calendar object
        $miniCal = new CalendarOccurrencesFieldDisplayModel;

        // Get the submitted parameters
        //-----------------------------
        $allTheQueryParams  = $_POST;

        $request = Craft::$app->getRequest();
        $miniCal->entry_id   = $request->getParam('entry_id');
        $miniCal->start_15th = strtotime( strtok( $request->getParam('start_15th'), '(' ) );
        $miniCal->end_15th   = strtotime( strtok( $request->getParam('end_15th'), '(' ) );
        $miniCal->calendar_text = $request->getParam('calendar_text');
        
        // Initialize the mini-calendar(s)
        $fieldService = new FService;
        $ajaxMiniCal = $fieldService->miniCalInit($miniCal);
 $ajaxMiniCal = "Hi there, dude";
        // Reply with the mini-calendar(s)
        $response = array('response' => $ajaxMiniCal, 'request' => $allTheQueryParams);
        $this->returnJson($response);    
    }


    public function actionDeleteOccurrence() {
    //AJAX server-side update of one item in cell on the mini-calendar in an Occurrences Field

        $request = Craft::$app->getRequest();
        $occurrence_id = $request->getParam( 'occurrence_id' );
        $result = craft()->drycalendar_calendarOccurrencesField->deleteOccurrence($occurrence_id);
        $response['success'] = ($result===true) ? 'Deleted' : $result;

        // Generate the response: a (probably empty) array of all the occurrences on this day
        $event_id = $request->getParam( 'entry_id');
        $dateYmd  = $request->getParam( 'date');
        $rs = craft()->drycalendar_calendarOccurrencesField->getOneDaysOccurrences($event_id, $dateYmd);
        $response['returnData'] = array('count'=>count($rs), 'rows'=>$rs);
        $response['message']    = '';
        $this->returnJson($response);    

    } // function actionDeleteOccurrence

    
    public function actionAddOccurrence() {
    // AJAX server-side update of one item in cell on the mini-calendar of an Occurrences Field

        // Get the submitted parameters
        //-----------------------------
        $request = Craft::$app->getRequest();
        $instance = new DryCalendarRecord;
        $instance->event_id = $request->getParam( 'entry_id');
        $instance->dateYmd  = $request->getParam( 'dateYmd');
        $instance->timestr  = $request->getParam( 'timestr');
        $instance->alt_text = $request->getParam( 'alt_text');
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
