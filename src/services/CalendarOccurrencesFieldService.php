<?php
namespace kr37\drycalendar\services;

use Craft;
use craft\base\Component;
use craft\db\Query;

use kr37\drycalendar\DryCalendar as Plugin;
use kr37\drycalendar\models\CalendarOccurrencesFieldDisplayModel;
use kr37\drycalendar\services\MainService;
use kr37\drycalendar\records\Drycalendar as DryCalendarRecord;

class CalendarOccurrencesFieldService extends Component
{
    public static function eventOccurrencesCount ($eventID) {
        $count = (new Query())
            ->select('count(*)')
            ->from(Plugin::CALENDAR_TABLE.' c37')
            ->where("c37.event_id = '$eventID'")
            ->count();
        return $count;
    }


    public function miniCalInit (CalendarOccurrencesFieldDisplayModel $miniCal) {

        // Get the occurrences of this entry
        $miniCal->occurrence = (new Query())
            ->select('*')
            ->from(Plugin::CALENDAR_TABLE.' c37')
            ->where("c37.event_id = '{$miniCal->entry_id}'")
            ->orderBy('dateYmd ASC, timestr ASC')
            ->all();
            
        // Get the right title for each occurrence
        foreach($miniCal->occurrence as $key => $occurrence) {
            $miniCal->title2disp[$key] = $occurrence['alt_text']?: $miniCal->calendar_text ?: $miniCal->entry_id;
        }
        
        if (count($miniCal->title2disp)) {
            $miniCal->occurrence = array_values($miniCal->occurrence);
            $miniCal->title2disp = array_values($miniCal->title2disp);
        }
        
        // Create the calendars
        //---------------------
        $display = '';
        for ($mth = $miniCal->start_15th; $mth <= $miniCal->end_15th; $mth = strtotime("+1 month", $mth) ) {
            $display .= $this->cal37_micro($miniCal, date("Y-m",$mth), 'no');
        }
        $display .= "\n<div style='clear:both'>\n";
        
        return $display;
    }

        

    function cal37_micro ($miniCal, $month, $tails='yes', $nextprev='no', $indent='') {
    //Prepares the Entry Edit Ajax mini-calendars for updating the calendar

        //Determine month of calendar
        $dn = (!$month) ? $dn = time() : strtotime($month);

        $first_of_month = MainService::thisMonth($dn);
        $starting_sunday = MainService::thisSunday($first_of_month);
        $second_sunday = $starting_sunday + 604800;
        $next_month = strtotime("+1 month", $dn);
        $prev = strtotime("-1 month", $dn);

        $output = "<div class='cal37_one_micro_cal'>\n";

        //Links to previous and next months
        if ($nextprev == 'yes') {
            $output .= "<a href='$fn?calstart=" . date("Y-m-d",$prev) . "' >" . date("F",$prev) . "</a> "
                . "&nbsp; <span style='text-size:larger'>" . date("F Y", $dn) . "</span>\n"
                . "<a href='$fn?calstart=" . date("Y-m-d",$next_month) . "' > &nbsp; " 
                . date("F",$next_month) . "</a>\n";
        } else {
            $output .= "<span style='text-size:larger'>" . date("F Y", $dn) . "</span>\n";
        }

        // Output the actual calendar
        $output .= "$indent<TABLE class='cal37_micro'>\n";

        //Days of week
        $output .= "$indent  <tr class=\"calday\">\n";
        for ($i=$starting_sunday; $i<$second_sunday; $i+=86400) {
            $output .= "$indent    <td WIDTH='14%' class='calday'>".date("D",$i)."</td>\n";
        }
        $output .= "$indent  </tr>\n";

        //Rest of calendar
        for ($sunday=$starting_sunday; $sunday<$next_month; $sunday+=604800) {
            $output .= "$indent  <tr class='calddate'>\n";
            $next_sunday = $sunday + 604800;
            for ($this_day = $sunday; $this_day<$next_sunday; $this_day+=86400) {
                if (($this_day >= $first_of_month) and ($this_day < $next_month))
                    $output .= $this->one_cell($miniCal, $this_day, "", "write_javascript", $indent.'  ');
                elseif ($tails == 'yes')
                    $output .= $this->one_cell($miniCal, $this_day, "1", "write_javascript", $indent.'  ');
                else 
                    $output .= "$indent    <TD width='14%'>&nbsp;</td>\n";
            }
        }
        $output .= "$indent  </tr>\n$indent</TABLE>\n</div>\n\n";
        return $output;
    }

    
    
