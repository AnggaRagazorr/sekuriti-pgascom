<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\PatrolSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatrolSubmitController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'area' => ['required', 'in:luar,smoking,balkon'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'address_lines' => ['nullable', 'array'],
            'address_lines.*' => ['string', 'max:255'],
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'], // max 5MB
            'submitted_at' => ['required', 'date'],
        ]);

        $user = $request->user();

        // simpan foto
        $path = $data['photo']->store('patrols/' . now()->format('Y-m-d'), 'public');

        $address = null;
        if (!empty($data['address_lines'])) {
            $address = implode("\n", $data['address_lines']);
        }

        $row = PatrolSubmission::create([
            'user_id' => $user->id,
            'barcode' => $data['barcode'] ?? null,
            'area' => $data['area'],
            'photo_path' => $path,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'address' => $address,
            'submitted_at' => $data ['submitted_at'],
        ]);

        return response()->json([
            'ok' => true,
            'id' => $row->id,
            'submitted_at' => $row->submitted_at->toDateTimeString(),
            'photo_url' => Storage::disk('public')->url($path),
        ]);
    }
}
