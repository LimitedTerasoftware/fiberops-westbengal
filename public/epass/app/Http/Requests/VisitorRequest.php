<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisitorRequest extends FormRequest
{

    private $visitor_id;
    public  function __construct($id =null)
    {
        parent::__construct();
        $this->visitor_id = $id ? $id : 0;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->visitor) {
            $email    = ['nullable', 'email', 'string'];
            $phone    = ['required', 'string'];

        } elseif($this->visitor_id){
            $email    = ['nullable', 'email', 'string'];
            $phone    = ['required', 'string'];
        } else {
            $email    = ['nullable', 'email', 'string' ];
            $phone    = ['required', 'string' ];
        }
        return [
            'first_name'                => 'required|string|max:100',
            'last_name'                 => 'required|string|max:100',
            'email'                     => $email,
            'phone'                     => $phone,
            'employee_id'               => 'required|numeric',
            'gender'                    => 'required|numeric',
            'used_content'              => 'required',
            'language'                  => 'required',
            'date_of_birth'             => 'required',
            'company_name'              => 'nullable|max:100',
            'national_identification_no'=> 'nullable|max:100',
            'purpose'                   => 'nullable|max:191',
            'address'                   => 'nullable|max:191',
            'image'                     => 'nullable|image|mimes:jpeg,png,jpg|max:5098',
        ];
    }
}
