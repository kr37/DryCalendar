<?php
namespace Craft;

class DryCalendar_CalupdateController extends BaseController
{

	public function actionAddAndDeleteInstances() {
		// Update from POST
		//==================
		
		$out = '';
		$add = 0; $success_add = 0;
		$del = 0; $success_del = 0;
		$allTheQueryParams  = $_POST;
		$timestr  = craft()->request->getParam('time1');
		$event_id = craft()->request->getParam('post_id');
		$alt_text = craft()->request->getParam('alt_text');
		
		foreach($allTheQueryParams as $name => $value) {
			$first3 = substr($name,0,3);
			$remain = substr($name,3);
			switch ($first3) {
				case "add":
					if (intval($timestr)!=0 && $event_id>0) {
						$instance = new DryCalendarRecord;
						$instance->timestr = $timestr;
						$instance->event_id = $event_id;
						$instance->alt_text = $alt_text;
						if ($instance->event_id AND $instance->timestr != "choose") {
							$instance->dateYmd = $remain;
							if ($instance->save()) {
								$success_add++; 
							} else { 
								$out .= "<p>Error in 'add'</p><pre>".print_r($instance->getErrors(),true)."</pre>";  
							}
						}
					}
					$add++;
					break;
				case "del":
					$instance = new DryCalendarRecord;
					if ($instance->deleteByPk($remain)) {
						$success_del++;
						$del++;
					}
					break;
			}
		}
		
		// Save the 'view' html
		$view = new DryCalendar_ViewsRecord;
		$view->subsetId     = craft()->request->getParam('subsetId');
		$view->startDateYmd = craft()->request->getParam('desiredStartYmd');
		$view->endDateYmd   = craft()->request->getParam('desiredEndYmd');
		$view1 = $view->findByAttributes(array('startDateYmd'=>$view->startDateYmd, 'endDateYmd'=>$view->endDateYmd));
		if ($view1) {
			$view = $view1;
		} else {
			$view->subsetId     = craft()->request->getParam('subsetId');
			$view->startDateYmd = craft()->request->getParam('desiredStartYmd');
			$view->endDateYmd   = craft()->request->getParam('desiredEndYmd');
		}
		$view->htmlBefore   = craft()->request->getParam('htmlBefore');
		$view->htmlAfter    = craft()->request->getParam('htmlAfter');
		$view->save();
		
		$out .= "<p style='color:red'>$success_add of $add records added. $success_del of $del records deleted.</p>\n";
      craft()->urlManager->setRouteVariables(array('calupdateResponse' => $out));
	}

}