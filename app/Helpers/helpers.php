<?php
   
use Carbon\Carbon;

function convertDateID($original_date)
{
    $days = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];
    $months = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];
    $day = Carbon::parse($original_date)->format('N');
    $date = Carbon::parse($original_date)->format('d');
    $month = Carbon::parse($original_date)->format('F');
    $year = Carbon::parse($original_date)->format('Y');
    
    return $days[$day] . ', ' . $date . ' ' . $months[$month] . ' ' . $year;
}

function hourMinuteOnly($original_time){
    $time = Carbon::parse($original_time)->format('H:i');
    return $time;
}