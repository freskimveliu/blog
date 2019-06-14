<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class APIRequest extends FormRequest
{
    protected function unAuthorizationMessage() {
        return "Unauthorized permission";
    }

    protected function unAuthorizedStatus() {
        return 401;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        $message = implode("\n", $errors);

        throw new ValidationException($validator,
            response()->json([
                'errors'    => $errors,
                'message'   => $message,
                'success'   => false], 400));
    }

    protected function failedAuthorization()
    {
        throw new ValidationException(null,
            response()->json([
                'errors'    => [],
                'message'   => $this->unAuthorizationMessage(),
                'success'   => false], $this->unAuthorizedStatus()));
    }
}
