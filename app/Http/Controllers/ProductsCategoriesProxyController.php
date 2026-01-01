<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Marketing\Repositories\URLRewriteRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Shop\Http\Controllers\ProductsCategoriesProxyController as BaseProductsCategoriesProxyController;
use Webkul\Theme\Repositories\ThemeCustomizationRepository;

class ProductsCategoriesProxyController extends BaseProductsCategoriesProxyController
{
    /**
     * Show product or category view. If neither category nor product matches, abort with code 404.
     *
     * @return \Illuminate\View\View|\Exception
     */
    public function index(Request $request)
    {
        $slugOrURLKey = urldecode(trim($request->getPathInfo(), '/'));

        /**
         * Support url for chinese, japanese, arabic and english with numbers.
         */
        if (! preg_match('/^([\p{L}\p{N}\p{M}\x{0900}-\x{097F}\x{0590}-\x{05FF}\x{0600}-\x{06FF}\x{0400}-\x{04FF}_-]+\/?)+$/u', $slugOrURLKey)) {
            visitor()->visit();

            $customizations = $this->themeCustomizationRepository->orderBy('sort_order')->findWhere([
                'status'     => self::STATUS,
                'channel_id' => core()->getCurrentChannel()->id,
            ]);

            // Get categories tree for the view
            $categories = $this->categoryRepository->getVisibleCategoryTree(
                core()->getCurrentChannel()->root_category_id
            );

            return view('shop::home.index', compact('customizations', 'categories'));
        }

        // Call parent method for other cases
        return parent::index($request);
    }
}

