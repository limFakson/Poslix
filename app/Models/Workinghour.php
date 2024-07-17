<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workinghour extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'enable_workinghours',
        'sunday',
        'sunday_first_time_start',
        'sunday_first_time_end',
        'sunday_second_time_enable',
        'sunday_second_time_start',
        'sunday_second_time_end',
        'monday',
        'monday_first_time_start',
        'monday_first_time_end',
        'monday_second_time_enable',
        'monday_second_time_start',
        'monday_second_time_end',
        'tuesday',
        'tuesday_first_time_start',
        'tuesday_first_time_end',
        'tuesday_second_time_enable',
        'tuesday_second_time_start',
        'tuesday_second_time_end',
        'wednesday',
        'wednesday_first_time_start',
        'wednesday_first_time_end',
        'wednesday_second_time_enable',
        'wednesday_second_time_start',
        'wednesday_second_time_end',
        'thursday',
        'thursday_first_time_start',
        'thursday_first_time_end',
        'thursday_second_time_enable',
        'thursday_second_time_start',
        'thursday_second_time_end',
        'friday',
        'friday_first_time_start',
        'friday_first_time_end',
        'friday_second_time_enable',
        'friday_second_time_start',
        'friday_second_time_end',
        'saturday',
        'saturday_first_time_start',
        'saturday_first_time_end',
        'saturday_second_time_enable',
        'saturday_second_time_start',
        'saturday_second_time_end',
    ] ;
}