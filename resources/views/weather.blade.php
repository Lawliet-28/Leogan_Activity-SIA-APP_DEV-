<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // ✅ Get city (search → last_city → default)
        $city = $request->city ?? ($user->last_city ?? 'Manila');

        $apiKey = config('services.weather.key');

        try {
            // ✅ CACHE (10 minutes)
            $weather = Cache::remember("weather_" . strtolower($city), 600, function () use ($city, $apiKey) {

                $response = Http::withoutVerifying()->get(
                    'https://api.weatherapi.com/v1/forecast.json',
                    [
                        'key' => $apiKey,
                        'q' => $city,
                        'days' => 3,
                        'aqi' => 'no',
                        'alerts' => 'no'
                    ]
                );

                // ❌ API failed inside cache
                if ($response->failed()) {
                    return null;
                }

                return $response->json();
            });

        } catch (\Exception $e) {
            // ❌ Network / unexpected error
            $weather = null;
        }

        // ✅ Save last searched city
        if ($request->city && $user) {
            $user->last_city = $request->city;
            $user->save();
        }

        // 👥 Admin gets users list
        if ($user && strtolower($user->role) === 'admin') {
            $users = User::all();
            return view('dashboard', compact('weather', 'users'));
        }

        // 👤 Normal user view
        return view('user-weather', compact('weather'));
    }
}
