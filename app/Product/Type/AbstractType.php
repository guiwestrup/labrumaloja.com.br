<?php

namespace App\Product\Type;

use Webkul\Product\Type\AbstractType as BaseAbstractType;

abstract class AbstractType extends BaseAbstractType
{
    /**
     * Update product.
     *
     * @param  array  $data
     * @param  int  $id
     * @param  array  $attributes
     * @return \Webkul\Product\Contracts\Product
     */
    public function update(array $data, $id, $attributes = [])
    {
        \Log::info('AbstractType::update called', [
            'product_id' => $id,
            'data_keys' => array_keys($data),
            'attributes' => $attributes,
        ]);

        $product = $this->productRepository->find($id);

        if (! $product) {
            \Log::error('Product not found', ['id' => $id]);
            throw new \Exception("Product with ID {$id} not found");
        }

        $product->update($data);

        \Log::info('Product base updated', ['product_id' => $id]);

        /**
         * Ensure locale and channel are in $data array for translated attributes
         * This is critical for attributes with value_per_locale or value_per_channel
         */
        if (! isset($data['locale'])) {
            $data['locale'] = request()->get('locale') 
                ?? request()->input('locale')
                ?? request()->query('locale')
                ?? core()->getRequestedLocaleCode('locale')
                ?? core()->getRequestedLocaleCodeInRequestedChannel()
                ?? app()->getLocale()
                ?? core()->getDefaultLocaleCodeFromDefaultChannel();
        }

        if (! isset($data['channel'])) {
            $data['channel'] = request()->get('channel')
                ?? request()->query('channel')
                ?? core()->getRequestedChannelCode()
                ?? core()->getDefaultChannelCode();
        }

        /**
         * If attributes are provided then only save the provided attributes and return.
         */
        if (! empty($attributes)) {
            $attributes = $this->attributeRepository->findWhereIn('code', $attributes);

            $this->attributeValueRepository->saveValues($data, $product, $attributes);

            return $product;
        }

        // Ensure attribute_family relationship is loaded
        if (! $product->relationLoaded('attribute_family')) {
            $product->load('attribute_family');
        }

        // Ensure we get the collection of attributes, not a query builder
        $customAttributes = $product->attribute_family->custom_attributes;
        if ($customAttributes instanceof \Illuminate\Database\Eloquent\Builder) {
            $customAttributes = $customAttributes->get();
        } elseif (! $customAttributes instanceof \Illuminate\Support\Collection) {
            $customAttributes = $product->attribute_family->custom_attributes()->get();
        }

        \Log::info('About to save attribute values', [
            'product_id' => $product->id,
            'attributes_count' => $customAttributes->count(),
            'locale' => $data['locale'] ?? 'not set',
            'channel' => $data['channel'] ?? 'not set',
        ]);

        $this->attributeValueRepository->saveValues($data, $product, $customAttributes);

        \Log::info('Attribute values saved', ['product_id' => $product->id]);

        if (empty($data['channels'])) {
            $data['channels'][] = core()->getDefaultChannel()->id;
        }

        $product->channels()->sync($data['channels']);

        if (! isset($data['categories'])) {
            $data['categories'] = [];
        }

        $product->categories()->sync($data['categories']);

        $product->up_sells()->sync($data['up_sells'] ?? []);

        $product->cross_sells()->sync($data['cross_sells'] ?? []);

        $product->related_products()->sync($data['related_products'] ?? []);

        $this->productInventoryRepository->saveInventories($data, $product);

        $this->productImageRepository->upload($data, $product, 'images');

        $this->productVideoRepository->upload($data, $product, 'videos');

        $this->productCustomerGroupPriceRepository->saveCustomerGroupPrices($data, $product);

        return $product;
    }
}

