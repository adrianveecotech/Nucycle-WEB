<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helper\Helper;
use App\Models\NotificationRecipient;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'DESC')->get();
        return view('notification.index', compact('notifications'));
    }

    public function create()
    {
        return view('notification.create');
    }

    public function insert(Request $request)
    {
        $this->validate(
            $request,
            [
                'title' => 'required',
                'message' => 'required',
                'when' =>  'required|in:date,now',
                'send_date' => 'required_if:when,date'
            ],
            [
                'send_date.required_if' => 'The date field is required.',
                'when.required' => 'Please select when to send the notification.'
            ]
        );
        Helper::webSendNotification($request);

        return redirect()->route('notification.index')->with('successMsg', 'Notificaion is created.');
    }

    public function cancel($id){
        $notification = Notification::where('id',$id)->first();
        if($notification->status != 'draft'){
            return redirect()->route('notification.index')->with('failMsg', 'Only notification with draft status can be deleted.');
        }
        $notification->status = 'cancelled';
        $notification->save();

        return redirect()->route('notification.index')->with('successMsg', 'Notification is cancelled.');

    }
}
