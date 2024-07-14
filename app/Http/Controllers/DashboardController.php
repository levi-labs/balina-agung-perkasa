<?php

namespace App\Http\Controllers;

use App\Models\Prediksi;
use App\Models\Training;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $training = Training::count();
        $testing = Prediksi::count();
        $user  = User::count();
        return view('pages.dashboard.index', compact('title', 'training', 'testing', 'user'));
    }
}
