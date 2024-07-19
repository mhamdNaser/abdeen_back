<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\LanguageResource;
use App\Models\Language;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $language = Language::all();

        return LanguageResource::collection($language);
    }

    public function active()
    {
        $language = Language::where("status", 1)->get();

        return LanguageResource::collection($language);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'direction' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:languages',
            'status' => 'required|boolean',
            'default' => 'required|boolean',
        ]);

        // حفظ بيانات اللغة في قاعدة البيانات
        $language = Language::create($validated);

        // إنشاء مجلد اللغة
        $this->createLanguageFiles($language->slug);

        return response()->json(['message' => 'Language created successfully'], 201);
    }

    public function addWordToAdminFile($slug,Request $request)
    {
        try {
            $key = $request->input('key');
            $translation = $request->input('value');

            // Path to admin.php file
            $adminFilePath = resource_path("lang/{$slug}/admin.php");

            // Check if directory exists, create if not
            if (!File::exists(dirname($adminFilePath))) {
                File::makeDirectory(dirname($adminFilePath), 0755, true);
            }

            // Initialize an empty array
            $adminData = [];

            // Check if admin.php file exists, load existing data if so
            if (File::exists($adminFilePath)) {
                $adminData = include $adminFilePath;

                // Check if $adminData is an array, if not initialize as empty array
                if (!is_array($adminData)) {
                    $adminData = [];
                }
            }

            // Add new key and translation to $adminData
            $adminData[$key] = $translation;

            // Convert $adminData to PHP code
            $phpCode = "<?php\n\nreturn " . var_export($adminData, true) . ";\n";

            // Write to admin.php file
            File::put($adminFilePath, $phpCode);

            return true; 
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {
            // Path to language files
            $languageDir = resource_path("lang/{$slug}");

            // Check if language directory exists
            if (!File::exists($languageDir)) {
                return response()->json(['message' => 'Language not found'], 404);
            }

            // Initialize an empty array to store combined data
            $combinedData = [];

            // Read admin.php content if exists
            $adminFilePath = "{$languageDir}/admin.php";
            if (File::exists($adminFilePath)) {
                $adminData = include $adminFilePath;
                if (is_array($adminData)) {
                    foreach ($adminData as $key => $value) {
                        $combinedData[] = ['key' => $key, 'value' => $value];
                    }
                }
            }

            // Return the combined data as JSON response
            return response()->json($combinedData, 200);
        } catch (\Exception $e) {
            // Return an error response if file reading fails
            return response()->json(['message' => 'Failed to retrieve language data', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Language $language)
    {
        //
    }

    public function changestatus($id)
    {
        $language = Language::findOrFail($id);

        // Toggle the status between 1 and 0
        $language->update([
            'status' => $language->status == 1 ? 0 : 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'admin status updated successfully.',
            "data" => $language
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the language by id
            $language = Language::findOrFail($id);

            // Delete the language from the database
            $language->delete();

            // Delete language files if they exist
            $languageDir = resource_path("lang/{$language->slug}");

            if (File::exists($languageDir)) {
                File::deleteDirectory($languageDir);
            }

            // Return a success response
            return response()->json(['message' => 'Language deleted successfully'], 200);
        } catch (\Exception $e) {
            // Return an error response if deletion fails
            return response()->json(['message' => 'Failed to delete language', 'error' => $e->getMessage()], 500);
        }

    }

    protected function createLanguageFiles($slug)
    {
        $languageDir = resource_path("lang/{$slug}");

        if (!File::exists($languageDir)) {
            File::makeDirectory($languageDir, 0755, true);
        }

        $adminContent = "<?php\n\nreturn [\n    // مصفوفة للإدارة\n];\n";

        File::put("{$languageDir}/admin.php", $adminContent);
    }
}
