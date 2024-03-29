// DryCalendar - cal37_postmeta_javascript.js - Javascript for the postmeta on the posts page
// Version: 0.1.10.133
// Does the following:
//   - implements datepicker for start and end dates
//   - propogates choice of a category to cal37_category
//     - AJAX to get the category name and URL    



jQuery(document).ready(function(){

    Garnish.calendarOccurrencesField = Garnish.Base.extend(
        {
            startDateFieldHandle:           "startDate-field",
            entryCalendarTextFieldHandle:   "fields-calendarText",
            'fieldIdSelector':              null,
            options:                        {},
            init: function (fieldId, settings) {
                this.options = $.extend({}, Garnish.calendarOccurrencesField.defaults, settings);
                this.startDateFieldHandle = this.options.startDateFieldHandle;

                console.log("this.options.startDateFieldHandle: "+this.options.startDateFieldHandle);
                console.log("this.options.entryCalendarTextFieldHandle: "+this.options.entryCalendarTextFieldHandle);

                this.startDateField = $('#' + this.options.fieldSelector + this.startDateFieldHandle);
                //console.log(this.startDateField);

                this.$field = $('#' + this.options.fieldSelector + fieldId);
                this.addListener(this.$field, 'click', $.proxy(this, 'clickEvent'));
                console.log("addListener("+'#' + this.options.fieldSelector + fieldId+", 'click', $.proxy(this, 'clickEvent'))");
                $('#fields-'+this.options.startDateField).change(function(data) { updateInfo(); });
                $('#expiryDate-date').change(function(data) { updateInfo(); });
                $('#'+this.options.entryCalendarTextFieldHandle).change(function(data) { updateInfo(); });
                console.log("Finished Garnish.calendarOccurrencesField.init("+fieldId+")");
                updateInfo();
            },
            clickEvent: function(){
                console.log("In my Garnish click-event." );

                // If it's already visible, just hide it.
                if ( $('#fields-cal37_event_inserter').is(':visible') ) {
                    console.log('Hiding...');
                    $('#fields-cal37_event_inserter').hide();
                    return;
                }

                //Prepare the calendars on the modal

                var cal_text   = jQuery("#cal37_calendar_text").val();

                //Determine what months to display
                Date.prototype.addMonth = function(n) { return new Date(this.setMonth(this.getMonth()+n)); };
                Date.prototype.copy = function () { return new Date(this.getTime()); };
                Date.prototype.yyyy_mm = function () {
                    var yyyy = this.getFullYear().toString();
                    var mm = (this.getMonth()+1).toString(); 
                    return yyyy + '-' + (mm[1]?mm:"0"+mm[0]); // padding
                };
                Date.prototype.fifteenth = function () { return this.yyyy_mm()+"-15"; };

                //Get Start Date
                var start_date = new Date( jQuery('input[id^=fields-startDate]').val() );
                console.log("Clicked the button. start_date: "+start_date);    
                if (isNaN(start_date)) {
                    alert("I'm not sure what that Start Date is. Let's assume it's today.");
                    start_date = new Date();
                }
                
                //Get End Date
                var end_date   = new Date(jQuery("input[name='expiryDate[date]'").val());
                console.log("end_date: "+end_date);
                if (end_date < start_date) {
                    alert("End Date should not occur before Start Date.\nLet's assume that the End Date is the same as the Start Date.");
                    end_date = start_date;
                }            
                if (isNaN(end_date)) {
                    //if no end date is specified, we'll display 4 months from either
                    //the start date, or the present day, whichever is later.
                    if ( Date.now() > start_date ) { 
                        start_date = new Date();
                    }
                    end_date = new Date( start_date.fifteenth() );
                    end_date.addMonth(3); //add 3 months        
                }
                
                var calendar_text = $("#"+this.options.entryCalendarTextFieldHandle).val();

                console.log (start_date.fifteenth() +" "+ end_date.fifteenth() +" calendar_text: "+ calendar_text);

                var data = { 
                    'entry_id'      : entry_id, 
                    'start_15th'    : start_date.fifteenth(), 
                    'end_15th'      : end_date.fifteenth(),
                    'calendar_text' : calendar_text
                };
                
                Craft.postActionRequest(
                    'drycalendar/calendar-occurrences-field/generate-ajax-mini-calendar', 
                    data, 
                    function(response) {
                        jQuery("#fields-cal37_micro").html(response.response);
                        $("#fields-cal37_event_inserter").show();
                });

            }
        },
        {
            defaults:{
                fieldSelector: 'fields-',
                otherFieldHandle: ''
            }
        }
    );


    jQuery('#cal37_end_date').change(function (data) {
        var monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December" ];
      endDate = new Date(data.target.value);
        year = endDate.getFullYear();
        month = ('0'+(endDate.getMonth()+1)).slice(-2);
        day = endDate.getDate()+1;
        jQuery('#enable-expirationdate').prop('checked', true).change();
        jQuery('#expirationdate_year').val(year).change();  
        jQuery('#expirationdate_month').val(month).change();    
        jQuery('#expirationdate_day').val(day).change();    
        jQuery('#expirationdate_hour').val('23').change();    
        jQuery('#expirationdate_minute').val('59').change();    
    }); //jQuery('#cal37_end_date').change

    
    updateInfo();
     
}); //jQuery(document).ready



