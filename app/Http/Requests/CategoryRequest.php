<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules       = [];
        $actionMethod = $this->route()->getActionMethod();

        if ($actionMethod === 'category_save') {
            $rules = [
                'category_id' => [
                    'nullable',
                    'integer',
                    function ($attribute, $value, $fail) {
                        // When updating, the category must exist
                        if ($this->filled('category_id')) {
                            if (!Category::where('id', $value)->exists()) {
                                $fail('The selected category does not exist.');
                            }
                        }
                    },
                ],
                'name' => [
                    'required',
                    'string',
                    'max:100',
                    function ($attribute, $value, $fail) {
                        // Name must be unique, excluding current record on update
                        $query = Category::where('name', $value);
                        if ($this->filled('category_id')) {
                            $query->where('id', '!=', $this->category_id);
                        }
                        if ($query->exists()) {
                            $fail('This category name already exists.');
                        }
                    },
                ],
            ];
        }

        if (in_array($actionMethod, ['category_edit', 'category_delete'])) {
            $rules = [
                'category_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) {
                        if (!Category::where('id', $value)->exists()) {
                            $fail('The selected category does not exist.');
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
            'category_id.required' => 'Category ID is required.',
            'category_id.integer'  => 'Category ID must be an integer.',
            'name.required'        => 'Category name is required.',
            'name.string'          => 'Category name must be a string.',
            'name.max'             => 'Category name must not exceed 100 characters.',
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
