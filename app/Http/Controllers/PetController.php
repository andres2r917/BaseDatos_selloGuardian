<?php

namespace App\Http\Controllers;

use App\Models\Pet;

class PetController extends Controller
{
    // Adopcion.jsx: solo las mascotas que se pueden adoptar
    public function index()
    {
        return Pet::where('Estado_adopcion', 'disponible')->latest()->get();
    }
}
