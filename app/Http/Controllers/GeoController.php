<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoController extends Controller
{
    public function reverse(Request $request)
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        // Nominatim: jangan terlalu presisi biar cache kepake & stabil
        $lat = round((float) $data['lat'], 5);
        $lng = round((float) $data['lng'], 5);

        $cacheKey = "revgeo:{$lat},{$lng}";

        $result = Cache::remember($cacheKey, now()->addDays(7), function () use ($lat, $lng) {
            $url = 'https://nominatim.openstreetmap.org/reverse';

            $resp = Http::withHeaders([
                // Penting: Nominatim minta User-Agent jelas
                'User-Agent' => 'SEKURITI-PGNCOM/1.0 (internal project)',
                'Accept-Language' => 'id',
            ])->timeout(10)->get($url, [
                'format' => 'jsonv2',
                'lat' => $lat,
                'lon' => $lng,
                'addressdetails' => 1,
            ]);

            if (!$resp->ok()) {
                return null;
            }

            $json = $resp->json();
            $addr = $json['address'] ?? [];

            // Susun versi "manusia"
            $road = $addr['road'] ?? $addr['pedestrian'] ?? $addr['path'] ?? null;

            $suburb = $addr['suburb'] ?? $addr['neighbourhood'] ?? null;
            $village = $addr['village'] ?? $addr['hamlet'] ?? $addr['city_district'] ?? null;

            $district = $addr['district'] ?? $addr['county'] ?? null; // seringnya kecamatan/area admin
            $city = $addr['city'] ?? $addr['town'] ?? $addr['municipality'] ?? null; // kab/kota
            $state = $addr['state'] ?? null; // provinsi

            // Format 3-4 baris seperti contoh kamu
            $lines = [];

            // Baris 1: jalan (kalau ada), kalau tidak: kampung/kelurahan
            $lines[] = $road ?: ($village ?: 'Lokasi tidak dikenal');

            // Baris 2: Kecamatan (kalau ada)
            if ($district) $lines[] = "Kecamatan {$district}";

            // Baris 3: Kabupaten/Kota (kalau ada)
            if ($city) $lines[] = (str_contains(strtolower($city), 'kota') || str_contains(strtolower($city), 'kabupaten'))
                ? $city
                : "Kabupaten/Kota {$city}";

            // Baris 4: Provinsi (kalau ada)
            if ($state) $lines[] = $state;

            return [
                'lat' => $lat,
                'lng' => $lng,
                'lines' => $lines,
                'raw' => $addr, // optional, kalau mau debug
            ];
        });

        if (!$result) {
            return response()->json([
                'ok' => false,
                'lines' => ['Lokasi tidak tersedia'],
            ], 200);
        }

        return response()->json([
            'ok' => true,
            'lines' => $result['lines'],
            'lat' => $result['lat'],
            'lng' => $result['lng'],
        ]);
    }
}
