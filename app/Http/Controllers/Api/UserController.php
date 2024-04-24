<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {

        $where = [];

        if ($request->name) {
            $where[] = ['name', 'like', '%' . $request->name . '%'];
        }

        if ($request->email) {
            $where[] = ['email', 'like', '%' . $request->email . '%'];
        }


        $users = User::orderBy('id', 'desc');

        if (!empty($where)) {
            $users = $users->where($where);
        }

        $users = $users->get();

        if ($users->count() > 0) {
            $status = 'success';
        } else {
            $status = 'no_data';
        }

        $response = [
            'status' => $status,
            'data' => $users
        ];
        return $response;
    }

    public function detail($id)
    {

        $user = User::find($id);

        if (!$user) {
            $status = 'no_data';
        } else {
            $status = 'success';
        }

        $response = [
            'status' => $status,
            'data' => $user
        ];

        return $response;
    }

    public function create(Request $request)
    {

        $this->validation($request);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        if ($user->id) {
            $response = [
                'status' => 'success',
                'data' => $user
            ];
        } else {
            $response = [
                'status' => 'error',
            ];
        }
        return $response;
    }

    public function update(Request $request, $id)
    {

        $user = User::find($id);

        if (!$user) {
            $response = [
                'status' => 'no_data'
            ];
        } else {
            $this->validation($request, $id);
            $method = $request->method();

            if ($method == 'PUT') {
                $user->name = $request->name;
                $user->email = $request->email;
                if ($request->password) {
                    $user->password = Hash::make($request->password);
                } else {
                    $user->password = null;
                }
                $user->save();
            } else {
                if ($request->name) {
                    $user->name = $request->name;
                }

                if ($request->email) {
                    $user->email = $request->email;
                }

                if ($request->password) {
                    $user->password = Hash::make($request->password);
                }

                $user->save();
            }

            $response = [
                'status' => 'success',
                'data' => $user
            ];
        }

        return $response;
    }

    public function delete(User $user)
    {

        $status = User::destroy($user->id);
        if ($status) {
            $response = [
                'status' => 'success'
            ];
        } else {
            $response = [
                'status' => 'error'
            ];
        }

        return $response;
    }

    public function validation($request, $id = 0)
    {

        $emailValidation = 'required|email|unique:users';

        if (!empty($id)) {
            $emailValidation = ',email,' . $id;
        }

        $rules = [
            'name' => 'required|min:5',
            'email' => $emailValidation,
            'password' => 'required|min:8'
        ];

        $messages = [
            'name.required' => 'Tên bắt buộc phải nhập',
            'name.min' => 'Tên không được nhỏ hơn :min ký tự',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu không được ít hơn :min kí tự'
        ];

        $request->validate($rules, $messages);
    }
}