<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApirequestController extends Controller
{
    public function generate_e_materai(Request $request)
    {
        // Validate that 'img_name' is present in the request
        $request->validate([
            'img_name' => 'required|string',
        ]);

        $img_name = $request->input('img_name');

        // Define the correct image path using storage_path helper
        $img_path = storage_path('app/private/emeterai_path/' . $img_name);

        // Check if the image exists
        if (!file_exists($img_path)) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        // Convert the image to base64
        $imgbase64 = base64_encode(file_get_contents($img_path));

        // Return the image in base64 format
        return response()->json(['imgbase64' => $imgbase64], 200);
    }
}
