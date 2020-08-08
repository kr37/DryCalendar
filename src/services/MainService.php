<?php
namespace kr37\drycalendar\services;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\elements\Entry;
use craft\helpers\UrlHelper;
use craft\web\Request;

use kr37\drycalendar\DryCalendar as Plugin;
use kr37\drycalendar\models\CalendarModel;
use kr37\drycalendar\models\DryCalendar_EventModel;
use kr37\drycalendar\records\DrycalendarViews;

class MainService extends Component
{

	private $twigAtts;
	private $settings;
	private $imageFieldHandle;

	public function initCal($fromDateYmd=NULL, $toDateYmd=NULL, $atts=array()) {
	// Creates a new CalendarModel object, populating all the event occurrences
	// Parameters come from either the calling function (TWIG) or from GET/POST. GET/POST always override.

		$this->settings = Plugin::$plugin->getSettings();
		$this->twigAtts = $atts;

		$cal = new CalendarModel;
		$cal->calupdate = (array_key_exists('calupdate',$atts)) ? $atts['calupdate'] : false;

  		$cal->showTails        = $this->getParam('showTails');
		$cal->status           = $this->getParam('status');
		$cal->rowOfDaysFormat  = $this->getParam('rowOfDaysFormat');
		$cal->dateformat       = $this->getParam('dateformat');
		$cal->dateformat1st    = $this->getParam('dateformat1st');
		$cal->occurrenceFormat = $this->getParam('occurrenceFormat');
		$cal->title            = $this->getParam('title');
		$cal->filler1          = $this->getParam('filler1');
		$cal->filler2          = $this->getParam('filler2');
		$cal->nodate           = $this->getParam('nodate');
		$catsToInclude         = $this->getParam('categoriesToInclude');
		$catsToExclude         = $this->getParam('categoriesToExclude');
		if (is_array($catsToInclude)) {
    		$cal->categoriesToInclude = $catsToInclude;
        } else {
            $cal->categoriesToInclude = $catsToInclude ? explode(',', $catsToInclude) : null;
        }
		if (is_array($catsToExclude)) {
		    $cal->categoriesToExclude = $catsToExclude;
        } else {
    		$cal->categoriesToExclude = $catsToExclude ? explode(',', $catsToExclude) : null;
        }

        $request = new Request;
		$cal->desiredStartYmd( $request->getParam('calstart') ?: $fromDateYmd ?: date("Y-m-01",time()) );
		$cal->desiredEndYmd( $request->getParam('calend')?: $toDateYmd ?: date("Y-m-t",$cal->desiredStartNum) );

        $view = (new Query())
            ->select(['*'])
            ->from([Plugin::VIEWS_TABLE])
            ->where(['startDateYmd' => $cal->desiredStartYmd()])
            ->andWhere(['endDateYmd' => $cal->desiredEndYmd()])
            ->one();
		//$view = $view->find()->where(array('startDateYmd'=>$cal->desiredStartYmd(), 'endDateYmd'=>$cal->desiredEndYmd()));
		if ($view) {
			$cal->filler1 = $view['htmlBefore'];
			$cal->filler2 = $view['htmlAfter'];
		}

		// OK, pull all occurrences from the db
        $cal->occurrence = (new Query())
            ->select("c37.id, event_id, dateYmd, timestr, alt_text, css_class, userjson")
			->from(Plugin::CALENDAR_TABLE . " c37")
            ->leftJoin('craft_elements', 'c37.event_id = craft_elements.id')
			->where("c37.dateYmd >= '{$cal->actualStartYmd()}'")
			->andWhere("c37.dateYmd <= '{$cal->actualEndYmd()}'")
            ->andWhere("craft_elements.enabled = '1'")
			->orderBy('dateYmd ASC, timestr ASC')
			->all();

		// Filter based on category,
		// and catalog event and category info for valid events (to save re-querying for repeated events)
		foreach($cal->occurrence as $key => $occurrence) {
			$eventID = $occurrence['event_id'];
			if ( !isset( $eventIsValid[$eventID] ) ) {
				// Collect info about this event. Especially, determine whether or not to display it.
				$entry    = Craft::$app->entries->getEntryById($eventID);
				if ($entry==null) {
					mail("kelsangrinzin@gmail.com","Craft DryCalendar debugging", "In the drycalendar table, there is a record with event_id = $eventID, but there is no corresponding entry number $eventID. I think you should delete this entry for the drycalendar table.");
					unset($cal->occurrence['key']);
					continue;
				}
				$category = $entry->mainCategory->inReverse()->one();

				$eventIsValid[$eventID] = 'no'; //We'll change it to yes if it turns out to be good.
				if ( empty($cal->categoriesToInclude) || in_array($category->id, $cal->categoriesToInclude) ) {
					if ( empty($cal->categoriesToExclude) || !in_array($category->id, $cal->categoriesToExclude) ) {
						$eventIsValid[$eventID] = 'yes';
						$event = new DryCalendar_EventModel;
						$event->url         = $entry->getUrl();
						$event->css         = (isset($category)) ? $category->cal37Css : '';
						$event->eventHandle = (isset($category)) ? $category->slug : '';
                        $event->catTitle    = $category->title;
						$event->title       = $entry->{$this->settings->entryCalendarTextFieldHandle} ?: $entry->title;
                        if (!is_null($entry->calendarImage)) {
                            $image = $entry->calendarImage->one();
                            if ($image) {
                                $event->title   = "<img src='{$image->url}' title='{$event->title}'>";
                                $event->imageAsTitle = true;
                            }
                        }
						$cal->event[$eventID]   = $event;
						$cal->entries[$eventID] = $entry;
					}
				}
			}
			if ($eventIsValid[$eventID] == 'no') unset($cal->occurrence[$key]);
		}
		$cal->occurrence = array_values($cal->occurrence);

        // Establish links to previous and next month calendar
        $cal->setLinkToPrev();
        $cal->setLinkToNext();

		// How will we create URLs?
		$cal->urlFieldHandle = $this->settings->categoryFieldHandle;
		return $cal;
	}

