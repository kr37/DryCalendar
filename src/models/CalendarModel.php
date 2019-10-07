<?php
namespace kr37\drycalendar\models;

use Craft;
use craft\base\Model;
use craft\base\Component;

class CalendarModel extends Model
{
    public $id;
    public $module;

	// *** Parameters principally affecting what data will be queried and displayed ***
	public $status = 'live pending expired'; //Which statuses to display?
	public $categoriesToExclude = null; //Which categories to exclude?
	public $categoriesToInclude = null; //If null, then include all categories

    // *** Start and End dates ***
	//For the next 6 variables, say the calendar is for April 2015. April 1 falls on Wednesday. April 30 falls on Thursday.
	//$desiredStartNum will be the Unix date number of April 1, 2015
	//$desiredEndNum will be the Unix date number of April 30, 2015
	//$actualStartNum will be the Unix date number of March 29 (the Sunday before)
	//$actualEndNum will be the Unix date number of May 2 (the Saturday after)
	//$prevDays will be 3
	//$postDays will be 2
	public $desiredStartNum = null; //The requested start for the calendar
	public $desiredEndNum = null; //The requested end for the calendar
    public $actualStartNum = null; //The requested start might not be the first day of the week,
                        //so actual start is the first day of the week which includes the desired start.
	public $actualEndNum = null; //The last day of the week that contains the desired end.
	public $prevDays = null; //The difference (in days) between the desired start and the end of the week
	public $postDays = null; //The difference (in days) between the desired end and the end of the week

	// *** The data arrays ***
	public $occurrence; //This array holds all of the actual individual events (30 HJs, 2 OSGs, 1 Kangso, 4 PWP, etc.)
	public $entries;    //This array holds all the Craft entries used by this calendar
	public $event;      //This array holds computed values for each Entry
	public $eventTitle; //The computed title for each event
	public $eventCss;   //The css class for each event
	public $eventHandle;
	public $eventUrl;   //The URL for each event

	// *** Parameters affecting the formatting and display ***
	public $showTails = 'yes'; //Fill in the boxes before the first of the month or after the last of the month?
	public $calupdate = false; //Format the calendar as interactive (updateable) ?
	public $title = '';  //Any title to display at the top?
	public $filler1 = '';  //Content to display at the beginning of the month if $showTails is false
	public $filler2 = '';  //Content to display at the end of the month if $showTails is false
	public $tailStyle = 'cal37-tail-day'; //CSS class for the "tails"
	public $rowOfDaysFormat = ''; //A PHP Date compliant format for the single line of days

	public $dateformat    = 'D j'; //Title text for each day
	public $dateformat1st = 'D, F j'; //format to use for the first of the month
	public $nodate        = ''; //Don't show the date at the top
	public $occurrenceFormat = '%s %s'; //The PHP printf format for an occurrence, %1$s is the time, %2$s is the event title

	public $urlFieldHandle = null;  //Where to get the URL for an entry

	function __construct(){
		parent::__construct($this->id, $this->module);
		$this->desiredStartYmd( date("Y-m-01",time()) );
	}

	public function desiredStartYmd($valueYmd = null) {
		if ($valueYmd) {
			$this->desiredStartNum = strtotime($valueYmd);
			// Update actual start
		   $this->prevDays = date( "w", $this->desiredStartNum );
			if ($this->showTails=='yes'):
				$this->actualStartNum = strtotime("-{$this->prevDays} days", $this->desiredStartNum);
			else:
				$this->actualStartNum = $this->desiredStartNum;
			endif;
		}
		return date("Y-m-d", $this->desiredStartNum);
	}

	public function desiredEndYmd($valueYmd = null) {
		if ($valueYmd) {
			$this->desiredEndNum = strtotime($valueYmd);
			$this->postDays = 6 - date( "w", $this->desiredEndNum );
			if ($this->showTails=='yes'):
				$this->actualEndNum = strtotime("+{$this->postDays} days", $this->desiredEndNum);
			else:
				$this->actualEndNum = $this->desiredEndNum;
			endif;
		}
		return date("Y-m-d", $this->desiredEndNum);
	}
	public function actualStartYmd() {
		return date("Y-m-d", $this->actualStartNum);
	}
	public function actualEndYmd() {
		return date("Y-m-d", $this->actualEndNum);
	}

/*	public function defineAttributes() {
		return array(
			'id'        => AttributeType::String, //in case multiple calendars on page
			'title'     => AttributeType::String,
			'calupdate' => AttributeType::Boolean,

			'status'    => AttributeType::String,

			'showTails' => AttributeType::Boolean, // if !showTails then fillerText will be displayed
			'filler1'   => AttributeType::String,
			'filler2'   => AttributeType::String,
			'tailStyle' => AttributeType::String,  // CSS class for days outside of the month (if showTails=true)

			'rowOfDaysFormat' => AttributeType::String //format for the top row of days of the week, if blank then no row of days!
		);
	}
*/
	function dateHeader() {
		if ( date("m",$this->desiredStartNum) == date("m",$this->desiredEndNum) )
			return date("F Y", $this->desiredStartNum);
		else
			return date("F Y", $this->desiredStartNum) . ' to ' . date("F Y", $this->desiredEndNum);
	}

}
