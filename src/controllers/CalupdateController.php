<?php
namespace kr37\drycalendar\controllers;

use Craft;
use yii\web\Controller;
use craft\web\Request;
use craft\web\UrlManager;

use kr37\drycalendar\records\Drycalendar;
use kr37\drycalendar\records\DrycalendarViews;

class CalupdateController extends Controller
{

    public $defaultAction = 'AddAndDeleteInstances3';

	public function actionAddAndDeleteInstances() {
		// Update from POST
		//==================

		$out = '';
		$add = 0;
        $del = 0;
        $success_add = 0;
        $success_del = 0;
		$allTheQueryParams  = $_POST;
        $request = Craft::$app->getRequest();
		$timestr  = $request->getParam('time1');
		$event_id = $request->getParam('post_id');
		$alt_text = $request->getParam('alt_text');

		foreach($allTheQueryParams as $name => $value) {
			$first3 = substr($name,0,3);
			$remain = substr($name,3);
			switch ($first3) {
				case "add":
					if (intval($timestr)!=0 && $event_id>0) {
						$occurrence = new Drycalendar;
						$occurrence->timestr = $timestr;
						$occurrence->event_id = $event_id;
						$occurrence->alt_text = $alt_text;
						if ($occurrence->event_id AND $occurrence->timestr != "choose") {
							$occurrence->dateYmd = $remain;
							if ($occurrence->save()) {
								$success_add++;
							} else {
                                $out .= "<p>Error in 'add'</p><pre>"
                                     .print_r($occurrence->getErrors(),true)."</pre>";
							}
						}
					}
					$add++;
					break;
				case "del":
                    $occurrence = Drycalendar::findOne($remain);
					if ($occurrence && $occurrence->delete()) {
						$success_del++;
						$del++;
					}
					break;
			}
		}

		// Save the 'view' html
		$view = new DrycalendarViews;
		$view->subsetId     = $request->getParam('subsetId');
		$view->startDateYmd = $request->getParam('desiredStartYmd');
		$view->endDateYmd   = $request->getParam('desiredEndYmd');
        /*$view1 = $view->find()
            ->where(['startDateYmd'=>$view->startDateYmd, 'endDateYmd'=>$view->endDateYmd]);
		if ($view1) {
			$view = $view1;
		} else {
			$view->subsetId     = $request->getParam('subsetId');
			$view->startDateYmd = $request->getParam('desiredStartYmd');
			$view->endDateYmd   = $request->getParam('desiredEndYmd');
        }*/
		$view->htmlBefore   = $request->getParam('htmlBefore');
		$view->htmlAfter    = $request->getParam('htmlAfter');
		$view->save();

		$out .= "<p style='color:red'>$success_add of $add records added. $success_del of $del records deleted.<br>{$view->htmlBefore}</p>\n";
        Craft::$app->getUrlManager()->setRouteParams(['calupdateResponse' => $out]);
	}

}
