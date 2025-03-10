<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:1')->only(['index', 'show']); // Hanya Super_admin
    }

    public function index()
    {
        $users = User::with('role', 'unit')->get();
        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('role', 'unit')->findOrFail($id);
        return view('users.show', compact('user'));
    }
}