<?php

namespace App\Helper;

use App\Models\Notification;
use App\Models\NotificationRecipient;
use DateTime;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Auth;
use App\Models\User;

class Helper
{
    public static function monthInThisYear()
    {
        $curr_date = date('m');
        $months = array();
        $monthsName = array();
        for ($i = 1; $i <= $curr_date; $i++) {
            $months[] = $i;
            $monthsName[] = Helper::monthNumberToName($i);
        }
        return array($months, $monthsName);
    }

    public static function randomColor($array)
    {
        $color = array();
        if (gettype($array) == 'array') {
            $length = count($array);
        } else {
            $length = $array;
        }
        for ($i = 0; $i < $length; $i++) {
            $string1 = (rand(0, 255));
            $string2 = (rand(0, 255));
            $string3 = (rand(0, 255));
            $colorString = 'rgb(' . $string1 . ',' . $string2 . ',' . $string3 . ')';
            array_push($color, $colorString);
        }
        return $color;
    }

    public static function dayInWeek()
    {
        $week1 = array('1', '7');
        $week2 = array('8', '14');
        $week3 = array('15', '21');
        $week4 = array('22', '31');
        $result = array();
        array_push($result, $week1, $week2, $week3, $week4);
        return $result;
    }

    public static function weekLabel()
    {
        $name = array('Week 1', 'Week 2', 'Week 3', 'Week 4');
        return $name;
    }

    public static function daysInThisMonth()
    {
        $labels = array();
        $numberOfDays = date('t');
        for ($i = 1; $i <= $numberOfDays; $i++) {
            array_push($labels, $i);
        }
        return $labels;
    }

    public static function twoDaysDifferentInMonth()
    {
        $labels = array();
        $data = array();
        $numberOfDays = date('t');
        for ($i = 1; $i <= $numberOfDays; $i += 2) {
            array_push($data, array($i, $i + 1));
            if ($i + 1 > $numberOfDays)
                array_push($labels, $i);
            else
                array_push($labels, $i . '-' . ($i + 1));
        }
        return array($labels, $data);
    }

    public static function calculateWeekByMonth($month, $year)
    {
    }

    public static function monthNumberToName($month)
    {
        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('M'); // March
        return $monthName;
    }

    public static function configNotification($user_token, $user, $title, $body, $notification_data, $user_type)
    {
        if ($user_token) {
            $notification = array(
                'to' => $user_token,
                'sound' => 'default',
                'title' =>  $title,
                'body' =>   $body,
                'data' => json_encode($notification_data),
                'priority' => 'high'
            );
            Helper::sendNotification($notification);
        }

        $notification_id = Notification::create([
            'title' =>  $title,
            'message' => $body,
            'user_type' => $user_type,
            'data' => json_encode($notification_data)
        ])->id;
        if (gettype($user) == 'array') {
            foreach ($user as $value) {
                NotificationRecipient::create([
                    'user_id' =>  $value,
                    'notification_id' => $notification_id
                ]);
            };
        } else {
            NotificationRecipient::create([
                'user_id' =>  $user,
                'notification_id' => $notification_id
            ]);
        }
    }

