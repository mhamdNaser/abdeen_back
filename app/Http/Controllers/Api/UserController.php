<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\users\StoreRequest;
use App\Http\Requests\Admin\users\UpdateRequest;
use App\Http\Requests\Admin\users\UserRequest;
use App\Http\Resources\Admin\users\UsersResource;
use App\Models\Image;
use App\Models\User;
use App\Models\UserArchives;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'Users_cache';

        $Users = Cache::remember($cacheKey, $cacheDuration, function () {
            return User::get();
        });

        $UserCountInDB = User::count();

        if ($Users->count() !== $UserCountInDB) {
            $Users = User::get();
            Cache::put($cacheKey, $Users, $cacheDuration);
        }

        return UsersResource::collection($Users);
    }


    public function login(UserRequest $request)
    {

        try {
            $User = User::where('email', $request->email)->first();

            if (!$User || !Hash::check($request->password, $User->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ]);
            }

            $token = $User->createToken('User Token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Successfully',
                'token' => $token,
                'User' => new UsersResource($User),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function changestatus($id)
    {
        $User = User::findOrFail($id);

        // Toggle the status between 1 and 0
        $User->update([
            'status' => $User->status == 1 ? 0 : 1,
        ]);

        // Clear the cache since a role status has been updated
        Cache::forget('Users_cache');

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully.',
            "data" => $User
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'admin' => new UsersResource($user),
        ], 200);
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

        // Create new User
        $User = User::create([
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
            'status' => 1,
        ]);


        if ($validated['image']) {
            $imageName = $validated['username'] . uniqid()  . '.' . $validated['image']->getClientOriginalExtension();

            // Specify the destination directory within the public disk
            $destinationPath = public_path('upload/images/users/');

            // Move the uploaded file to the destination directory
            $validated['image']->move($destinationPath, $imageName);

            // Construct the image path
            $imagePath = 'upload/images/users/' . $imageName;

            // Create image record
            $image = new Image();
            $image->path = $imagePath; // Store the image path
            $image->name = $imageName; // Store the image path
            $User->images()->save($image);

            $User->update([
                'image' => $imagePath
            ]);
        }

        // Commit the transaction
        DB::commit();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
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
            $UsersToDelete = User::whereIn('id', $idsToDelete)->get();

            foreach ($UsersToDelete as $User) {
                UserArchives::create([
                    'username' => $User->username,
                    'email' => $User->email,
                    'phone' => $User->phone,
                    'password' => $User->password,
                    'first_name' => $User->first_name,
                    'medium_name' => $User->medium_name,
                    'last_name' => $User->last_name,
                    'country_id' => $User->country_id,
                    'state_id' => $User->state_id,
                    'city_id' => $User->city_id,
                    'image' => null,
                ]);

                $images = $User->images;

                foreach ($images as $image) {
                    $imagePath = public_path($image->path);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $image->delete();
                }

                $User->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Users soft deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete Users.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $validated = $request->validated();

        // Begin a database transaction
        DB::beginTransaction();

        // Find the User by ID
        $User = User::findOrFail($id);

        // Prepare data for update
        $updateData = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        // Update name fields if 'name' is present
        if ($validated['name']) {
            $nameParts = explode(' ', $validated['name'], 3);
            $updateData['first_name'] = $nameParts[0];
            $updateData['medium_name'] = isset($nameParts[1]) ? $nameParts[1] : null;
            $updateData['last_name'] = isset($nameParts[2]) ? $nameParts[2] : null;
        }

        // Handle image upload if provided
        if ($validated['image']) {
            $imageName = $validated['username'] . uniqid() . '.' . $validated['image']->getClientOriginalExtension();

            // Specify the destination directory within the public disk
            $destinationPath = public_path('upload/images/users/');

            // Move the uploaded file to the destination directory
            $validated['image']->move($destinationPath, $imageName);

            // Construct the image path
            $imagePath = 'upload/images/users/' . $imageName;

            // Check if user already has an image
            $existingImage = $User->images()->first();
            if ($existingImage) {
                // Update existing image record
                $existingImage->name = $imageName;
                $existingImage->path = $imagePath;
                $existingImage->save();
            } else {
                // Create a new image record
                $image = new Image();
                $image->name = $imageName;
                $image->path = $imagePath;
                $User->images()->save($image);
            }

            // Update user's image path
            $updateData['image'] = $imagePath;
        }

        // Update User with the prepared data
        $User->update($updateData);

        Cache::forget("Users_cache");

        // Commit the transaction
        DB::commit();

        // Return a success response with the updated User data
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'User' => $User, // Optionally return the updated User data
        ], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $User = User::findOrFail($id);
        DB::beginTransaction();

        try {
            $softdelete = UserArchives::create([
                'username' => $User->username,
                'email' => $User->email,
                'phone' => $User->phone,
                'password' => $User->password,
                'first_name' => $User->first_name,
                'medium_name' => $User->medium_name,
                'last_name' => $User->last_name,
                'country_id' => $User->country_id,
                'state_id' => $User->state_id,
                'city_id' => $User->city_id,
                'image' => null,
            ]);

            // Get all images of the user
            $images = $User->images;

            // Delete image records from database
            foreach ($images as $image) {
                // Delete image file from the server
                $imagePath = public_path($image->path);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                // Delete the image record from the database
                $image->delete();
            }

            // Delete the user record
            $User->delete();

            Cache::forget("Users_cache");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User soft deleted successfully.'
            ], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'User soft delete failed. ' . $e->getMessage()
            ], 500);
        }
    }


}
