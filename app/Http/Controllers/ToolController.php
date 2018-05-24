<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToolController extends Controller
{
    public function generate_unique_string($table, $column, $length)
    {
        $string = str_random($length);
        $result = DB::table($table)->where($column, $string)->first();
        if ($result) {
            return $this->generate_unique_string($table, $column, $length);
        }else{
            return $string;
        }
    }
}
