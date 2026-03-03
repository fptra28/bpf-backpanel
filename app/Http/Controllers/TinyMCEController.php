<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TinyMCEController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);

        $file = $request->file('file');
        $name = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $destination = public_path('img/uploads');

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $name);

        return response()->json([
            'location' => url('/img/uploads/' . $name),
        ]);
    }
}