	private function getParam($param) {
		//Returns the first value of param from: GET/POST, $atts (from TWIG), or plugin $settings
        $request = new Request;
        return $request->getParam($param)
			?: (array_key_exists($param,$this->twigAtts) ? $this->twigAtts[$param] : $this->settings[$param]);
	}

	public function calendar_full($cal) {
	// This is the main function for displaying a calendar

//$c = print_r($cal, true);
//return "<pre>$c</pre>";
		// Display title with month and year, such as Meditate in Fort Collins ~ December 2006-----
		$br = $cal->title ? '<br>' : '';
		$out = "<h2 class='cal37_mainheader'>{$cal->title}$br{$cal->dateHeader()}</h2>\n";

		// Display links to previous and next month-----
		// Figure out the URLs
		$host = $_SERVER['HTTP_HOST'];
		$uri = $_SERVER['REQUEST_URI'];
		$total_url = "http://$host$uri";
		$argv = array_key_exists('argv', $_SERVER) ? $_SERVER['argv'] : '';
        $request = new Request;
		$segments = $request->segments;

		$querystart = strpos($total_url,"?");
		$out .= "<!-- segments: ".print_r($segments,true)."\ntotal_url: $total_url\nquerystart: $querystart\n";
		if (!$querystart) {
			$querystart = strlen($total_url);
		}
		if ($querystart) {
			$base_url = substr($total_url,0,$querystart);
			$queries = substr($total_url,$querystart+1);
			$fn = $base_url . "?";
			$out .= "base_url: $base_url\nqueries: $queries\nfn: $fn\n";
		} else {
			$fn = $total_url . "?";
		}
		$out .= "-->\n\n";

		// Display PREV & NEXT
		$out .= "<div class='cal37_nextprev'>\n"
		      . "	<a href='{$fn}{$cal->linkToPrev->param}' >{$cal->linkToPrev->text}</a>\n"
		      . "	".date("F", $cal->desiredStartNum)." \n"
		      . "	<a href='{$fn}{$cal->linkToNext->param}' >{$cal->linkToNext->text}</a>\n"
		      . "	<br>\n"
		      . "</div>\n"
			  . "<input type='hidden' name='desiredStartYmd' value='{$cal->desiredStartYmd()}'>\n"
			  . "<input type='hidden' name='desiredEndYmd' value='{$cal->desiredEndYmd()}'>\n";

		// Output the calendar-----
		$next_date_to_display = $this->thisSunday($cal->desiredStartNum);
		$out .= "<div id='cal37'>\n\n"
			. "	<table class='cal37'>\n";

		//Output a top row of days of the week, if desired
		if ($cal->rowOfDaysFormat!='') {
			$out .= "		<tr>\n";
			//BUG ALERT: These constants for the length of a day or week need to be changed
			//because on daylight savings days, the length of the day is different.
			for ($i=$next_date_to_display; $i<$next_date_to_display+7*86400; $i+=86400) {
				$out .= "			<th>".date($cal->rowOfDaysFormat,$i)."</td>\n";
			}
			$out .= "		</tr>\n";
		}
		//Output the remaining calendar
		while ($next_date_to_display <= $cal->actualEndNum) {
			$next_date_to_display=$this->lineofevents($cal, $next_date_to_display, $out);
		}
		$out .= "	</table>\n</div><!--cal37-->\n";

		return $out;
	}

