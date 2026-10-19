<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrator;

class AdministratorController extends Controller
{
    public function consulta12(){
        $administrators = Administrator::all();
        return $administrators;
    }
    
}