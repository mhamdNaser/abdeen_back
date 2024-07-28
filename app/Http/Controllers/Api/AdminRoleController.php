<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\roles_permissions\PermissionRequest;
use App\Http\Requests\Admin\roles_permissions\StoreRequest;
use App\Http\Resources\Admin\roles\PermissionResource;
use App\Http\Resources\Admin\roles\RoleResource;
use App\Http\Resources\Admin\roles\RolePermissionResource;
use App\Models\AdminRole;
use App\Models\Permission;
use App\Models\PermissionRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'roles_cache';

        $roles = Cache::remember($cacheKey, $cacheDuration, function () {
            return AdminRole::whereNot('id', 1)->get();
        });

        $adminCountInDB = AdminRole::whereNot('id', 1)->count();

        if ($roles->count() !== $adminCountInDB) {
            $roles = AdminRole::whereNot('id', 1)->get();
            Cache::put($cacheKey, $roles, $cacheDuration);
        }

        return RoleResource::collection($roles);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        // التحقق من البيانات
        $validatedData = $request->validated();

        // إنشاء الرول الجديدة
        $role = AdminRole::create([
            'name' => $validatedData['name'],
            'status' => 1,
        ]);

        // جلب جميع الأذونات
        $permissions = Permission::get();

        // تخزين كل إذن مرتبط بالرول الجديدة مع إعطائه حالة 0
        foreach ($permissions as $permission) {
            PermissionRole::create([
                'role_id' => $role->id,
                'permission_id' => $permission->id,
                'status' => 0,
            ]);
        }

        // مسح الكاش لأنه تم إضافة رول جديدة
        Cache::forget('roles_cache');

        return response()->json([
            'success' => true,
            'message' => 'Role stored successfully.',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $records = PermissionRole::where('role_id', $id )->with('permission')
            ->get();

        return response()->json([
            'success' => true,
            'mes' => "Role Permission",
            'data' => RolePermissionResource::collection($records),
        ]);
    }

    public function permission(PermissionRequest $request, $id)
    {
        $data = [];
        $update = false;

        if (!empty($request->permission)) {
            $json_decode = json_decode($request->permission[0]);

            foreach ($json_decode as $element) {
                $data[] = [
                    'id' => $element->id,
                    'value' => $element->value,
                ];
            }

            foreach ($data as $row) {
                $records = PermissionRole::where('role_id', $id)->where('id', $row['id'])->get();

                foreach ($records as $record) {
                    if ($row['value'] == true) {
                        $update = PermissionRole::where('id', $row['id'])->update([
                            'status' => 1,
                        ]);
                    } elseif ($row['value'] == false) {
                        $update = PermissionRole::where('id', $row['id'])->update([
                            'status' => 0,
                        ]);
                    }
                }
            }
        }

        if ($update) {
            $updatedRecords = PermissionRole::where('role_id', $id)
                ->whereIn('id', array_column($data, 'id'))
                ->get();

            return response()->json([
                'success' => true,
                'mes' => 'Saving Changes Successfully',
                'data' => PermissionResource::collection($updatedRecords),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'mes' => 'Error',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, $id)
    {
        $adminRole = AdminRole::findOrFail($id);

        $validatedData = $request->validated();

        $adminRole->update([
            'name' => $validatedData['name'],
            'status' => 1,
        ]);

        // Clear the cache since a role has been updated
        Cache::forget('roles_cache');

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.'
        ], 200);
    }

    public function softDeleteArray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        $idsToDelete = $validatedData['array'];

        DB::beginTransaction();

        // Fetch admins with IDs matching $idsToDelete
        $rolesToDelete = AdminRole::whereIn('id', $idsToDelete)->get();

        // Move admins to AdminArchive and delete from Admin
        foreach ($rolesToDelete as $role) {

            $role->delete();
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Roles deleted successfully.',
        ], 200);
    }

    public function changestatus($id)
    {
        $adminRole = AdminRole::findOrFail($id);

        // Toggle the status between 1 and 0
        $adminRole->update([
            'status' => $adminRole->status == 1 ? 0 : 1,
        ]);

        // Clear the cache since a role status has been updated
        Cache::forget('roles_cache');

        return response()->json([
            'success' => true,
            'message' => 'Role status updated successfully.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $adminRole = AdminRole::findOrFail($id);
        $adminRole->delete();

        // Clear the cache since a role has been deleted
        Cache::forget('roles_cache');

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.'
        ], 200);
    }
}