	public static function thisMonth($timestmp) {
	//Returns the unix timestamp for the first day of the month that $timestmp falls in
	  $dayofmonth = date("d",$timestmp) - 1;
	  return strtotime("-".$dayofmonth." day", $timestmp);
	}

	public static function thisSunday($timestmp) {
	//Returns the unix timestamp for Noon on Sunday of the week that $timestmp falls in (same time of day as timestamp)
	//There is still some daylight savings time glitch here maybe?
	  $daynum = date("w", $timestmp);
	  return strtotime("-".$daynum." day", $timestmp);
	}

	function lineofevents($cal, $datenum, &$out) {
	//Produces a week's worth of lists of daily events and increment $datenum

		$out .= "		<tr>\n";
		$i = 0;
		while ($i<7) {
			if ($cal->showTails=='yes' || ($datenum >= $cal->desiredStartNum && $datenum <= $cal->desiredEndNum)) {
				// Display a regular day of the month
				$out .= "			<td>\n";
				$out .= $this->events1day_all($cal, $datenum, 'cal37Tail');
				$out .= "			</td>\n";
				$datenum = strtotime('+1 day', $datenum);
				$i++;
			} else {
				// Display the blank boxes for the start or end of the month
				if ($datenum < $cal->desiredStartNum):
					$filler = $cal->filler1;
					$class = 'cal37_tail1';
					$days = ($cal->desiredStartNum - $datenum)/86400;
				else:
					$filler = $cal->filler2;
					$class = 'cal37_tail2';
					$days = 7 - date('w', $datenum);
				endif;
				$out .= "			<td colspan='$days' class='cal37_calendar $class cal37_colspan$days'>$filler</td>\n";
				$datenum = strtotime("+$days day", $datenum);
				$i += $days;
			}
		}
		$out .= "		</tr>\n";
		return ($datenum);
	}


