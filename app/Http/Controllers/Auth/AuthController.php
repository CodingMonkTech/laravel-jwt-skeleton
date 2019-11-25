<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use JWTAuth;
use Jenssegers\Agent\Agent;
use App\Models\JwtToken;
use Illuminate\Http\Request;
use App\User;
use App\Models\Role;
use Crypt;
use Hash;
use Mail;
use Carbon\Carbon;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login','register','verify']]);
    }

    public function register(Request $request){
        $validate=User::validator($request);
        
        if($validate->fails()){
            $response = array('status' => false,'message'=>'Validation error','data'=>$validate->messages());
            return response()->json($response, 400);
        }

        $role=Role::where('name','user')->first();

        $User= User::create([
            'name' => $request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'contact'=>$request->contact,
            'gender'=>$request->gender,
            'dob'=>$request->dob,
            'email_verified_at'=>null
        ]);

        $User->roles()->sync($role->id);

        $verification_code=Crypt::encrypt($User->email);

        $FRONTEND_URL=env('FRONTEND_URL');

        $account_verification_link=$FRONTEND_URL.'/auth/verify?token='.$verification_code;

        $html='<html>
            Hi, '.$User->name.'<br><br>

            Thank you for registering on '.env('APP_NAME').'.

            Here is your account verification link. Click on below link to verify you account. <br><br><a href="'.$account_verification_link.'" target=_blank >'.$account_verification_link.'</a>
        </html>';

        Mail::send('emails.general',["html"=>$html] , function($message) use ($request,$User){
            $message->to($request->email, $User->name)
            ->subject(env('APP_NAME').': Account Verification');
        });


        $response = array('status' => true,'message'=>'You are registered successfully, check email and click on verification link to activate your account.');
        return response()->json($response, 200);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {	

        $credentials = request(['email', 'password']);

        if(empty(request('email')) || empty(request('password'))){
            return response()->json(['status'=>false,'message' => 'both email and password required'], 401);
        }
    
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['status'=>false,'message' => 'Invalid credentials'], 401);
        }

        $user=JWTAuth::user();

        if($user->email_verified_at==null){
            return response()->json(['status'=>false,'message' => 'Email not verfied, verify your email first.'], 401);   
        }

        if($user->is_active==0){
            return response()->json(['status'=>false,'message' => 'You are deactivated, kindly contact admin.'], 401);   
        }

        $this->authenticated($request,$user,$token);
        return $this->respondWithToken($token);
    }

    public function verify(Request $request)
    {
       
        $validate = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        
        if ($validate->fails()) {
            $response = array('status' => false,'message'=>'Validation error','data'=>$validate->messages());
            return response()->json($response, 400);
        }

        $token = $request->token;

        $verification_token = Crypt::decrypt($request->token);
        
        $user = User::where('email',$verification_token)->first();
        $email_verified_at=Carbon::now();

        if($user) {
            $user->email_verified_at = $email_verified_at;
            $user->save();
            
            $response = array('status' =>true ,'message'=>'Account successfully verified');
            return response()->json($response, 200);

        }else{
            $response = array('status' =>false ,'message'=>'Invalid verification token');
            return response()->json($response, 401);
        }
    }

    public function changePassword(Request $request)
    {   
        $validate = Validator::make($request->all(), [
            'password' => 'required'
        ]);
        
        if ($validate->fails()) {
            $response = array('status' => false,'message'=>'Validation error','data'=>$validate->messages());
            return response()->json($response, 400);
        }

        $User=JWTAuth::user();
        if($User){
             $User->password=Hash::make($request->password);
             $User->save();
             $response = array('status' =>true ,'message'=>'Password changed successfully.');
            return response()->json($response, 200);         
        }else{
            $response = array('status' =>false ,'message'=>'User not found');
            return response()->json($response, 404);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {        
        $user=JWTAuth::user();
        $user_data=array('name'=>$user->name,'email'=>$user->email,'roles'=>$user->roles->pluck('name'));
        
        return response()->json($user_data);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        JWTAuth::parseToken()->invalidate();
        return response()->json(['status'=>true,'message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user=JWTAuth::user();

        $user_data=array('name'=>$user->name,'email'=>$user->email,'roles'=>$user->roles->pluck('name'));

        return response()->json([            
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user'=>$user_data,
            
        ]);
    }


    protected function authenticated($request, $user, $token)
    {
        $olduser = $user;
        $user->last_login = date("Y-m-d H:i:s");
        $user->save();

        $agent = new Agent();

        $isDesktop = $agent->isDesktop();
        $isPhone = $agent->isPhone();
        $jwtToken = new JwtToken();
        $jwtToken->user_id = $user->id;
        $jwtToken->token = $token;
        $jwtToken->browser = $agent->browser();;
        $jwtToken->platform = $agent->platform();
        $jwtToken->device = $agent->device();
        $mobileHeader = $request->header('x_platform');
        if (isset($mobileHeader) && $mobileHeader == 'mobile') {
            JwtToken::where('user_id',$user->id)->where('phone',1)->delete();
            
            $jwtToken->phone = 1;
            $jwtToken->save();

        } else {
            JwtToken::where('user_id',$user->id)->where('desktop',1)->delete();
            
            $jwtToken->desktop = 1;
            $jwtToken->save();
        } 
    }
}