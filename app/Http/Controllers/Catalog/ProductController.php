<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Webkul\Admin\Http\Controllers\Catalog\ProductController as BaseProductController;
use Webkul\Admin\Http\Requests\ProductForm;

class ProductController extends BaseProductController
{
    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ProductForm $request, int $id)
    {
        try {
            Log::info('Product update started', [
                'product_id' => $id,
                'request_data_keys' => array_keys($request->all()),
                'locale' => $request->get('locale'),
                'channel' => $request->get('channel'),
            ]);

            Event::dispatch('catalog.product.update.before', $id);

            $product = $this->productRepository->update($request->all(), $id);

            Log::info('Product update completed', [
                'product_id' => $id,
                'product_sku' => $product->sku ?? 'N/A',
            ]);

            Event::dispatch('catalog.product.update.after', $product);

            session()->flash('success', trans('admin::app.catalog.products.update-success'));

            return redirect()->route('admin.catalog.products.index');
        } catch (\Exception $e) {
            Log::error('Product update failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token', '_method']),
            ]);

            session()->flash('error', 'Erro ao salvar produto: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }
}

