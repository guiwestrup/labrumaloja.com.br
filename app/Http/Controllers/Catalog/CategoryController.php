<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Admin\Http\Controllers\Catalog\CategoryController as BaseCategoryController;
use Webkul\Admin\Http\Resources\CategoryTreeResource;

class CategoryController extends BaseCategoryController
{
    /**
     * Get all categories in tree format.
     */
    public function tree(): JsonResource
    {
        // Get channel with fallback
        $channel = core()->getRequestedChannel() 
            ?? core()->getCurrentChannel() 
            ?? core()->getDefaultChannel();
        
        // Get root category ID with fallback
        $rootCategoryId = $channel?->root_category_id;
        
        // If no root category, get all visible categories
        if (empty($rootCategoryId)) {
            $categories = $this->categoryRepository->getVisibleCategoryTree();
        } else {
            $categories = $this->categoryRepository->getVisibleCategoryTree($rootCategoryId);
        }

        return CategoryTreeResource::collection($categories);
    }
}

