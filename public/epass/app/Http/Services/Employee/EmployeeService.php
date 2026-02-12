<?php

namespace App\Http\Services\Employee;

use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;


class EmployeeService
{
    /**
     * @param Request $request
     * @param int $limit
     * @return mixed
     */
    public function all()
    {
        return Employee::orderBy('id', 'desc')->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Employee::findorFail($id);
    }

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    public function findWhere($column, $value)
    {
        $result = Employee::where($column, $value)->get();

        return $result;
    }

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    public function findWhereFirst($column, $value)
    {
        $result = Employee::where($column, $value)->first();

        return $result;
    }

    /**
     * @param int $perPage
     * @return mixed
     */
    public function paginate($perPage = 100)
    {
        return Employee::paginate($perPage);
    }

    /**
     * @param EmployeeRequest $request
     * @return mixed
     */
    public function make(EmployeeRequest $request)
    {
        $input['first_name'] = $request->input('first_name');
        $input['last_name']  = $request->input('last_name');
        $input['username']   = $this->username($request->input('email'));
        $input['email']      = $request->input('email');
        $input['phone']      = $request->input('phone');
        $input['password']   = Hash::make($request->input('password'));
               $user         = User::create($input);
               $role         = Role::find(2);
        $user->assignRole($role->name);

        if($request->hasfile('image'))
        {
            $file = $request->file('image');
            $extenstion = $file->getClientOriginalExtension();
            $filename = time().'.'.$extenstion;
            $file->move('images/img/', $filename);
            $data['img'] = $filename;
        }
        $result='';
        if($user) {
            $data['first_name'] = $request->input('first_name');
            $data['last_name'] = $request->input('last_name');
            $data['phone'] = $request->input('phone');
            $data['user_id'] = $user->id;
            $data['gender'] = $request->input('gender');
            $data['department_id'] = $request->input('department_id');
            $data['designation_id'] = $request->input('designation_id');
            $data['date_of_joining'] = $request->input('date_of_joining');
            $data['about'] = $request->input('about');
            $data['status'] = 5;
            $result = Employee::create($data);


        }
        return $result;

    }

    /**
     * @param $id
     * @param EmployeeUpdateRequest $request
     * @return mixed
     */
    public function update($id, EmployeeUpdateRequest $request)
    {
        $employee = Employee::find($id);
        $input['first_name'] = $request->input('first_name');
        $input['last_name'] = $request->input('last_name');
        $input['username'] = $this->username($request->input('email'));
        $input['email'] = $request->input('email');
        $input['phone'] = $request->input('phone');
        if($request->hasfile('image'))
        {
           $destination ='images/img/'.$employee->img;
           if(File::exists($destination))
           {
               File::delete($destination);
           }
            $file = $request->file('image');
            $extenstion = $file->getClientOriginalExtension();
            $filename = time().'.'.$extenstion;
            $file->move('images/img/', $filename);
            $input['img'] = $filename;
            $data['img'] = $filename;
        }
        $employee->update($input);
        $user = User::find($employee->user_id);
       
        if($user) {
            $data['first_name'] = $request->input('first_name');
            $data['last_name'] = $request->input('last_name');
            $data['phone'] = $request->input('phone');
            $data['user_id'] = $user->id;
            $data['gender'] = $request->input('gender');
            $data['department_id'] = $request->input('department_id');
            $data['designation_id'] = $request->input('designation_id');
            $data['date_of_joining'] = $request->input('date_of_joining');
            $data['about'] = $request->input('about');
            $data['status'] = 5;
            $user->update($data);

        }
        return $employee;
    }

    public function check($id,$request)
    {

        if($request['status'] == 1){
            $checkin = new Attendance();
            $checkin->employee_id            = $id;
            $checkin->status                 = $request['status'];
            $checkin->checkin_time           = $request['checkin_time'];
            $checkin->date                   = date('Y-m-d', strtotime($request['date']));
            $checkin->save();
            return $checkin;
        }elseif ($request['status'] == 2){

            $checkout = Attendance::where(['employee_id'=>$id,'date'=>date('Y-m-d')])->first();
            $checkout->status                   = $request['status'];
            $checkout->checkout_time            = $request['checkout_time'];
            $checkout->date                     = date('Y-m-d', strtotime($request['date']));
            $checkout->save();
            return $checkout;
        }
        return false;
    }
    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        try {
            $employee = Employee::find($id);
            $employee->user->delete();
            $employee->delete();
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    private function username($email)
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }
}
