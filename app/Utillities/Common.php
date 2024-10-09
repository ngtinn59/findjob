<?php

namespace App\Utillities;

use Carbon\Carbon;
use Illuminate\Support\Str;

class Common
{
    public static function uploadFile($file, $path) {
        $file_name_original = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $file_without_extension = Str::replaceLast('.' . $extension, '', $file_name_original);

        $str_time_now = Carbon::now()->format('ymd_his');
        $file_name = Str::slug($file_without_extension) . '_' . uniqid() . '_' . $str_time_now . '.' . $extension;

        $file->move($path, $file_name);

        return $file_name;
    }
}
