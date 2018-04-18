<?php
namespace Craft;

class DryCalendar_CalendarOccurrencesFieldDisplayModel extends BaseController
{
	// Inputs
	public $entry_id = null;
	public $start_15th = null;
	public $end_15th = null;
	public $calendar_text = '';

	// Intermediate Values
	public $occurrence; //This array holds all of the actual individual events
	public $title2disp; //This array holds the computed title for each occurrence.
	
	// Constructor
	function __construct(){
		parent::__construct($this->id, $this->module);
	}		
}