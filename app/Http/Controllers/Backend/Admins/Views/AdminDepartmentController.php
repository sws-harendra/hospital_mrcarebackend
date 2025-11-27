<?php

namespace App\Http\Controllers\Backend\Admins\Views;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminDepartmentController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::latest()->get();
        return view('backend.admins.pages.department', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admins.pages.department-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:department,name',
            'description' => 'required|string|max:500',
            'status' => 'boolean'
        ]);

        Department::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? 1
        ]);

        return redirect()->route('admins.department.index')
            ->with('success', 'Department created successfully.');
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
        $department = Department::findOrFail($id);
        return view('backend.admins.pages.department-edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:department,name,' . $id,
            'description' => 'required|string|max:500',
            'status' => 'boolean'
        ]);

        $department->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? 1
        ]);

        return redirect()->route('admins.department.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()->route('admins.department.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Update status via AJAX
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            $department = Department::findOrFail($id);
            $status = $request->status == '1' || $request->status === 1 || $request->status === true;
            
            $department->update(['status' => $status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}
