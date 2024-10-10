<?php

namespace App\Utillities;

class Constant
{
    /*
  | ---------------------------------------------------
  |  Các hằng số, role dùng chung toàn hệ thống
  | ---------------------------------------------------
  */

    //used
    const user_level_host = 0;
    const user_level_developer = 1;
    const user_level_employer = 2;
    const user_status_active = 3;
    const user_status_inactive = 4;
    const start_date = 'start-date';
    const end_date = 'start-date';

    public static $user_level = [
        self::user_level_host => 'host',
        self::user_level_developer => 'developer',
        self::user_level_employer => 'employer',
        self::user_status_active => 'active',
        self::user_status_inactive => 'inactive',
    ];

}
