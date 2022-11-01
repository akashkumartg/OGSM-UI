<?php

namespace App\Models;

use CodeIgniter\Model;

class Common extends Model
{

    public function sendNotification($notifData, $token)
    {

        $url = "https://fcm.googleapis.com/fcm/send";

        $subscription_key  = "key=AAAA4M3AJk8:APA91bGBdssHrFK-AkDBdBKmjBQknSk8CEI7qxySPOKR0kP10FPA6lqg4mkALMneMgPErqHhawctrfzjIcfMM45p1JFByWwsdtz8QHtyeiYZxd7MLuPf-At363tgIrVaTyMoMWVWeI7O";

        $request_headers = array(
            "Authorization:" . $subscription_key,
            "Content-Type: application/json"
        );

        $postRequest = [
            "notification" => [
                "title" =>  $notifData['title'],
                "body" =>  $notifData['body'],
                "click_action" => $notifData['click_action'],
                "icon" =>  "https://image.slidesharecdn.com/intelligent-ly-deckogsmfordist-120606073515-phpapp01/95/ogsm-strategy-framework-2-638.jpg?cb=1370807598",
            ],
            "to" =>  $token
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $season_data = curl_exec($ch);

        if (curl_errno($ch)) {
            print "Error: " . curl_error($ch);
            return  curl_error($ch);
            exit();
        }

        curl_close($ch);
        $json = json_decode($season_data, true);
        return $json;
    }

    public function initCurlPost($postdata, $url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    public function sendUwlNotification($email, $name, $title, $description, $type, $app, $page, $value, $actionLink)
    {
        $url = 'https://apps.t10.me/worklist/api/v1/work-item';
        $dataArr = [
            "email" =>  $email,
            "workItem" => [
                "name" => $name,
                "title" => $title,
                "description" => $description
            ],
            "notification" => [
                "title" => $title,
                "body" => $description
            ],
            "app" => [
                "type" => $type, // app or site
                "name" => $app, // MSFA or MVPL
                "page" => $page, // dist-appraisal,
                "value" => $value,
                "actionLink" => $actionLink
            ]
        ];
        $postdata = json_encode($dataArr);
        $data = $this->initCurlPost($postdata, $url);

        if ($data) {
            return $data;
        } else {
            return false;
        }
    }
}