	function events1day_all($cal, $datenum, $otherCss) {
	//Produces a list of all the occurrences on this day
		static $monthCss;

		$date = date("Y-m-d",$datenum);
		$dayofmonth = date("j",$datenum);
		if ($dayofmonth=='1') {
			$monthCss =  date("F ",$datenum) . ((date("m",$datenum) & 1) ? 'month-odd' : 'month-even');
			$newMonthCss = 'cal37-newmonth';
			$pDate = date($cal->dateformat1st, $datenum);
		} else {
			$newMonthCss = '';
			$pDate = date($cal->dateformat, $datenum);
		}

		// Ouput the date info
		$out = '';
		if ($cal->nodate != 'yes') {
			$out .= "				<p class='date $newMonthCss $monthCss $otherCss'>";
			if ('' != $cal->calupdate) $out .= "<input type='checkbox' name='add$date'>";
			$out .= "$pDate</p>\n";
		}

		// Output the occurrences
		$out .= "				<ul class='cal37 cal37_calendar $otherCss' >\n";
        $urlHelper = new UrlHelper;
        $siteUrl = $urlHelper->baseSiteUrl();
		while ( isset( $cal->occurrence[0] )  &&  $cal->occurrence[0]['dateYmd'] <= $date ) {
			$minCount = 1;

			//Take the first event off of $cal->occurrence and use it, shortening the array
			$row      = array_shift($cal->occurrence);
			$entryID  = $row['event_id'];
			$event    = $cal->event[$entryID];
            preg_match("&.*:\/\/[^\/]*(\/.*)&", $event->url, $matches);
            $url      = $matches[1];
			$class    = $row['css_class'] ?: $event->css;
			$time     = $this->nicetime($row['timestr']);
			if ($row['alt_text']) {
				$program  = $row['alt_text'];
			} else {
				$program  = $event->title;
				if ($event->imageAsTitle) $time='';
			}
			if ($cal->calupdate) {
				$program .= " {$entryID}";
				$updateCheckbox = "<input type='checkbox' name='del{$row['id']}' >\n						";
			} else {
				$updateCheckbox = '';
            }

			$out .= <<<ONEOCCURRENCE
					<li data-instance_id='{$row['id']}' data-event_id='$entryID' data-category='{$event->eventHandle}' class='$class {$row['timestr']} $cal->tailStyle'>
						$updateCheckbox<a href='{$url}'>
ONEOCCURRENCE;
			$out .= sprintf($cal->occurrenceFormat, $time, $program) . "</a>\n					</li>\n";
		} //while
		$out     .= "				</ul>\n";
		if (!isset($minCount))
			$out .= "				<br>\n";  //if there were no events, still put a blank line.
		return $out;
	} //function events1day_all


	function eventsOneDayAll($cal, $datenum, $otherCss) {
	//Produces a list of all the occurrences on this day
		static $monthCss;

		$date = date("Y-m-d",$datenum);
		$dayofmonth = date("j",$datenum);
		if ($dayofmonth=='1') {
			$monthCss =  date("F ",$datenum) . ((date("m",$datenum) & 1) ? 'month-odd' : 'month-even');
			$newMonthCss = 'cal37-newmonth';
			$pDate = date($cal->dateformat1st, $datenum);
		} else {
			$newMonthCss = '';
			$pDate = date($cal->dateformat, $datenum);
		}

		$twigVars['calUpdate']   = $cal->calupdate;
		$twigVars['date']        = $date;
		$twigVars['dayOfMonth']  = $dayofmonth;
		$twigVars['noDate']      = $cal->nodate;
		$twigVars['monthCss']    = $monthCss;
		$twigVars['newMonthCss'] = $newMonthCss;
		$twigVars['otherCss']    = $otherCss;
		$twigVars['pDate']       = $pDate;
		$twigVars['occurrences'] = array();
		$twigVars['entries']     = array();
		$twigVars['updateCheckbox'] = array();
		$twigVars['class']       = array();
		$twigVars['program']     = array();
		$twigVars['time']        = array();

		// Put all the occurrences into $twigVars
		while ( isset( $cal->occurrence[0] )  &&  $cal->occurrence[0]['dateYmd'] <= $date ) {

			//Take the first event off of $cal->occurrence and use it, shortening the array
			$occurrence                    = array_shift($cal->occurrence);
			$entryId                       = $occurrence['event_id'];
			$twigVars['occurrences'][]     = $occurrence;
			$twigVars['entries'][$entryId] = $cal->entries[$entryId];
			$twigVars['class'][]           = $occurrence['css_class'] ?: $cal->event[$entryId]->css;

			$time     = $this->nicetime($occurrence['timestr']);
			if ($occurrence['alt_text']) {
				$program  = $occurrence['alt_text'];
			} else {
				$program  = $cal->event[$entryId]->title;
				if ($cal->event[$entryId]->imageAsTitle) $time='';
			}
			if ($cal->calupdate) {
				$program .= " {$entryId}";
				$twigVars['updateCheckbox'][] = "<input type='checkbox' name='del{$occurrence['id']}' >\n						";
			} else
				$twigVars['updateCheckbox'][] = '';

			$twigVars['program'][] = $program;
			$twigVars['time'][]    = $time;

		} //while

		$oldPath = craft()->path->getTemplatesPath();
		$newPath = craft()->path->getPluginsPath() . 'drycalendar/templates';
		craft()->path->setTemplatesPath($newPath);
		$out .= craft()->templates->render('_calendarOneDay.html', $twigVars);
		craft()->path->setTemplatesPath($oldPath);
		return $out;
	} //function eventsOneDayAll

