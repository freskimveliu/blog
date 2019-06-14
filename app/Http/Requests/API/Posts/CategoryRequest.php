<?php

namespace App\Http\Requests\API\Posts;

use App\Http\Requests\API\APIRequest;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:3',
        ];
    }
}
