<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Helper\Tool;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    // creating API to FECTCH data
    // public function index(){
    //     $users = Users::all();
    //     if($users->count() > 0){
    //         return response()->json([
    //             'status' => 200,
    //             'users' => $users
    //         ], 200);
    //     } else{
    //         return response()->json([
    //             'status' => 404,
    //             'message' => 'No users found!'
    //         ], 404);
    //     }
    // }

    // add user data
    public function createUser(Request $request){
        // DB begin
        DB::beginTransaction();
    
        //try (success) catch (error)
        try{
            // validate data => namaLengkap & email required
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:191',
                'namaLengkap' => 'required|string|max:191',
                'tanggalLahir' => 'nullable|date',
                'telepon' => 'nullable|string',
                'kota' => 'nullable|string',
                'pekerjaan' => 'nullable|string',
                'password' => 'required|string',
            ]);
    
            // create user
            $user = Users::create([
                'namaLengkap' => $request->input('namaLengkap'),
                'tanggalLahir' => $request->input('tanggalLahir'),
                'email' => $request->input('email'),
                'telepon' => $request->input('telepon'),
                'kota' => $request->input('kota'),
                'pekerjaan' => $request->input('pekerjaan'),
                'password' => $request->input('password'),
            ]);
    
            // kalau udah ok semua, db commit
            DB::commit();
            // return
            // Return a success response
            return response()->json([
                'success' => true,
                'messages' => 'User ditambahkan',
                'data' => $user,
            ], JsonResponse::HTTP_CREATED);
    
        } 
        catch (Exception $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'messages' => $e->getMessage(),
                'data' => null,
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
    // get user by id
    public function getUserID($id){
        DB::beginTransaction();
    
        try{
            $finduser = Users::find($id);

            if($finduser){
                DB::commit();
                // Return a success response
                return Tool::response(true, 'User is found', $finduser, Response::HTTP_CREATED);
            }else{
                DB::rollBack();

            // Return a failure response
            return Tool::response(false, 'User is not found', $finduser, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
        } catch(Exception $e){
            DB::rollBack();

            // Return a failure response
            return Tool::response(false, 'User is not found', $finduser, Response::HTTP_UNPROCESSABLE_ENTITY);
            
        }
        
    }

    // edit user by id
    public function editUser(Request $request, $id){
        // DB begin
        DB::beginTransaction();
    
        //try (success) catch (error)
        try{
            // validate data => namaLengkap & email required
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:191',
                'namaLengkap' => 'required|string|max:191',
                'tanggalLahir' => 'nullable|date',
                'telepon' => 'nullable|string',
                'kota' => 'nullable|string',
                'pekerjaan' => 'nullable|string',
                'password' => 'required|string',
            ]);
    
            // CARI user by id
            $cariuser = Users::find($id);
            
            $cariuser->update([
                'namaLengkap' => $request->input('namaLengkap'),
                'tanggalLahir' => $request->input('tanggalLahir'),
                'email' => $request->input('email'),
                'telepon' => $request->input('telepon'),
                'kota' => $request->input('kota'),
                'pekerjaan' => $request->input('pekerjaan'),
                'password' => $request->input('password'),
            ]);
    
            // kalau udah ok semua, db commit
            DB::commit();
            // return
            // Return a success response
            return response()->json([
                'success' => true,
                'messages' => 'User UPDATED',
                'data' => $cariuser,
            ], JsonResponse::HTTP_CREATED);
    
        } 
        catch (Exception $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'messages' => $e->getMessage(),
                'data' => null,
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    // delete user by id 
    public function deleteUser($id){
        DB::beginTransaction();

        $finduser = Users::find($id);
        
        // condition
        if($finduser){
            $finduser->delete();

            DB::commit();
            return Tool::response(true, 'User is deleted!', $finduser, Response::HTTP_CREATED);
        } else{
            DB::rollBack();

            return Tool::response(false, 'User is not found', null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    // filter user
    public function filterUsers(Request $request)
{
    // 1. ambil input parameter dr http, dan kasih nilai default
    $page = $request->input('page', 1);
    $take = $request->input('take', 10);
    $orderBy = $request->input('orderBy', 'namaLengkap');
    $order = $request->input('order', 'ASC');
    $search = $request->input('search', null);

    // 2. inisialisasi query 
    $query = Users::query();

    // 3. filter search
    if ($search) {
        $query->where('namaLengkap', 'like', '%' . $search . '%');
    }

    // 4. quesry disorting based on orderBy (nama lengkap dan asc or desc)
    $query->orderBy($orderBy, $order);

    // 5. pagination. 
    $users = $query->skip(($page - 1) * $take)->take($take)->get();

    // 6. count-> itung all data di database
    $total = Users::count(); 

    // 7. itung total page dari data dan take yg kita tentuin
    $totalPages = ceil($total / $take);

    // 8. pagination nextpage sama prev page
    $next = $page < $totalPages;
    $previous = $page > 1;

    // 9. return 
    return response()->json([
        'success' => true,
        'page' => $page,
        'take' => $take,
        'orderBy' => $orderBy,
        'order' => $order,
        'search' => $search,
        'totalPages' => $totalPages,
        'hasNext' => $next,
        'hasPrevious' => $previous,
        'data' => $users,
    ], 200);
}

}
