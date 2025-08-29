<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Device;
use App\Models\Notification;
use App\Models\NotificationUser;


class NotificationSendHelper
{
    public static function sendNotification( $array )
    {
        $devices = [];

        $insert_id = new Notification();
//        $insert_id->user_id = $array['user_id'];
        $insert_id->save();
        $android_array = [];
        $ios_array = [];

        NotificationUser::create([
            'notification_id' => $insert_id->id,
            'user_id'         => $array['user_id'],
        ]);

        $device = new Device();
        $android_devices = Device::where('user_id', $array['user_id'])
            ->where('device_type', 'Android')
            ->get();
        $ios_devices = Device::where('user_id', $array['user_id'])
            ->where('device_type', 'iOS')
            ->get();

        foreach ($android_devices as $android_device) {
            $array['title'] = $insert_id->title;
            $array['message'] = $insert_id->message;
            $android_array[] = $android_device->device_token;
        }

        $array['android_array'] = array_unique($android_array);
        $array['ios_array'] = [];
        $device->sendNotification($array);


        foreach ($ios_devices as $ios_device) {
            $array['title'] = $insert_id->title;
            $array['message'] = $insert_id->message;
            $ios_array[] = $ios_device->device_token;
        }
        $array['android_array'] = [];
        $array['ios_array'] = array_unique($ios_array);
        $device = new Device();
        $device->sendNotification($array);

        $insert_id->save();

    }

}
