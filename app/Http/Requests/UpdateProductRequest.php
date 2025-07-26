<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'sku' => 'sometimes|string|max:255|unique:products,sku,'.$productId,
            'price' => 'sometimes|numeric|min:0|max:999999.99',
            'stock_quantity' => 'sometimes|integer|min:0',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => 'The product name must be a valid string.',
            'name.max' => 'The product name may not be greater than 255 characters.',
            'sku.string' => 'The SKU must be a valid string.',
            'sku.unique' => 'This SKU already exists.',
            'sku.max' => 'The SKU may not be greater than 255 characters.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be at least 0.',
            'price.max' => 'The price must not exceed 999999.99.',
            'stock_quantity.integer' => 'The stock quantity must be a whole number.',
            'stock_quantity.min' => 'The stock quantity must be at least 0.',
        ];
    }
}
