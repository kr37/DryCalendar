<?php
namespace kr37\drycalendar\controllers;

use Craft;
use yii\web\Controller;
use craft\web\Request;
use craft\web\UrlManager;

use kr37\drycalendar\records\Drycalendar;
use kr37\drycalendar\records\DrycalendarViews;

class TemplatingController extends Controller
{

	public function actionAttendanceUpdate() {
		// Update from POST
		//==================

		$out = '';
        $request = '';
        $change = '';
        $success_chg = '';
		$allTheQueryParams  = $_POST;

		foreach($allTheQueryParams as $name => $value) {
            $request .= "[$name]=>$value ";
            if ("" != $value) {
                $first3 = substr($name,0,3);
                $remain = substr($name,3);
                switch ($first3) {
                    case "atn":
                        $change++; 
                        $occurrence = Drycalendar::findOne($remain);
                        $occurrence->userJson = $value;
                        if ($occurrence->save()) {
                            $success_chg++;
                        } else {
                            $out .= "<p>Error in 'add'</p><pre>"
                                 .print_r($occurrence->getErrors(),true)."</pre>";
                        }
                        break;
                    case "delNOTUSINGTHIS":
                        $occurrence = Drycalendar::findOne($remain);
                        if ($occurrence && $occurrence->delete()) {
                            $success_del++;
                            $del++;
                        }
                        break;
                }
            }
		}

		$out .= "<p style='color:red'>Processed: $request</p>\n";
        Craft::$app->getUrlManager()->setRouteParams(['calupdateResponse' => $out]);
	}

}
