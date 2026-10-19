<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function consulta1()
    {
        return response()->json(User::with(['role', 'userCivil'])->get());
    }

    public function index()
    {
        return response()->json(User::all());
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'    => 'required|unique:users,email',
            'password' => 'required',
        ]);

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        $data['Fecha_registro'] = now();

        return response()->json(User::create($data), 201);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $data = $request->all();
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    public function consulta3(){
        $users = User::all();
        return $users;
    }
   public function consulta4(){
    $users = User::where('Telefono', 'like', '300%')->get();
    return $users;
}
  public function prueba_user(){
    $user = User::find(1);
    return [
        "user" => $user,
        "role" => $user->role,
        "userCivil" => $user->userCivil,
    ];
}
}
