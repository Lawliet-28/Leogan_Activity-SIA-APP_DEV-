<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // 👤 Logged-in user
        $user = auth()->user();
//WEather section
      try {
    $weatherResponse = Http::withoutVerifying()
        ->timeout(5)
        ->retry(2, 200)
        ->get('https://api.weatherapi.com/v1/current.json', [
            'key' => config('services.weather.key'),
            'q' => 'Manila'
        ]);

    if ($weatherResponse->successful()) {
        $weather = $weatherResponse->json();
    } else {
        $weather = null;
    }

} catch (\Exception $e) {
    $weather = null;
}


        // 👥 Your API (users list)
       $users = User::all();

        return view('dashboard', compact('user', 'weather', 'users'));
    }
}
