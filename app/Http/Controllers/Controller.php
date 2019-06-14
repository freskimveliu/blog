<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $per_page;
    public $user_language;

    public function __construct()
    {
        $this->setupPerPage();
        $this->setupUserLanguage();
    }

    protected function setupPerPage() {

        $per_page = request('per_page', 12);

        $this->per_page = (int)$per_page;
    }

    protected function setupUserLanguage() {
        $defaultLocale = config('app.locale');

        $userRequestLocale = request()->header('Accept-Language', $defaultLocale);

        $this->user_language = $userRequestLocale;
    }

    public function respondWithSuccess($data = [], $message = null, $status_code = 200)
    {
        $response = [
            "message" => $message ?? "Success",
            "status_code" => $status_code,
        ];


        if ($data instanceof LengthAwarePaginator) {
            $response["data"] = $data->items();
            $response["pagination"] = $this->paginator($data);
        } else {
            $response["data"] = $data;
        }


        return response()->json($response, $status_code);
    }

    public function respondWithError($errors = [], $message = null, $status_code = 400){
        return response()->json(['errors' => $errors, 'message' => $message ?? "An error occurred", 'success' => false], $status_code);
    }

    public function respondInvalidInputs($errors = [], $message = null, $status_code = 400)
    {

        $message = implode("\n", $errors);

        return response()->json(['errors' => $errors, 'message' => $message ?? "Invalid inputs", 'success' => false], $status_code);
    }


    public function hasPagination($data)
    {
        if (method_exists($data, 'total')) {
            return ["total" => $data->total(), "current_page" => $data->currentPage(), "item_per_page" => $data->perPage(), "last_page" => $data->lastPage()];
        }

        return false;
    }

    public function pagination($results)
    {
        if (empty($results)) {
            return [
                "data" => [],
                "pagination" => []
            ];
        }
        return [
            "data" => $results->items(),
            "pagination" => self::pagination_data($results)
        ];
    }

    public function pagination_data($data)
    {
        if (method_exists($data, 'total')) {
            return ["total" => $data->total(), "current_page" => $data->currentPage(), "item_per_page" => $data->perPage(), "last_page" => $data->lastPage()];
        }

        return false;
    }


    public function validateRequest($request, $rules) {
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $validation->errors()->all();
        }

        return null;
    }

    public function paginator(LengthAwarePaginator $paginator) {
        return [
            "total"         => $paginator->total(),
            "current_page"  => $paginator->currentPage(),
            "item_per_page" => $paginator->perPage(),
            "last_page"     => $paginator->lastPage()
        ];
    }
}
