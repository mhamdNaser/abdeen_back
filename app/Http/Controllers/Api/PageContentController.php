<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use App\Http\Resources\Admin\PageContentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PageContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PageContentResource::collection(PageContent::query()
        ->orderBy('id', 'desc')
        ->paginate(10));
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
    
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Begin the database transaction
        DB::beginTransaction();
        
        $fileName = $validatedData['title'];
        file_put_contents(public_path('upload/Pages/' . $fileName . '.php'), $validatedData['content']);
        
        // Save the filename in the database along with other data
        $PageContent = PageContent::create([
            'title' => $validatedData['title'],
            'name' => $fileName,
        ]);

        // Commit the transaction if everything is successful
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'PageContent stored successfully',
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $PageContent = PageContent::findOrFail($id);
        return new PageContentResource($PageContent);
    }
    
    public function showByTitle(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
        ]);
    
        $title = $validatedData['title']; // Extract title from validated data
        $PageContent = PageContent::where('title', $title)->get();
        return new PageContentResource($PageContent->first());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PageContent $pageContent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request )
    {
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Find the PageContent record by title
        $pageContent = PageContent::where('title', $request->title)->firstOrFail();

        // Perform your modifications to the content
        $modifiedContent = $request->content;

        // Save the modified content to the same file, overwriting the existing content
        file_put_contents(public_path('upload/Pages/' . $pageContent->title . '.php'), $modifiedContent);

        // Update other data in the database
        $pageContent->update([
            'title' => $request->title,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Page content updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Begin the database transaction
        DB::beginTransaction();
        $PageContent = PageContent::findOrFail($id);

        // Delete the associated file
        $filePath = public_path('upload/Pages/' . $PageContent->title . '.php');
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the material record from the database
        $PageContent->delete();

        // Commit the transaction if everything is successful
        DB::commit();

        return response()->json([
            'success' => true,
            'mes' => 'Delete PageContent Successfully',
        ]);
    }
}
