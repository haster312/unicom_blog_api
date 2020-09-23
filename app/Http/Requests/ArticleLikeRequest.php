<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\CustomResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;


class ArticleLikeRequest extends FormRequest
{
    protected $type;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        error($validator->errors()->toArray());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'article_id' => 'required',
            'user_id' => 'required'
        ];
    }
}
