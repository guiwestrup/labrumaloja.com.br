<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;
use Webkul\Product\Repositories\ProductAttributeValueRepository as BaseProductAttributeValueRepository;

class ProductAttributeValueRepository extends BaseProductAttributeValueRepository
{
    /**
     * Save attribute values
     *
     * @param  array  $data
     * @param  \Webkul\Product\Contracts\Product  $product
     * @param  mixed  $attributes
     * @return void
     */
    public function saveValues($data, $product, $attributes)
    {
        $attributeValuesToInsert = [];

        // Ensure attribute_values relationship is loaded
        if (! $product->relationLoaded('attribute_values')) {
            $product->load('attribute_values');
        }

        foreach ($attributes as $attribute) {
            if ($attribute->type === 'boolean') {
                $data[$attribute->code] = ! empty($data[$attribute->code]);
            }

            if (in_array($attribute->type, ['multiselect', 'checkbox'])) {
                $data[$attribute->code] = implode(',', $data[$attribute->code] ?? []);
            }

            // Allow empty strings for text/textarea fields, but skip if truly not set
            if (! array_key_exists($attribute->code, $data)) {
                continue;
            }

            if (
                $attribute->type === 'price'
                && empty($data[$attribute->code])
            ) {
                $data[$attribute->code] = null;
            }

            if (
                $attribute->type === 'date'
                && empty($data[$attribute->code])
            ) {
                $data[$attribute->code] = null;
            }

            if (in_array($attribute->type, ['image', 'file'])) {
                $data[$attribute->code] = gettype($data[$attribute->code]) === 'object'
                    ? request()->file($attribute->code)->store('product/'.$product->id)
                    : $data[$attribute->code];
            }

            $attributeValues = $product->attribute_values
                ->where('attribute_id', $attribute->id);

            // Get channel from data, request, or default
            if ($attribute->value_per_channel) {
                $channel = $data['channel'] 
                    ?? request()->get('channel') 
                    ?? request()->query('channel')
                    ?? core()->getRequestedChannelCode()
                    ?? core()->getDefaultChannelCode();
            } else {
                $channel = null;
            }

            // Get locale from data, request, or default - critical for translated attributes
            if ($attribute->value_per_locale) {
                $locale = $data['locale'] 
                    ?? request()->get('locale')
                    ?? request()->input('locale')
                    ?? request()->query('locale')
                    ?? core()->getRequestedLocaleCode('locale')
                    ?? core()->getRequestedLocaleCodeInRequestedChannel()
                    ?? app()->getLocale()
                    ?? core()->getDefaultLocaleCodeFromDefaultChannel();
            } else {
                $locale = null;
            }

            if ($attribute->value_per_channel) {
                if ($attribute->value_per_locale) {
                    $filteredAttributeValues = $attributeValues
                        ->where('channel', $channel)
                        ->where('locale', $locale);
                } else {
                    $filteredAttributeValues = $attributeValues
                        ->where('channel', $channel);
                }
            } else {
                if ($attribute->value_per_locale) {
                    $filteredAttributeValues = $attributeValues
                        ->where('locale', $locale);
                } else {
                    $filteredAttributeValues = $attributeValues;
                }
            }

            $attributeValue = $filteredAttributeValues->first();

            $uniqueId = implode('|', array_filter([
                $channel,
                $locale,
                $product->id,
                $attribute->id,
            ]));

            if (! $attributeValue) {
                $attributeValuesToInsert[] = array_merge($this->getAttributeTypeColumnValues($attribute, $data[$attribute->code]), [
                    'product_id'   => $product->id,
                    'attribute_id' => $attribute->id,
                    'channel'      => $channel,
                    'locale'       => $locale,
                    'unique_id'    => $uniqueId,
                ]);
            } else {
                $previousTextValue = $attributeValue->text_value;

                if (in_array($attribute->type, ['image', 'file'])) {
                    /**
                     * If $data[$attribute->code]['delete'] is not empty, that means someone selected the "delete" option.
                     */
                    if (! empty($data[$attribute->code]['delete'])) {
                        Storage::delete($previousTextValue);

                        $data[$attribute->code] = null;
                    }
                    /**
                     * If $data[$attribute->code] is not equal to the previous one, that means someone has
                     * updated the file or image. In that case, we will remove the previous file.
                     */
                    elseif (
                        ! empty($previousTextValue)
                        && $data[$attribute->code] != $previousTextValue
                    ) {
                        Storage::delete($previousTextValue);
                    }
                }

                $attributeValue = $this->update([
                    $attribute->column_name => $data[$attribute->code],
                    'unique_id'             => $uniqueId,
                ], $attributeValue->id);
            }
        }

        if (! empty($attributeValuesToInsert)) {
            $this->insert($attributeValuesToInsert);
        }
    }
}

