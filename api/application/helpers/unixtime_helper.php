<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function from_unix_timespan_to_string($start_timestamp, $stop_timestamp)
{
    $span = $stop_timestamp - $start_timestamp;
    if($span==0) return '00:00:00';
    $hours=floor($span/3600);
    $minutes=floor(($span-$hours*3600)/60);
    $seconds=$span - $hours*3600 - $minutes*60;
    $hours = ($hours<10) ? '0'.$hours : $hours;
    $minutes = ($minutes<10) ? '0'.$minutes : $minutes;
    $seconds = ($seconds<10) ? '0'.$seconds : $seconds;
    return $hours.':'.$minutes.':'.$seconds;
}