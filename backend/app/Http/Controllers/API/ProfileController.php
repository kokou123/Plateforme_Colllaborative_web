<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Supprimer l'ancienne photo si elle existe
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        // Stocker la nouvelle photo
        $path = $request->file('photo')->store('photos', 'public');

        $user->update(['photo' => $path]);

        return response()->json([
            'message' => 'Photo de profil mise à jour',
            'user'    => $user,
            'photo_url' => Storage::url($path),
        ]);
    }
}