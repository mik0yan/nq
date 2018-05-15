<?php
/**
 * Created by PhpStorm.
 * User: mikuan
 * Date: 2018/3/24
 * Time: 下午10:57
 */

namespace App\Admin\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpaController extends Controller
{
    public function index()
    {
        return view('home');
    }
}