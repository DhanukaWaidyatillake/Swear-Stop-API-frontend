<?php

//Artisan::command('inspire', function () {
//    $this->comment(Inspiring::quote());
//})->purpose('Display an inspiring quote')->hourly();


\Illuminate\Support\Facades\Schedule::command('signup_secret:refresh')->everyMinute();

\Illuminate\Support\Facades\Schedule::command('trigger-subscription-renewal')->hourly();
