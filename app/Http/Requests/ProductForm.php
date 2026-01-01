<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Webkul\Admin\Http\Requests\ProductForm as BaseProductForm;

class ProductForm extends BaseProductForm
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::error('ProductForm validation failed', [
            'product_id' => $this->id ?? 'unknown',
            'errors' => $validator->errors()->toArray(),
            'input_data' => $this->except(['_token', '_method']),
        ]);

        parent::failedValidation($validator);
    }
}

