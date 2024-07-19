<?php

namespace App\Services;

use App\Models\ProductTags;

class ProductTagService
{
    /**
     * Save product tag in the database.
     *
     * @param array $data
     * @return ProductTags
     */
    public function saveProductTag(array $data): ProductTags
    {
        // Assuming $data is validated already using ProductTagRequest

        // Create a new ProductTags instance
        $productTag = new ProductTags();

        // Fill the instance with data from the request
        $productTag->fill($data);

        // Save the instance
        $productTag->save();

        return $productTag;
    }
}