	private function nicetime($time0) {
	//$time0 is a time string like 14:00:00
	//Returns null if $time0 is less than 0 or greater than 24.
	//Returns time with AM/PM, and if possible without minutes:  10AM, 4PM, 2:45PM, etc.
		if ($time0 < 0 or $time0 > 24) $time = null;
		else {
			$date = '';
			$timestamp=strtotime($date." ".$time0);
			$minutes=date("i",$timestamp);
			if ($minutes>0) $time = date("g:ia",$timestamp);
			else $time=date("ga",$timestamp);
		}
		return $time;
	}

	public function possibleEvents($cal) {
		// Get list of current events to choose from
		$this->settings = Plugin::$plugin->getSettings();
		$entries = Entry::find()
		    ->section('events')
		    ->startDate(array("<={$cal->actualEndYmd()}", NULL))
		    ->expiryDate(array(">={$cal->actualStartYmd()}", NULL))
		    ->enabledForSite('1')
		    ->orderBy($this->settings->entryCalendarTextFieldHandle . ' ASC')
            ->all();
		return $entries;
	}

	function myErrorHandler($errorLevel, $errorMessage) {
		return true;
	}

	public function calUpdateEventsOptions(CalendarModel $cal) {
		// Put the possible events into a <SELECT><OPTION>
		//set_error_handler(array($this,"myErrorHandler"));
		$events = $this->possibleEvents($cal);
		$eventsOptions = '';
		foreach ($events as $row) {
			$name = $row->{$this->settings->entryCalendarTextFieldHandle} ?: $row->title ?: "Entry ID: {$row->id}";
			$eventsOptions .= "<OPTION VALUE='{$row->id}'>$name &nbsp; | &nbsp; {$row->mainCategory->inReverse()->one()->title}";
			if ($row->expiryDate > '0000-00-00') {
				$eventsOptions .= " &nbsp; | &nbsp; {$row->startDate->format('Y-m-d')} - {$row->expiryDate->format('Y-m-d')}";
			}
			$eventsOptions .= "</OPTION>\n					";
		}
		return $eventsOptions;
	}

	public function calUpdateTimesOptions() {
		// get <OPTIONS> for times
		$settings = craft()->plugins->getPlugin('drycalendar')->getSettings();
		return $settings['availableTimes'];
	}

	public function htmlBefore($startYmd = null, $endYmd = null, $subsetId = null) {
		$view = DrycalendarViews::find()->where(['startDateYmd'=>$startYmd, 'endDateYmd'=>$endYmd])->one();
		return ($view) ? $view->htmlBefore : '';
	}

	public function htmlAfter($startYmd = null, $endYmd = null, $subsetId = null) {
		$view = DrycalendarViews::find()->where(['startDateYmd'=>$startYmd, 'endDateYmd'=>$endYmd])->one();
		return ($view) ? $view->htmlAfter : '';
	}

    public static function getOccurrences($fromYmd, $toYmd) {

		// OK, pull all occurrences from the db
        $occurrences = (new Query())
            ->select("c37.id, event_id, dateYmd, timestr, alt_text, css_class, userJson")
			->from(Plugin::CALENDAR_TABLE . " c37")
            ->leftJoin('craft_elements', 'c37.event_id = craft_elements.id')
			->where("c37.dateYmd >= '$fromYmd'")
			->andWhere("c37.dateYmd <= '$toYmd'")
            ->andWhere("craft_elements.enabled = '1'")
			->orderBy('dateYmd ASC, timestr ASC')
			->all();
        return $occurrences;
    }

}