function updateInfo() {
    console.log('in updateInfo()');
    var startDate = $('input[id^=fields-startDate]').val(); 
                            //$('#fields-startDate-field').val(); 
                            //+Garnish.calendarOccurrencesField.startDateField).val();
    var endDate   = $('input[name="expiryDate[date]"]').val();
    var text      = $('textarea[name="fields[calendarText]"]').val();
    //console.log( {{Garnish.calendarOccurrencesField.entryCalendarTextFieldHandle }} );
    //var text      = $('#' + {{Garnish.calendarOccurrencesField.entryCalendarTextFieldHandle }} ).val() 
    text          = text ? text : 'No text set. Will use the title.';
    var info = 'Start Date: ' + startDate + ' &nbsp; End Date: ' + endDate;
    info += ' &nbsp; Default text: <strong>' + text + '</strong>';
    $("#fields-cal37_info").html(info);
    console.log('leaving updateInfo()');
}



function updateTD(obj, dbDate, data) {
    console.log("Returned Data: ", data);
    var articleID = jQuery('input[name="id"]').val();
    numrows = data.returnData.count;
    switch (numrows) {
        case 0: 
            obj.id = "d" + dbDate;
            obj.style.backgroundColor = "";
            obj.title   = ""; 
            break;
        case 1:
            obj.id = "d" + dbDate + "_" + data.returnData.rows[0].id;
            obj.style.backgroundColor = "green"; 

            time = data.returnData.rows[0].timestr;
            if (time<0 || time>24) time = null;
            else {
              hr = time.substr(0,2);
              min = time.substr(3,2);
              //Make it 12 hour time with am/pm
              ampm = (hr <12) ? "am " : "pm ";
              if (hr<13) hr = hr * 1;
              else hr -= 12;
              if (min == "00") 
                time = hr + ampm; 
              else 
                time = hr + ":" + min + ampm;
            }
            title2disp = data.returnData.rows[0].alt_text;
            if (title2disp=="" || title2disp==undefined) {
              title2disp = jQuery('#cal37_calendar_text').val();
              title2disp = (title2disp=="" || title2disp==undefined) ? articleID : title2disp;
            }
            obj.title = time + title2disp;
            break;
        default:
            obj.id = "d" + dbDate + "_" + data.returnData.rows[0].id;
            obj.style.backgroundColor = "blue";
            obj.title = "Sorry, there are "+ numrows+ " events on this day, and "
                              + "I do not know how to deal with the same event multiple times per day."
                              + " You will have to use the main CalUpdate page.";
            break;
    }  //switch (numrows)
} //function updateTD

function updateCell(obj, dbDate, calItemID) {
    var action, alertText = "", s2="", message = "";
    var articleID = jQuery('input[name="ID"]').val();
    var oldColor = obj.style.backgroundColor; 
    obj.style.backgroundColor = "orange";
    dbDate = obj.id.substr(1,10);
    calItemID = obj.id.substr(12);
    console.log("obj",obj);
    console.log("obj.id",obj.id);
    console.log("obj.dbDate",dbDate);
    console.log("obj.calItemID",calItemID);

    if (calItemID) {
        // DELETE - If there's already an event on the clicked square, then delete it.
        var parameters = {
           'entry_id'       : entry_id, 
           'dateYmd'        : dbDate, 
           'occurrence_id'  : calItemID
        };
        Craft.postActionRequest(
            'drycalendar/calendar-occurrences-field/delete-occurrence', 
            parameters, 
            function(response) {
                if (response.success=="Deleted") {
                    cal37_numOccurrences--;
                    jQuery("#fields-cal37_choose_dates_button").html("In calendar " + cal37_numOccurrences + " times");
                } else {
                    alert(response.success);
                }
                    updateTD(obj, dbDate, response);
            }
        );
    } else {
        // ADD - Since there's not already an event on the clicked square, then add one.
        //Validate time and text before ADD
        time = document.getElementById("fields-time1").value;
        if (time == "--Do not change the text between these hyphens--") alertText += "Please set the time.\n";
        altText = document.getElementById("fields-alt_text").value;
        text = (altText=="") ? $("#"+Garnish.calendarOccurrencesField.entryCalendarTextFieldHandle).value : altText;
        if (text=="") alertText = alertText + "Please enter either Calendar Text or Alternate Text.";
        if (alertText == "") {
            //Do the ADD
            var parameters = {
               'entry_id'       : entry_id, 
               'dateYmd'        : dbDate, 
               'timestr'        : time, 
               'alt_text'       : altText,
               'cal37_category' : jQuery('#cal37_category').val(),
               'streamed'       : jQuery('#fields-streamed').val()
            };
            Craft.postActionRequest(
                'drycalendar/calendar-occurrences-field/add-occurrence', 
                parameters, 
                function(response) {
                    if (response.success=="Added") {
                        cal37_numOccurrences++;
                        jQuery("#fields-cal37_choose_dates_button").html("In calendar " + cal37_numOccurrences + " times");
                    } else {
                        alert(response.success);
                    }    
                    console.log('time to updateTD',obj,dbDate,response);
                    updateTD(obj, dbDate, response);
            });
        } else {
            alert(alertText);
            obj.style.backgroundColor = oldColor;

        }
    }
} //Function updateCell
