<?php

namespace App\Http\Requests;

use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InwardOutwardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'material_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    // Material must exist and not be soft-deleted
                    if (!Material::where('id', $value)->whereNull('deleted_at')->exists()) {
                        $fail('The selected material does not exist or has been deleted.');
                    }
                },
            ],
            'date' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
            'quantity' => [
                'required',
                'numeric',
                'not_in:0',                        // Quantity cannot be zero
                'regex:/^\-?\d+(\.\d{1,2})?$/',   // Max 2 decimal places, can be negative
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'  => 'Please select a material category.',
            'category_id.integer'   => 'Category selection is invalid.',
            'category_id.exists'    => 'The selected category does not exist.',
            'material_id.required'  => 'Please select a material.',
            'material_id.integer'   => 'Material selection is invalid.',
            'date.required'         => 'Date is required.',
            'date.date'             => 'Date must be a valid date.',
            'date.date_format'      => 'Date must be in the format YYYY-MM-DD.',
            'quantity.required'     => 'Quantity is required.',
            'quantity.numeric'      => 'Quantity must be a number.',
            'quantity.not_in'       => 'Quantity cannot be zero.',
            'quantity.regex'        => 'Quantity must have at most 2 decimal places and can be negative.',
        ];
    }

    // Return JSON error response on validation failure
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'msg'           => implode(' ', $validator->errors()->all()),
            'msgtype'       => 'val_error',
            'msgsuc'        => 'bg-danger',
            'msgfail'       => 'bg-danger',
            'msgtype_title' => 'Validation Error',
            'TargetURL'     => null,
        ], 200));
    }
}
