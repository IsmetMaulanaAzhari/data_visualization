@extends('layouts.app')

@section('title', 'Weather - All Cities')

@section('content')
<div class="mb-8">
    <h1 class="text-5xl font-bold bg-gradient-to-r from-blue-600 via-cyan-500 to-teal-600 bg-clip-text text-transparent mb-2">
        Detail Cuaca Semua Kota
    </h1>
    <p class="text-gray-600 text-lg">Informasi lengkap cuaca untuk semua kota di Pulau Jawa</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($allWeather as $city => $data)
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white overflow-hidden hover:shadow-lg transition-all duration-300 card-hover">
        <div class="bg-gradient-to-r from-blue-500 via-cyan-500 to-teal-500 p-5 text-white">
            <h3 class="text-2xl font-bold">
                📍 {{ $city }}
            </h3>
            <p class="text-sm text-blue-100 mt-1">
                Lat: {{ $cities[$city]['lat'] }}, Lon: {{ $cities[$city]['lon'] }}
            </p>
        </div>
        
        @if($data['success'] && $data['current'])
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    @php
                        $code = $data['current']['weather_code'];
                        $emoji = match(true) {
                            $code == 0 => '☀️',
                            $code <= 3 => '⛅',
                            $code <= 48 => '🌫️',
                            $code <= 65 => '🌧️',
                            $code <= 77 => '❄️',
                            $code <= 82 => '⛈️',
                            default => '⚡',
                        };
                    @endphp
                    <span class="text-6xl">{{ $emoji }}</span>
                </div>
                <div class="text-right">
                    <p class="text-5xl font-bold text-gray-800">{{ round($data['current']['temperature']) }}°C</p>
                    <p class="text-gray-600 mt-1 capitalize">{{ $data['current']['weather_description'] }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <p class="text-gray-600 text-xs">Terasa Seperti</p>
                    <p class="font-semibold text-blue-700 text-lg">{{ round($data['current']['apparent_temperature']) }}°C</p>
                </div>
                <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-100">
                    <p class="text-gray-600 text-xs">Kelembaban</p>
                    <p class="font-semibold text-cyan-700 text-lg">{{ $data['current']['humidity'] }}%</p>
                </div>
                <div class="bg-teal-50 rounded-lg p-4 border border-teal-100">
                    <p class="text-gray-600 text-xs">Kecepatan Angin</p>
                    <p class="font-semibold text-teal-700 text-lg">{{ round($data['current']['wind_speed']) }} km/h</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                    <p class="text-gray-600 text-xs">Presipitasi</p>
                    <p class="font-semibold text-purple-700 text-lg">{{ $data['current']['precipitation'] }} mm</p>
                </div>
            </div>

            <!-- Mini 3-Day Forecast -->
            @if(count($data['daily']) >= 3)
            <div class="mt-5 pt-5 border-t border-gray-200">
                <p class="text-xs text-gray-600 font-semibold mb-4">📅 Prakiraan 3 Hari ke Depan</p>
                <div class="flex justify-between gap-2">
                    @for($i = 1; $i < 4 && $i < count($data['daily']); $i++)
                    <div class="flex-1 text-center bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-600 font-medium">{{ substr($data['daily'][$i]['day_name'], 0, 3) }}</p>
                        @php
                            $dayCode = $data['daily'][$i]['weather_code'];
                            $dayEmoji = match(true) {
                                $dayCode == 0 => '☀️',
                                $dayCode <= 3 => '⛅',
                                $dayCode <= 48 => '🌫️',
                                $dayCode <= 65 => '🌧️',
                                $dayCode <= 77 => '❄️',
                                $dayCode <= 82 => '⛈️',
                                default => '⚡',
                            };
                        @endphp
                        <p class="text-2xl my-2">{{ $dayEmoji }}</p>
                        <p class="text-xs font-semibold text-gray-700">{{ round($data['daily'][$i]['temp_max']) }}°</p>
                    </div>
                    @endfor
                </div>
            </div>
            @endif
        </div>
        @else
        <div class="p-6 text-center text-red-600">
            <p class="text-2xl mb-2">⚠️</p>
            <p class="font-medium">Data cuaca tidak tersedia</p>
        </div>
        @endif
    </div>
    @endforeach
</div>

<div class="mt-8 text-center">
    <a href="{{ route('weather.refresh') }}" class="inline-block px-8 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium">
        🔄 Perbarui Semua Data
    </a>
</div>
@endsection
