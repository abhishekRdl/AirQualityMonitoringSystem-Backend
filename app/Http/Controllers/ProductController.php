<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
// use Illuminate\Support\Facades\DB;


// use App\Models\Location;
// use App\Models\Branch;
// use App\Models\Facilities;
// use App\Models\Building;
// use App\Models\Floor;
// use App\Models\labDepartment;



class ProductController extends Controller
{
    
    // public function index(){        
    //     $product = Product::all();  
    //     if($product){
    //         return response()->json(["Products"=>$product], 200);
    //     } 
    //     else{
    //         return response()->json(["Message"=>"Products not found"], 200);
    //     }       
    // }




    public function index(Request $request){
        $userRole = "";
        $userId = "";
        $companyCode = "";

         $location_id =  $request->location_id;
         
        
         return response()->json(["message"=>$location_id],404);
        
        
    
    } 
    
    
    
    public function show($id){
        $product = Product::find($id);
        if($product){
            return response()->json(["Products"=>$product],200);    
        }
        else{
            return response()->json(["Message"=>"Product not found"], 404);
        }        
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required|max:191',
            'description'=>'required|max:191',
            'price'=>'required|max:191',
            'quantity'=>'required|max:191',
        ]);       

        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->save();
        return response()->json(["message"=>"Product added succesfully"], 200); 
    }


    public function update(Request $request, $id){
        $request->validate([
            'name'=>'required|max:191',
            'description'=>'required|max:191',
            'price'=>'required|max:191',
            'quantity'=>'required|max:191',
        ]);
       
        $product = Product::find($id);
        if($product){
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->quantity = $request->quantity;
            $product->save();
            return response()->json(["message"=>"Product updated succesfully"], 200); 
        }
        else{
            return response()->json(["message"=>"Product not found"],404);
        }        
    }

    public function destroy($id){
        $product = Product::find($id);                            
        if($product){
            $product->delete();
            return response()->json(['message'=>"Product Deleted succesfully"], 200);
        }
        else{
            return response()->json(["message"=>"Product not found"],404);
        }
    }

    public function get_data(Request $request){
        $query = Product::query();

        if($s = $request->input(key:'s')){
            $query->whereRaw(sql:"name LIKE '%". $s ."%'");
                // ->orWhereRaw(sql:"description LIKE  '%". $s ."%'");
        }

        if($sort = $request->input(key:'sort')){
            $query->orderBy('id',$sort);
        }

        $perPage = 5;
        $page = $request->input(key:'page', default:1);
        $total = $query->count();

        $result = $query->offset(value:($page - 1) * $perPage)->limit($perPage)->get();
        
        // return response()->json([
        //     "data"=>$query->get()
        // ]);

        return [
            'data' => $result,
            'total_data'=>$total,
            'page'=>$page,
            'last_page'=>ceil(num:$total/ $perPage)
        ];
    }



}
