<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PageContentResource extends JsonResource
{
    public static $wrap = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // Get the file contents from the public directory based on the filename
        $filePath = public_path('upload/Pages/' . $this->title . '.php');

        if (file_exists($filePath)) {
            $fileContents = file_get_contents($filePath);

            return [
                'id' => $this->id,
                'title' => $this->title,
                'content' => $fileContents,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            ];
        } else {
            // Handle the case when the file does not exist
            return [
                'id' => $this->id,
                'content' => null, // or handle it in a way that fits your application logic
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            ];
        }
    }
}