    function getTodaysEvents($miniCal, $timestmp) {
    //Returns array(index2event1, index2event2, etc)
        $eventIndex = array();
        $date = date("Y-m-d",$timestmp);
        for ($i=0; $i<count($miniCal->occurrence); $i++) {
            if ($date == $miniCal->occurrence[$i]['dateYmd'])  $eventIndex[] = $i;
        }
        return $eventIndex;
    }


    function one_cell($miniCal, $datenum,$show_month,$cellform,$indent="") {
    //If $show_month='1' then the first date will be preceded by the month, 
    //  regardless of which day it is (typically used for the first row of 
    //  dates displayed)  (ie. February 27  28  March 1  2  3  4  5)

        switch ($cellform) {
            case 'write_javascript':
                $dbDate = date("Y-m-d", $datenum);
                $day31 = date("j",$datenum);
                $month = ''; //($day31==1 or $show_month!="") ? date("M ",$datenum): "";
                $eventIndexes = $this->getTodaysEvents($miniCal, $datenum);
                $numEventsToday = count($eventIndexes);
                switch ($numEventsToday) {
                    case 0: 
                        $colorclass = ""; $title = ""; $id = "";
                        break;
                    case 1:
                        $colorclass = "background-color:green"; //"events_one";
                        $index      = $eventIndexes[0];
                        $time       = $miniCal->occurrence[$index]['timestr'];
                        //$output = "events[$index]: " . print_r($miniCal->occurrence[$index],true); 
                        if ($time<0 or $time>24) 
                            $time = '';
                        else {
                            $timestamp = strtotime($time);
                            $minutes   = date("i",$timestamp);
                            $time = ($minutes>0) ? date("g:ia ",$timestamp) : date("ga ",$timestamp);
                        }
                        $title      = $time . $miniCal->title2disp[$index] . "\n" . $miniCal->occurrence[$index]['css_class'] . "\n";
                        $id           = $miniCal->occurrence[$index]['id'];
                        break;
                    default:
                        $colorclass = "background-color:blue"; //events_multi";
                        $index      = $eventIndexes[0];
                        $title      = "Sorry, there are $numEventsToday events on this day, and "
                                      . "I do not know how to deal with the same event multiple times per day."
                                      . " You will have to use the main CalUpdate page.";
                        $id         = $miniCal->occurrence[$index]['id'];
                        //$id         = "";
                        break;
                }
                $output  = "$indent  <TD id='d$dbDate"."_$id' width='14%' "
                          . "onclick='updateCell(this,\"$dbDate\")' style='$colorclass' "
                          . "title='$title'>$month$day31</td>\n";
                $datenum += 86400; //Next Day
                break;
            default:
                $output  = "$indent  <td>";
                $output .= output_form(array('form' => $cellform));
                $output .= "$indent  </td>";
                //echo "<!--output_form is:\n$output-->";
                break;
        }
        return $output;
    }


    public function deleteOccurrence ($occurrence_id) {
    // ajax Delete of an occurrence -- posted from the ajax miniCalendar on the CP Entries page.

        $occurrence = DryCalendarRecord::findOne($occurrence_id);
        if ($occurrence->delete()) {
            return true; 
        } else {
            return "Unable to delete occurrence $occurrence_id.";
        }

        $instance = new DryCalendarRecord;
        if ($instance->deleteByPk($occurrence_id)) {
            return true; 
        } else {
            return "Unable to delete occurrence $occurrence_id.";
        }
    }


    public function addOccurrence (DryCalendarRecord $occurrence) {
    // ajax Add of an occurrence -- posted from the ajax miniCalendar on the CP Entries page.

        if (!($occurrence->event_id>0)) return "The event_id of {$occurrence->event_id} is not valid. This looks like a bug.";
        if (!$this->isTime($occurrence->timestr) && (intval($occurrence->timestr)==0)) return "Please set the time.";
        if ($occurrence->save()) {
            return true;
        } else {
            return 'Error on save: ' . $instance->getErrors();
        }
    }

    
    public function getOneDaysOccurrences($event_id, $dateYmd) {
    // Part of the reply from an ajax add or delete of an occurrence
        $results = (new Query())
            ->select(['id', 'dateYmd', 'timestr', 'alt_text'])
            ->from(Plugin::CALENDAR_TABLE.' c37')
            ->where("event_id = '$event_id'") 
            ->andWhere("dateYmd = '$dateYmd'")
            ->orderBy('timestr ASC')
            ->all();
        return $results; 
    }
    
    private function isTime($time) {
        $pattern = "/^([1-2][0-3]|[01]?[1-9]):([0-5]?[0-9]):([0-5]?[0-9])$/";
        return (preg_match($pattern, $time)===1) ? true : false;
    }

}
