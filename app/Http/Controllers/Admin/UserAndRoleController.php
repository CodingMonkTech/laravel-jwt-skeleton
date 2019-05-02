<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Validator;
use DataTables;
use Carbon\Carbon;

class UserAndRoleController extends Controller
{    

    // Users
    public function getUsers()
    {
        $users = User::whereDoesntHave('roles')->orwhereHas('roles', function ($query) {
            $query->whereNotIn('name', ['user']);
        })->get();
       
       $response = array('status' => true,'message'=>"users retrieved.",'data'=>$users);
            return response()->json($response, 200);
    }
    
    // Users
    public function getDTUsers()
    {
       $users = User::whereDoesntHave('roles')->orwhereHas('roles', function ($query) {
            $query->whereNotIn('name', ['user']);
        })->get();
       
       return DataTables::of($users)->setRowId('id')->make(true);
    }

    public function createUser(Request $request){
        $validate=User::validator($request);
        if($validate->fails()){
            $response = array('status' => false,'message'=>'Validation error','data'=>$validate->messages());
            return response()->json($response, 400);
        }

        $email_verified_at=Carbon::now();

        $User= User::create([
            'name' => $request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'contact'=>$request->contact,
            'gender'=>$request->gender,
            'dob'=>$request->dob,
            'email_verified_at'=>$email_verified_at
        ]);

        $response = array('status' => true,'message'=>'User created successfully.','data'=>$User);
        return response()->json($response, 200);
    }

    public function updateUser(Request $request){        
        
        $exist=User::whereNotIn('id',[$request->id])->where('email',[$request->email])->first();

        if($exist){
            $response = array('status' => false,'message'=>'Email already exists.');
            return response()->json($response, 400);
        }

        $validate=User::updateValidator($request);

        if($validate->fails()){
            $response = array('status' => false,'message'=>'Validation error','data'=>$validate->messages());
            return response()->json($response, 400);
        }

        $User= User::find($request->id);
        if($User){
            $User->name=$request->name;
            $User->email=$request->email;
            $User->contact=$request->contact;
            $User->gender=$request->gender;
            $User->dob=$request->dob;
            $User->save();
            $response = array('status' => true,'message'=>'User updated successfully.','data'=>$User);             
            return response()->json($response, 200);
        }else{
            $response = array('status' => false,'message'=>'User not found.');
            return response()->json($response, 404);
            }            
    }

    public function getUser($id){
        $User= User::find($id);  
        if($User){
            $response = array('status' => true,'message'=>"user retrieved.",'data'=>$User);
            return response()->json($response, 200);
        }else{
            $meta = array('status' => false,'message'=>'User not found.');
            $messages = array('data' => array(),'meta'=>$meta);
            return response()->json($messages, 404);
        }
    }

    public function deleteUser($id){
        $User= User::find($id);         
        
         if($User){
            $User->roles()->detach();
            $User->delete(); 
            $response = array('status' => true,'message'=>'User successfully deleted.');             
            return response()->json($response, 200);
        }else{
            $response = array('status' => false,'message'=>'User not found','data' => array());
            return response()->json($response, 404);
        }
    }

    public function resetPassword(Request $request){   
        $default_password=123456;
        $default_password_ob=Setting::where('key','default_password')->first();
        
        if($default_password_ob){
            $default_password=$default_password_ob->value;
        }

        $User=User::find($request->id);
        if($User){
            $User->password=Hash::make($default_password);
            $User->save();
            $response = array('status' => true,'message'=>'User password reseted successfully.');             
            return response()->json($response, 200);
        }
        else{
            $response = array('status' => false,'message'=>'User not found.','data' => array());             
            return response()->json($response, 404);
        }            
    }

    // User Role Assign
    public function assignRole(Request $request){
        $user = User::find($request->user_id);
        if($user){
            $user->roles()->sync($request->role_id);
            $response = array('status' => true,'message'=>'Role assigned successfully.');
            return response()->json($response, 200);    
        }else{
            $response = array('status' => false,'message'=>'User not found.','data' => array());             
            return response()->json($response, 404);
        }
        
    }

    public function getUserRole($id){
        $User= User::find($id);  
         if($User){
            $response = array('status' => true,'message'=>'Role retrieved.','data'=>$User->roles[0]);             
            return response()->json($response, 200);
        }else{
            $response = array('status' => false,'message'=>'User not found.','data' => array());             
            return response()->json($response, 404);
        }
    }

    // Roles
    public function getDTRoles()
    {
       return DataTables::of(Role::query())->setRowId('id')->make(true);
    }

    // Users
    public function getRoles()
    {
        $Roles = Role::all();
       
        $response = array('status' => true,'message'=>"roles retrieved.",'data'=>$Roles);
            return response()->json($response, 200);
    }

    public function createRole(Request $request){
        $validate=Role::validator($request);
        if($validate->fails()){            
             $response = array('status' => false,'message'=>"Validation error.",'data'=>$validate->messages());
            return response()->json($messages, 400);
        }

        $Role= Role::create([
            'name' => $request->name,
            'display_name'=>$request->display_name,
            'description'=>$request->description,
        ]);

        $response = array('status' => true,'message'=>'Role created successfully.','data'=>$Role);return response()->json($response, 200);
    }

    public function updateRole(Request $request){        
        
        $exist=Role::whereNotIn('id',[$request->id])->where('name',[$request->name])->first();

        if($exist){
            $response = array('status' => false,'message'=>'Role name already exists.');
            return response()->json($response, 400);
        }

        $validate=Role::updateValidator($request);

        if($validate->fails()){
            $response = array('status' => false,'message'=>'Validation error','data'=>$validate->messages());
            return response()->json($response, 400);
        }

        $Role= Role::find($request->id);
        if($Role){
            $Role->name=$request->name;
            $Role->display_name=$request->display_name;
            $Role->description=$request->description;
            $Role->save();
            $response = array('status' => true,'message'=>'Role updated successfully.','data'=>$Role);             
            return response()->json($response, 200);
        }else{
            $response = array('status' => false,'message'=>'role not found.');             
            return response()->json($response, 404);
        }            
    }

    public function getRole($id){
        $Role= Role::find($id);  
         if($Role){
            $response = array('status' => 'success','data'=>$Role);             
            return response()->json($response, 200);
        }else{
            $meta = array('status' => 'error','message'=>'role not found.');
            $messages = array('data' => array(),'meta'=>$meta);
            return response()->json($messages, 404);
        }
    }

    public function deleteRole($id){        
        $Role= Role::whereId($id)->delete(); 
         if($Role){
            $response = array('status' => 'success','message'=>'Role successfully deleted.');             
            return response()->json($response, 200);
        }else{
            $meta = array('status' => 'error','message'=>'role not found');
            $messages = array('data' => array(),'meta'=>$meta);
            return response()->json($messages, 404);
        }
    }

}
