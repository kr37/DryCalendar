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
        // userJson may contain anything, but if attendance
        // info is included then the keys will be "16+" or "<16"
		//==================

		$out = '';
        $request = '';
        $change = '';
        $success_chg = '';
		$allTheQueryParams  = $_POST;

		foreach($allTheQueryParams as $name => $value) {
            //if ($name != 'CRAFT_CSRF_TOKEN') $request .= "[$name]=>$value ";
            $first3 = substr($name,0,3);
            $remain = substr($name,3);
            if ($first3 == "16+" or $first3 == "<16") {
                $occurrence = Drycalendar::findOne($remain);
                $userArray = json_decode($occurrence->userJson, true);
                if (null == $userArray) {
                    $userArray = [];
                } else {
                    if ( array_key_exists($first3, $userArray) && $userArray[$first3] != $value) {
                        $change++; 
                        $userArray[$first3] = $value;
                        $occurrence->userJson = json_encode($userArray);
                        if ($occurrence->save()) {
                            $success_chg++;
                        } else {
                            $out .= "<p>Error in $name</p><pre>"
                                 .print_r($occurrence->getErrors(),true)."</pre>";
                        }
                    }
                }
            }
		}

		$out .= "<p style='color:red'>Processed: $request $success_chg out of $change changes</p>\n";
        Craft::$app->getUrlManager()->setRouteParams(['calupdateResponse' => $out]);
	}

}
