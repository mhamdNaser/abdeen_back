<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\users\UserArchivesResource;
use App\Models\User;
use App\Models\UserArchives;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserArchivesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'usersArchive_cache';

        $adminsArchive = Cache::remember($cacheKey, $cacheDuration, function () {
            return UserArchives::get();
        });

        $adminCountInDB = UserArchives::count();

        if ($adminsArchive->count() !== $adminCountInDB) {
            $adminsArchive = UserArchives::get();
            Cache::put($cacheKey, $adminsArchive, $cacheDuration);
        }

        return UserArchivesResource::collection($adminsArchive);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $admin = UserArchives::findOrFail($id);
        DB::beginTransaction();

        try {
            $softdelete = User::create([
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

    public function updatearray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        $idsToDelete = $validatedData['array'];

        DB::beginTransaction();

        try {
            // Fetch admins with IDs matching $idsToDelete
            $adminsToTable = UserArchives::whereIn('id', $idsToDelete)->get();

            // Move admins to UserArchives and delete from Admin
            foreach ($adminsToTable as $admin) {
                User::create([
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $User = UserArchives::findOrFail($id);
        DB::beginTransaction();

        if ($User->image && file_exists(public_path($User->image))) {
            unlink(public_path($User->image));
        }

        $User->delete();

        Cache::forget("admins_cache");

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ], 200);
    }

    public function destroyarray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        $idsToDelete = $validatedData['array'];

        DB::beginTransaction();

        try {
            // Fetch admins with IDs matching $idsToDelete
            $UsersToTable = UserArchives::whereIn('id', $idsToDelete)->get();

            
            // Move admins to UserArchives and delete from Admin
            foreach ($UsersToTable as $User) {
                if ($User->image && file_exists(public_path($User->image))) {
                    unlink(public_path($User->image));
                }

                $User->delete();
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
}
