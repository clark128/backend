<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MotorcycleController extends Controller
{
    /**
     * Transform the image paths to proper URLs
     */
    private function transformImagePaths($motorcycle)
    {
        if ($motorcycle->image_path) {
            // Normalize path separators
            $path = str_replace('\\', '/', $motorcycle->image_path);
            // Remove any 'public/' or 'storage/' prefix
            $path = preg_replace('/^(public\/|storage\/)*/', '', $path);
            // Ensure the path starts with motorcycle_images/ if it doesn't already
            if (!str_starts_with($path, 'motorcycle_images/')) {
                $path = 'motorcycle_images/' . $path;
            }
            $motorcycle->image_path = $path;
        }

        if ($motorcycle->specification_image_path) {
            // Normalize path separators
            $path = str_replace('\\', '/', $motorcycle->specification_image_path);
            // Remove any 'public/' or 'storage/' prefix
            $path = preg_replace('/^(public\/|storage\/)*/', '', $path);
            // Ensure the path starts with specification_images/ if it doesn't already
            if (!str_starts_with($path, 'specification_images/')) {
                $path = 'specification_images/' . $path;
            }
            $motorcycle->specification_image_path = $path;
        }

        return $motorcycle;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $motorcycles = Motorcycle::all();
        
        // Transform each motorcycle to include full URLs
        $motorcycles->transform(function ($motorcycle) {
            return $this->transformImagePaths($motorcycle);
        });

        return response()->json($motorcycles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'features' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'specification_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['name', 'price', 'features', 'description']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('motorcycle_images', 'public');
        }

        if ($request->hasFile('specification_image')) {
            $data['specification_image_path'] = $request->file('specification_image')->store('specification_images', 'public');
        }

        $motorcycle = Motorcycle::create($data);
        
        // Transform the response to include full URLs
        $motorcycle = $this->transformImagePaths($motorcycle);

        return response()->json($motorcycle, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Motorcycle $motorcycle)
    {
        // Transform the response to include full URLs
        $motorcycle = $this->transformImagePaths($motorcycle);

        return response()->json($motorcycle);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Motorcycle $motorcycle)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'features' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'specification_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['name', 'price', 'features', 'description']);

        if ($request->hasFile('image')) {
            if ($motorcycle->image_path) {
                Storage::disk('public')->delete($motorcycle->image_path);
            }
            $data['image_path'] = $request->file('image')->store('motorcycle_images', 'public');
        } elseif ($request->input('remove_image')) {
            if ($motorcycle->image_path) {
                Storage::disk('public')->delete($motorcycle->image_path);
            }
            $data['image_path'] = null;
        }

        if ($request->hasFile('specification_image')) {
            if ($motorcycle->specification_image_path) {
                Storage::disk('public')->delete($motorcycle->specification_image_path);
            }
            $data['specification_image_path'] = $request->file('specification_image')->store('specification_images', 'public');
        } elseif ($request->input('remove_specification_image')) {
            if ($motorcycle->specification_image_path) {
                Storage::disk('public')->delete($motorcycle->specification_image_path);
            }
            $data['specification_image_path'] = null;
        }

        $motorcycle->update($data);

        // Transform the response to include full URLs
        $motorcycle = $this->transformImagePaths($motorcycle);

        return response()->json($motorcycle);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Motorcycle $motorcycle)
    {
        if ($motorcycle->image_path) {
            Storage::disk('public')->delete($motorcycle->image_path);
        }
        if ($motorcycle->specification_image_path) {
            Storage::disk('public')->delete($motorcycle->specification_image_path);
        }

        $motorcycle->delete();

        return response()->json(null, 204);
    }
}
