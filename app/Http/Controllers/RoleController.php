<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function consulta1(){
        $roles = Role::select('Nombre_rol')->get();
        return $roles;
    }
}
