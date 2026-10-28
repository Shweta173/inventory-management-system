<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules        = [];
        $actionMethod = $this->route()->getActionMethod();

        if ($actionMethod === 'material_save') {
            $rules = [
                'material_id' => [
                    'nullable',
                    'integer',
                    function ($attribute, $value, $fail) {
                        // When updating, the material must exist
                        if ($this->filled('material_id')) {
                            if (!Material::where('id', $value)->exists()) {
                                $fail('The selected material does not exist.');
                            }
                        }
                    },
                ],
                'category_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) {
                        // Category must exist
                        if (!Category::where('id', $value)->exists()) {
                            $fail('The selected category does not exist.');
                        }
                    },
                ],
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-Z0-9\s\-_]+$/', // Alphanumeric only
                    function ($attribute, $value, $fail) {
                        // Material name must be unique per category
                        $query = Material::where('category_id', $this->category_id)
                                         ->where('name', $value);
                        if ($this->filled('material_id')) {
                            $query->where('id', '!=', $this->material_id);
                        }
                        if ($query->exists()) {
                            $fail('A material with this name already exists in the selected category.');
                        }
                    },
                ],
                'opening_balance' => [
                    'required',
                    'numeric',
                    'regex:/^\-?\d+(\.\d{1,2})?$/', // Max 2 decimal places
                ],
            ];
        }

        if (in_array($actionMethod, ['material_edit', 'material_delete'])) {
            $rules = [
                'material_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) {
                        if (!Material::where('id', $value)->exists()) {
                            $fail('The selected material does not exist.');
                        }
                    },
                ],
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'material_id.required'        => 'Material ID is required.',
            'material_id.integer'         => 'Material ID must be an integer.',
            'category_id.required'        => 'Please select a category.',
            'category_id.integer'         => 'Category selection is invalid.',
            'name.required'               => 'Material name is required.',
            'name.string'                 => 'Material name must be a string.',
            'name.max'                    => 'Material name must not exceed 255 characters.',
            'name.regex'                  => 'Material name must be alphanumeric (letters, numbers, spaces, hyphens, underscores).',
            'opening_balance.required'    => 'Opening balance is required.',
            'opening_balance.numeric'     => 'Opening balance must be a number.',
            'opening_balance.regex'       => 'Opening balance must have at most 2 decimal places.',
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
