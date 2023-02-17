<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use Auth;
use Validator;
use Illuminate\Support\Facades\Hash;
use DB;

class UserController extends Controller
{
    // for register /////////////////////////////////////////////

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:5|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered Successfully',
            'user' => $user,
        ]);
    }

    // for login /////////////////////////////////////////////

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if (!($token = auth()->attempt($validator->validated()))) {
            return response()->json($validator->errors(), 400);
        }
        return $this->respondWithToken($token);
    }

    // for fetch user data ///////////////////////////////////

    public function profile(Request $request)
    {
        return response()->json(auth()->user());
    }

    // for refresh //////////////////////////////

    public function refresh(Request $request)
    {
        return $this->respondWithToken(auth()->refresh());
    }

    // for logout /////////////////////////////////

    public function logout(Request $request)
    {
        auth()->logout();

        return response()->json(['message' => 'User Successfully logged out']);
    }

    // for update /////////////////////////////////////////

    public function updateProfile(Request $request)
    {
        if (auth()->user()) {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required|string',
                'email' => 'required|string|email',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $user = User::find($request->id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
            return response()->json([
                'success' => true,
                'msg' => 'user data Updated successfully',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'user is not authorized',
            ]);
        }
    }

    // for delete//////////////////////////////////////////////////

    function delete(Request $request)
    {
        if (auth()->user()) {
            $product = User::Find($request->id);
            $result = $product->delete();

            if ($result) {
                return ['return' => 'User deleted'];
            } else {
                return ['return' => 'User not found'];
            }
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'user is not authorized',
            ]);
        }
    }

    ////----------------------------------------------------------------

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>
                auth()
                    ->factory()
                    ->getTTL() * 60,
        ]);
    }

    // products upload   ////////////////////////////////

    public function uploadproducts(Request $request)
    {
        if (auth()->user()) {
            $validator = Validator::make($request->all(), [
                'products_name' => 'required|string',
                'SKU' => 'required|string|unique:products',
                'price' => 'required|integer',
                'image' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $images = $request->file('image');
            $imageName = '';
            foreach ($images as $image) {
                $new_name = rand() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('/upload', $new_name));
                $imageName = $imageName . $new_name . ',';
            }
            print_r($imageName);

            $product = Product::create([
                'products_name' => $request->products_name,
                'SKU' => $request->SKU,
                'price' => $request->price,
                'image' => $imageName,
            ]);

            return response()->json([
                'message' => 'User registered Successfully',
                'product' => $product,
            ]);
        } else {
            return response()->json([
                'message' => 'User not authenticated',
            ]);
        }
    }
}