    public static function sendNotification($payload)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://exp.host/--/api/v2/push/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Accept-Encoding: gzip, deflate",
                "Content-Type: application/json",
                "cache-control: no-cache",
                "host: exp.host"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
    }

    public static function sendEmail($to, $subject, $body, $attachment = '')
    {

        $mail = new PHPMailer();

        try {
            $mail->IsSMTP();
            $mail->SMTPDebug  = 0;
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host       = "webmail.nucycle.com.my";
            $mail->Port       = 465;
            $mail->AddAddress($to);
            if ($attachment)
                $mail->addAttachment($attachment);
            $mail->Username = "noreply@nucycle.com.my";
            $mail->Password = "N0rep1y@LgMy99";
            $mail->FromName = "NuCycle";
            $mail->SetFrom("noreply@nucycle.com.my", "NuCycle");
            $mail->AddReplyTo("noreply@nucycle.com.my", "NuCycle");
            $mail->Subject    = $subject;
            $mail->Body = $body;
            $mail->Send();
        } catch (Exception $e) {
        }
    }

    public static function getCollectionHubId()
    {
        if (in_array(4, Auth::user()->users_roles_id())) {
            return (Auth::user()->hub_admin->collection_hub_id);
        } elseif (in_array(5, Auth::user()->users_roles_id())) {
            return (Auth::user()->hub_reader[0]->collection_hub_id);
        }
    }

    public static function previousSevenDays()
    {
        for ($i = 0; $i < 7; $i++) {
            echo date("d/m", strtotime($i . " days ago")) . '<br />';
        }
    }

    public static function lpadClearanceId($id)
    {
        $do = "DO-" . str_pad($id, 4, '0', STR_PAD_LEFT);
        return $do;
    }

    public static function webSendNotification($request)
    {
        if ($request->when == 'now' || $request->when == 'now_from_scheduler') {
            $user_type = $request->user_type;
            if ($user_type == 'customer') {
                $user_token = User::where('device_token', '!=', null)->where('receive_notification', 1)->leftJoin('user_role', function ($join) {
                    $join->on('users.id', '=', 'user_role.user_id');
                })->where('user_role.role_id', 2)->pluck('device_token')
                    ->all();
                $user = User::leftJoin('user_role', function ($join) {
                    $join->on('users.id', '=', 'user_role.user_id');
                })->where('user_role.role_id', 2)->pluck('users.id')
                    ->all();
            } else if ($user_type == 'collector') {
                $user_token = User::leftJoin('user_role', function ($join) {
                    $join->on('users.id', '=', 'user_role.user_id');
                })->where('device_token', '!=', null)->where('receive_notification', 1)->where('user_role.role_id', 3)->pluck('device_token')
                    ->all();
                $user = User::leftJoin('user_role', function ($join) {
                    $join->on('users.id', '=', 'user_role.user_id');
                })->where('user_role.role_id', 3)->pluck('users.id')
                    ->all();
            } else if ($user_type == 'all') {
                $user_token = User::leftJoin('user_role', function ($join) {
                    $join->on('users.id', '=', 'user_role.user_id');
                })->where(function ($query) {
                    $query->where('device_token', '!=', null)->where('receive_notification', 1);
                })->where(function ($query) {
                    $query->where('user_role.role_id', 3)
                        ->orWhere('user_role.role_id', 2);
                })->pluck('device_token')->all();
                $user = User::leftJoin('user_role', function ($join) {
                    $join->on('users.id', '=', 'user_role.user_id');
                })->where('user_role.role_id', 3)
                    ->orWhere('user_role.role_id', 2)
                    ->pluck('users.id')->all();
            }
            $notification = array(
                'to' => $user_token,
                'sound' => 'default',
                'title' => $request->title,
                'body' =>  $request->message,
                'priority' => 'high'

            );
        }
        if ($request->when == 'now_from_scheduler') {
            $notification_id = $request->id;
            $notification_db = Notification::find($notification_id);
            $notification_db->title = $request->title;
            $notification_db->message = $request->message;
            $notification_db->status = 'published';
            $notification_db->time_sent = date("Y-m-d H:i:s");
            $notification_db->save();

            Helper::sendNotification($notification);
            foreach ($user as $value) {
                NotificationRecipient::create([
                    'user_id' =>  $value,
                    'notification_id' => $notification_id
                ]);
            };
        }
        if ($request->when == 'now') {
            Helper::sendNotification($notification);
            $notification_id = Notification::create([
                'title' =>  $request->title,
                'message' => $request->message,
                'user_type' => $request->user_type,
                'data' => null,
                'status' => 'published',
                'time_set' => date("Y-m-d H:i:s"),
                'time_sent' => date("Y-m-d H:i:s"),
            ])->id;
            foreach ($user as $value) {
                NotificationRecipient::create([
                    'user_id' =>  $value,
                    'notification_id' => $notification_id
                ]);
            };
        }
        if ($request->when == 'date') {
            Notification::create([
                'title' =>  $request->title,
                'message' => $request->message,
                'user_type' => $request->user_type,
                'data' => null,
                'status' => 'draft',
                'time_set' => $request->send_date,
                'time_sent' => null,
            ]);
        }
    }
}
