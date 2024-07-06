<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\admins\AdminRequest;
use App\Http\Requests\Admin\admins\StoreRequest;
use App\Http\Requests\Admin\admins\UpdateRequest;
use App\Http\Resources\Admin\admins\AdminResource;
use App\Http\Resources\Admin\admins\LoginResource;
use App\Models\Admin;
use App\Models\AdminArchive;
use App\Models\Image;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'admins_cache';

        $admins = Cache::remember($cacheKey, $cacheDuration, function () {
            return Admin::whereNot('role_id', 1)
            ->orWhereNull('role_id')
            ->with('role')
                ->get();
        });

        $adminCountInDB = Admin::whereNot('role_id', 1)
        ->orWhereNull('role_id')
        ->count();

        if ($admins->count() !== $adminCountInDB) {
            $admins = Admin::whereNot('role_id', 1)
            ->orWhereNull('role_id')
            ->with('role')
                ->get();
            Cache::put($cacheKey, $admins, $cacheDuration);
        }

        return AdminResource::collection($admins);
    }


    public function login(AdminRequest $request)
    {

        try {
            $admin = Admin::where('email', $request->email)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ]);
            }

            $token = $admin->createToken('Admin Token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Successfully',
                'token' => $token,
                'admin' => new LoginResource($admin),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function refreshToken(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token is required'], 400);
        }

        $user = Admin::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Sanctum's built-in refresh token mechanism
        $newToken = $user->createToken('Admin Token')->plainTextToken;

        return response()->json(['token' => $newToken]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        // Validate and get validated data
        $validated = $request->validated();

        // Begin database transaction
        DB::beginTransaction();

        // Create new admin
        $admin = Admin::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'first_name' => $validated['first_name'],
            'medium_name' => $validated['medium_name'],
            'last_name' => $validated['last_name'],
            'country_id' => $validated['country_id'],
            'state_id' => $validated['state_id'],
            'city_id' => $validated['city_id'],
            'role_id' => $validated['role_id'],
            'status' => 1,
        ]);


        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));

            // Create image record
            $image = new Image();
            $image->name = $imagePath; // Store the image path
            $admin->images()->save($image);

            $admin->update([
                'image' => $imagePath
            ]);
        }

        // Commit the transaction
        DB::commit();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully',
        ], 201);
    }


    public function softDeleteArray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        $idsToDelete = $validatedData['array'];

        DB::beginTransaction();

        try {
            // Fetch admins with IDs matching $idsToDelete
            $adminsToDelete = Admin::whereIn('id', $idsToDelete)->get();

            // Move admins to AdminArchive and delete from Admin
            foreach ($adminsToDelete as $admin) {
                AdminArchive::create([
                    'username' => $admin->username,
                    'email' => $admin->email,
                    'phone' => $admin->phone,
                    'password' => $admin->password,
                    'first_name' => $admin->first_name,
                    'medium_name' => $admin->medium_name,
                    'last_name' => $admin->last_name,
                    'country_id' => $admin->country_id,
                    'state_id' => $admin->state_id,
                    'city_id' => $admin->city_id,
                    'image' => $admin->image,
                    'role_id' => $admin->role_id,
                    'status' => 0,
                ]);

                $admin->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admins soft deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete admins.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function changestatus($id)
    {
        $admin = Admin::findOrFail($id);

        // Toggle the status between 1 and 0
        $admin->update([
            'status' => $admin->status == 1 ? 0 : 1,
        ]);

        // Clear the cache since a role status has been updated
        Cache::forget('admins_cache');

        return response()->json([
            'success' => true,
            'message' => 'admin status updated successfully.',
            "data"=> $admin
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $validated = $request->validated();

        // Begin a database transaction
        DB::beginTransaction();

        // Find the admin by ID
        $admin = Admin::findOrFail($id);

        // Prepare data for update
        $updateData = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role_id' => $validated['role_id'],
        ];

        // Update name fields if 'name' is present
        if ($validated['name']) {
            $nameParts = explode(' ', $validated['name'], 3);
            $updateData['first_name'] = $nameParts[0];
            $updateData['medium_name'] = isset($nameParts[1]) ? $nameParts[1] : null;
            $updateData['last_name'] = isset($nameParts[2]) ? $nameParts[2] : null;
        }

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
            $updateData['image'] = $imagePath;
        }

        // Update admin with the prepared data
        $admin->update($updateData);

        Cache::forget("admins_cache");

        // Commit the transaction
        DB::commit();

        // Return a success response with the updated admin data
        return response()->json([
            'success' => true,
            'message' => 'Admin updated successfully.',
            'admin' => $admin, // Optionally return the updated admin data
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        DB::beginTransaction();

        try {
            $softdelete = AdminArchive::create([
                'username' => $admin->username,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'password' => $admin->password,
                'first_name' => $admin->first_name,
                'medium_name' => $admin->medium_name,
                'last_name' => $admin->last_name,
                'country_id' => $admin->country_id,
                'state_id' => $admin->state_id,
                'city_id' => $admin->city_id,
                'image' => $admin->image,
                'role_id' => $admin->role_id,
                'status' => 0,
            ]);

            $admin->delete();

            Cache::forget("admins_cache");
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin soft deleted successfully.'
            ], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Admin soft delete failed' . $e->getMessage()
            ], 500);
        }
    }


    private function uploadImage($imageFile)
    {
        // Generate unique image name
        $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();

        // Specify the destination directory within the public disk
        $destinationPath = public_path('upload/images/admin/');

        // Move the uploaded file to the destination directory
        $imageFile->move($destinationPath, $imageName);

        // Return the image path
        return 'upload/images/admin/' . $imageName;
    }
}
