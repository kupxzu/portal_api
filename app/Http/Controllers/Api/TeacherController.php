<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::all();
        
        return response()->json([
            'success' => true,
            'data' => $teachers
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:teachers',
            'password' => 'required|string|min:8',
            'set_active' => 'sometimes|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher = Teacher::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'set_active' => $request->set_active ?? 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teacher created successfully',
            'data' => $teacher
        ], 201);
    }

    public function show($id)
    {
        $teacher = Teacher::find($id);
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $teacher
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::find($id);
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:teachers,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'set_active' => 'sometimes|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'email', 'set_active']);
        
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Teacher updated successfully',
            'data' => $teacher
        ], 200);
    }

    public function destroy($id)
    {
        $teacher = Teacher::find($id);
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully'
        ], 200);
    }

    public function updateStatus($id, Request $request)
    {
        $teacher = Teacher::find($id);
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'set_active' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher->update([
            'set_active' => $request->set_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teacher status updated successfully',
            'data' => $teacher
        ], 200);
    }
}