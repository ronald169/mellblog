<?php

use Carbon\Carbon;

if (!function_exists('generateRandomDateInRange')) {
    function generateRandomDateInRange($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $difference = $end->timestamp - $start->timestamp;

        $randomSeconds = rand(0, $difference);

        return $start->copy()->addSeconds($randomSeconds);
    }
}
