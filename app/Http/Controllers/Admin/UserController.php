<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //show user list to admin
    public function showUserList(Request $request)
    {
        if($request->ajax()){

            $users = User::select(['id', 'name', 'email', 'mobile', 'role', 'is_verified', 'created_at']);

            return datatables()->eloquent($users) //of($users)
                ->addColumn('role', function ($user) {
                     return ucfirst($user->role);
                 })
                ->addColumn('is_verified', function ($user) {
                     return $user->is_verified ? 'Yes' : 'No';
                })
                ->addColumn('created_at', function ($user) {
                     return $user->created_at->format('Y-m-d h:i:s');
                })
                //Action buttons
                ->addColumn('action', function ($user) {
                     return '<button data-id="'.$user->id.'" class="btn btn-sm btn-primary edit-user-btn">Edit</button>
                     <button data-id="'.$user->id.'" class="btn btn-sm btn-danger delete-user-btn">Delete</button>';
                 })
                ->make(true);
        }
        //$users = User::all(); // Later: fetch users with pagination
        return view('admin.user.user-list');//, compact('users')
    }

    //edit user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        dd($user);

        return view('admin.user.edit-user', compact('user'));
    }

    //update user
    public function update(Request $request)
    {
        //dd($request->all());
        try {
            $user = User::findOrFail($request->input('id'));
            if($user){
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->mobile = $request->input('mobile');
                $user->save();
                return response()->json(['status' => 'success', 'message' => 'User updated successfully'], 200);
            }else{
                return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
            }
           
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Unable to update user'.$e->getMessage()], 404);
        }
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email|unique:users,email,'.$id,
        //     'mobile' => 'required|string|max:15|unique:users,mobile,'.$id,
        //     'role' => 'required|in:user,admin',
        //     'is_verified' => 'required|boolean',
        // ]);
    }

    //delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if($user){
             $user->delete();
            return response()->json(['status' => 'success', 'message' => 'User deleted successfully']);
        }else{
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }
       
    }

    public function getUserListData()
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }

}
