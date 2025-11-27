<?php

namespace App\Http\Controllers\Backend\Admins\Views;

use App\Models\HomeSlider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class AdminHomeSliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = HomeSlider::latest()->get();
        return view('backend.admins.pages.home-slider', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admins.pages.home-slider-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'required|url',
            'status' => 'boolean'
        ]);

        // Create uploads directory if not exists
        $uploadPath = public_path('uploads/home_slider');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($uploadPath, $imageName);
            $imagePath = 'uploads/home_slider/' . $imageName;
        }

        HomeSlider::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image' => $imagePath,
            'link' => $request->link,
            'status' => $request->status ?? 1
        ]);

        return redirect()->route('admins.home-slider.index')
            ->with('success', 'Home slider created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $homeSlider = HomeSlider::findOrFail($id);
        return view('backend.admins.pages.home-slider-edit', compact('homeSlider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       
        $homeSlider = HomeSlider::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'required|url',
            'status' => 'boolean'
        ]);

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'link' => $request->link,
            'status' => $request->status ?? 1
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($homeSlider->image && File::exists(public_path($homeSlider->image))) {
                File::delete(public_path($homeSlider->image));
            }

            $uploadPath = public_path('uploads/home_slider');
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($uploadPath, $imageName);
            $data['image'] = 'uploads/home_slider/' . $imageName;
        }

        $homeSlider->update($data);

        return redirect()->route('admins.home-slider.index')
            ->with('success', 'Home slider updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $homeSlider = HomeSlider::findOrFail($id);

        // Delete image
        if ($homeSlider->image && File::exists(public_path($homeSlider->image))) {
            File::delete(public_path($homeSlider->image));
        }

        $homeSlider->delete();

        return redirect()->route('admins.home-slider.index')
            ->with('success', 'Home slider deleted successfully.');
    }

    /**
     * Update status via AJAX
     */
    public function updateStatus(Request $request, string $id)
    {
        $homeSlider = HomeSlider::findOrFail($id);
        $homeSlider->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.'
        ]);
    }
}
