<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\admins\AdminArchivesResource;
use App\Models\AdminArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Admin\admins\AdminResource;
use App\Models\Admin;

class AdminArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'adminsArchive_cache';

        $adminsArchive = Cache::remember($cacheKey, $cacheDuration, function () {
            return AdminArchive::with('role')->get();
        });

        $adminCountInDB = AdminArchive::count();

        if ($adminsArchive->count() !== $adminCountInDB) {
            $adminsArchive = AdminArchive::with('role')->get();
            Cache::put($cacheKey, $adminsArchive, $cacheDuration);
        }

        return AdminArchivesResource::collection($adminsArchive);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $admin = AdminArchive::findOrFail($id);
        DB::beginTransaction();

        try {
            $softdelete = Admin::create([
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
                'message' => 'Admin Recovery successfully.'
            ], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Admin Recovery failed' . $e->getMessage()
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
            $adminsToTable = AdminArchive::whereIn('id', $idsToDelete)->get();

            // Move admins to AdminArchive and delete from Admin
            foreach ($adminsToTable as $admin) {
                Admin::create([
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
        $admin = AdminArchive::findOrFail($id);
        DB::beginTransaction();

        $admin->delete();

        Cache::forget("admins_cache");

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Admin soft deleted successfully.'
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
            $adminsToTable = AdminArchive::whereIn('id', $idsToDelete)->get();

            // Move admins to AdminArchive and delete from Admin
            foreach ($adminsToTable as $admin) {

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
}
