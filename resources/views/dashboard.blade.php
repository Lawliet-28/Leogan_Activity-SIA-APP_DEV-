<x-app-layout>

<x-slot name="header">
    <h2 class="text-xl font-bold">Dashboard</h2>
</x-slot>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: #f5f7fb;
    }

    .card {
        border: none;
        border-radius: 14px;
    }

    .shadow-sm {
        box-shadow: 0 6px 18px rgba(0,0,0,0.06) !important;
    }

    .avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #0d6efd;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto;
    }
</style>

<div class="container py-4">

    <!-- 🔝 TOP ROW -->
    <div class="row mb-4">

        <!-- 👤 PROFILE -->
        <div class="col-md-4 mb-3">
            <div class="card p-4 shadow-sm h-100 text-center">

                <div class="avatar mb-3">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <h5 class="fw-bold mb-1">
                    {{ $user->full_name ?? $user->name }}
                </h5>

                <p class="text-muted mb-2">
                    {{ $user->email }}
                </p>

                <span class="badge bg-primary px-3 py-2">
                    {{ $user->role ?? 'User' }}
                </span>

            </div>
        </div>

        <!-- 🌦️ WEATHER -->
        <div class="col-md-8 mb-3">
            <div class="card p-4 shadow-sm h-100 text-center">

                <h5 class="fw-bold mb-2">🌦️ Weather</h5>

                @if($city)
                    <p class="text-muted">
                        Showing results for: <strong>{{ $city }}</strong>
                    </p>
                @endif

                <form method="GET" action="{{ route('weather') }}" class="d-flex justify-content-center mb-3">
                    <input
                        type="text"
                        name="city"
                        class="form-control w-50"
                        placeholder="Enter city..."
                        value="{{ $city }}"
                    >
                    <button class="btn btn-primary ms-2">Search</button>
                </form>

                @if(!$weather)
                    <div class="alert alert-danger mt-3">
                        ⚠️ Unable to load weather data.
                    </div>
                @elseif(isset($weather['location']))

                    <h4>{{ $city }}</h4>

                    <small class="text-muted">
                        {{ $weather['location']['country'] }}
                    </small>

                    <div class="my-2">
                        <img src="{{ $weather['current']['condition']['icon'] }}" width="80">
                    </div>

                    <h1 class="fw-bold">
                        {{ $weather['current']['temp_c'] ?? '--' }}°C
                    </h1>

                    <p class="text-muted">
                        {{ $weather['current']['condition']['text'] ?? '' }}
                    </p>

                    <div class="d-flex justify-content-around mt-3">
                        <div>
                            <strong>{{ $weather['current']['humidity'] ?? '--' }}%</strong><br>
                            <small class="text-muted">Humidity</small>
                        </div>

                        <div>
                            <strong>{{ $weather['current']['wind_kph'] ?? '--' }}</strong><br>
                            <small class="text-muted">Wind</small>
                        </div>
                    </div>

                @endif

            </div>
        </div>

    </div>

    <!-- 🕓 RECENT SEARCHES (CARD STYLE) -->
    @if(!empty($recentCities))
    <div class="card p-4 shadow-sm mb-4">

        <h5 class="fw-bold mb-3">🕓 Recent Searches</h5>

        <div class="row">
            @foreach($recentCities as $recent)

                <div class="col-md-3 mb-2">
                    <a href="{{ route('weather', ['city' => $recent]) }}"
                       class="btn btn-outline-primary w-100">
                        {{ $recent }}
                    </a>
                </div>

            @endforeach
        </div>

    </div>
    @endif

    <!-- 👥 USERS -->
    <div class="card p-3 shadow-sm">

        <h5 class="fw-bold mb-3">👥 Users</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>{{ $u['id'] ?? '-' }}</td>
                            <td>{{ $u['full_name'] ?? $u['name'] ?? '-' }}</td>
                            <td>{{ $u['email'] ?? '-' }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $u['role'] ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

</div>

</x-app-layout>
