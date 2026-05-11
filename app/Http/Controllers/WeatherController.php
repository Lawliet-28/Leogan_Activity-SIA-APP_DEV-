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
        // ✅ HANDLE CITY + RECENT SEARCHES
        if ($request->filled('city')) {
            $city = $request->city;

            // 🔥 Get existing history
            $history = session('recent_cities', []);

            // 🔁 Remove duplicate (case-insensitive)
            $history = array_filter($history, function ($c) use ($city) {
                return strtolower($c) !== strtolower($city);
            });

            // ➕ Add new city to top
            array_unshift($history, $city);

            // ✂️ Limit to 5 recent searches
            $history = array_slice($history, 0, 5);

            // 💾 Save to session
            session([
                'last_city' => $city,
                'recent_cities' => $history
            ]);

        } else {
            $city = session('last_city', 'Manila');
        }

        // ✅ GET RECENT SEARCHES
        $recentCities = session('recent_cities', []);

        // 🔐 API KEY
        $apiKey = config('services.weather.key');

        // 🔥 CACHE KEY
        $cacheKey = 'weather_' . strtolower($city);

        // 🌦️ FETCH WEATHER
        $weather = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($city, $apiKey) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(5)
                    ->retry(2, 1000)
                    ->get('https://api.weatherapi.com/v1/forecast.json', [
                        'key' => $apiKey,
                        'q' => $city,
                        'days' => 3,
                        'aqi' => 'no',
                        'alerts' => 'no'
                    ]);

                if ($response->failed()) {
                    return null;
                }

                return $response->json();

            } catch (\Exception $e) {
                return null;
            }
        });

        // 👤 AUTH USER
        $user = auth()->user();

        // 👥 USERS (admin only)
        $users = [];

        if ($user && strtolower($user->role) === 'admin') {
            $users = User::all();

            return view('dashboard', [
                'weather' => $weather,
                'users' => $users,
                'user' => $user,
                'city' => $city,
                'recentCities' => $recentCities, // 🔥 ADD THIS
            ]);
        }

        // 👤 NORMAL USER
        return view('user-weather', [
            'weather' => $weather,
            'user' => $user,
            'city' => $city,
            'recentCities' => $recentCities, // 🔥 ADD THIS
        ]);
    }
}
