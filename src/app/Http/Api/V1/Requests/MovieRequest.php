<?php

namespace le_54ba\MovieSeeder\App\Http\Api\V1\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Waavi\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Validation\ValidationException;

class MovieRequest extends FormRequest
{

    use SanitizesInput;

    protected $queryParametersToValidate = ['category_id' => 'category_id'];

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
            'category_id' => 'nullable|integer',
            'popular|desc' =>'nullable|regex:/^$/',
            'popular|asc' =>'nullable|regex:/^$/',
            'rated|desc'=>'nullable|regex:/^$/',
            'rated|asc'=>'nullable|regex:/^$/',
            'title'=>'nullable|string',
            'overview'=>'nullable|string',
            'adult'=>'nullable|integer',
            'original_language'=>'nullable|string',
            'video'=>'nullable|integer',
            'release_date'=>'nullable|date|date_format:Y-m-d' ,
            // 'release_date' => 'nullable|integer|min:1900|max:'.date("Y")

        ];
    }

     /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

     public function filters()
    {
        return [
            'category_id' => 'trim|escape',
            'title' => 'trim|escape',
            'original_language' => 'trim|escape',

        ];
    }

    public function all($keys = null)
    {
        $data = parent::all();

        foreach ($this->queryParametersToValidate as $validationDataKey => $queryParameter) {
            $data[$validationDataKey] = $this->query($queryParameter);
        }

        return $data;
    }
}
