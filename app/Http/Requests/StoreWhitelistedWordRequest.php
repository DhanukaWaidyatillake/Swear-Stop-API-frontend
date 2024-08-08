<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWhitelistedWordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !is_null(auth()->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'word' => [
                'required',
                'string',
                Rule::unique('whitelisted_words')->where(fn(Builder $query) => $query->where('user_id', auth()->user()->id))
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'word.required' => 'Please specify a word',
            'word.string' => 'Word cannot be empty',
            'word.unique' => 'This word is already in Whitelist'
        ];
    }
}
