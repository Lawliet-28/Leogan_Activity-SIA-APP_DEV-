<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weather App</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .weather-card {
            max-width: 400px;
            margin: auto;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border: none;
        }

        .forecast-card {
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            border: none;
        }

        .search-box {
            max-width: 500px;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="container py-5 text-center">

    <!-- 🔓 LOGOUT -->
    <div class="d-flex justify-content-end mb-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-danger">Logout</button>
        </form>
        <!-- 🕓 RECENT SEARCHES -->
@if(!empty($recentCities))
<div class="card p-3 shadow-sm mb-4 mt-4 search-box">

    <h6 class="fw-bold mb-3">🕓 Recent Searches</h6>

    <div class="d-flex flex-wrap gap-2 justify-content-center">
        @foreach($recentCities as $recent)
            <a href="{{ route('weather', ['city' => $recent]) }}"
               class="btn btn-outline-primary btn-sm">
                {{ $recent }}
            </a>
        @endforeach
    </div>

</div>
@endif
    </div>

    <h2 class="mb-2">🌤 Weather App</h2>

    <!-- ✅ SHOW CURRENT CITY -->
    @if($city)
        <p class="text-muted">Showing results for: <strong>{{ $city }}</strong></p>
    @endif

    <!-- 🔍 SEARCH -->
    <form method="GET" action="{{ route('weather') }}" class="search-box d-flex mb-4">
        <input
            type="text"
            name="city"
            class="form-control"
            placeholder="Enter city..."
            value="{{ $city }}"
        >
        <button class="btn btn-primary ms-2">Search</button>
    </form>

    <!-- ❌ ERROR -->
    @if(!$weather)
        <div class="alert alert-danger">
            ⚠️ Unable to load weather data. Please try again.
        </div>
    @endif

    <!-- 🌦️ CURRENT WEATHER -->
    @if(isset($weather['location']))
        <div class="card weather-card p-4 mb-5">

            <h4>{{ $weather['location']['name'] }}</h4>
            <small>{{ $weather['location']['country'] }}</small>

            <div class="my-3">
                <img src="{{ $weather['current']['condition']['icon'] }}" width="80">
            </div>

            <h1 class="fw-bold">
                {{ $weather['current']['temp_c'] }}°C
            </h1>

            <p>{{ $weather['current']['condition']['text'] }}</p>

            <hr>

            <p>💧 Humidity: {{ $weather['current']['humidity'] }}%</p>
            <p>🌬 Wind: {{ $weather['current']['wind_kph'] }} kph</p>

        </div>
    @endif

    <!-- 📅 FORECAST -->
    @if(isset($weather['forecast']))
        <h5 class="mb-4">📅 3-Day Forecast</h5>

        <div class="row justify-content-center">

            @foreach($weather['forecast']['forecastday'] as $day)
                <div class="col-md-3 mb-3">
                    <div class="card forecast-card p-3 text-center">

                        <h6>{{ $day['date'] }}</h6>

                        <img src="{{ $day['day']['condition']['icon'] }}" width="60">

                        <p>{{ $day['day']['condition']['text'] }}</p>

                        <hr>

                        <p>🌡 Avg: {{ $day['day']['avgtemp_c'] }}°C</p>

                    </div>
                </div>
            @endforeach

        </div>
    @endif

</div>

</body>
</html>
