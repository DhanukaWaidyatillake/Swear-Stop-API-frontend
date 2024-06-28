<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class ToastMessageService
{
    public function showToastMessage($type,$message)
    {
        Session::put('toast',[
            'type' => $type,
            'message' => $message
        ]);
    }
}
