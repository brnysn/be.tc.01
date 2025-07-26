<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
        return [
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateStockAvailability($validator);
        });
    }

    /**
     * Validate that all products have sufficient stock.
     */
    protected function validateStockAvailability(Validator $validator): void
    {
        $items = $this->input('items', []);

        foreach ($items as $index => $item) {
            if (! isset($item['product_id']) || ! isset($item['quantity'])) {
                continue; // Skip if basic validation failed
            }

            $product = Product::find($item['product_id']);

            if ($product && ! $product->isInStock($item['quantity'])) {
                $validator->errors()->add(
                    "items.{$index}.quantity",
                    "The requested quantity ({$item['quantity']}) exceeds available stock ({$product->stock_quantity}) for product: {$product->name}."
                );
            }
        }
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'The items are required.',
            'items.array' => 'The items must be an array.',
            'items.*.product_id.required' => 'The product ID is required.',
            'items.*.product_id.exists' => 'The product ID does not exist.',
            'items.*.quantity.required' => 'The quantity is required.',
            'items.*.quantity.integer' => 'The quantity must be a whole number.',
            'items.*.quantity.min' => 'The quantity must be at least 1.',
        ];
    }
}
