<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UTILITY\DataUtilityController;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailNotify;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $companyCode = "";   
    protected $table = "";

    function __construct(Request $request) {
        $getData = new UtilityController($request);
        $this->companyCode = $getData->getCompanyCode();        
    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */



    public function getPassword($n) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';      
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }      
        return $randomString;
    }



    public function store(Request $request)
    {
        
        
        $customer = Customer::where('customerId', $request->customerId)->first();     
        
        if($customer){                             
            return response()->json(["message" => "Customer ID is already exist"]);
        }        
        else{
            $customer = new Customer;
            $customer->customerName = $request->customerName;
            $customer->email = $request->email;
            $customer->phoneNo = $request->phoneNo;  
            $customer->address = $request->address;
            $customer->customerId = $request->customerId;              
            $imagePath = "";
            
            $image = $request->customerLogo;  // your base64 encoded
            if($image){
                $image = str_replace('data:image/png;base64,', '', $request->customerLogo);
                $image = str_replace(' ', '+', $image);
                $imageName =  "customerLogo.png";
                //$picture   = date('His').'-'.$filename;                
                $path = "Customers/".$request->customerId."/logo";     
                $imagePath = $path."/".$imageName;        
                Storage::disk('public_uploads')->put($path."/".$imageName, base64_decode($image));                
            }
           
            //code to request a file from form

            // if ($request->hasFile('customerLogo'))
            // {
            //     $allowedfileExtension=['png'];
            //     $file      = $request->file('customerLogo');
            //     $filename  = $file->getClientOriginalName();
            //     $extension = $file->getClientOriginalExtension();
            //     $file_size = $file->getSize();
            //     $picture   = date('His').'-'.$filename;

            //     //move image to public/img folder
            //     $check=in_array($extension,$allowedfileExtension);

            //     if($check){                   
            //         $path = "Customers/".$request->customerId."/logo";
            //         $file->move(public_path($path), $picture);
            //         $imagePath = $path."/".$picture;
            //         // return response()->json([
            //         //         "message" => $imagePath,                            
            //         //     ]
            //         // );
            //     }
            //     else{
            //         return response()->json([
            //             "message" => "Select the png image",
            //             "status" => 404
            //         ]);
            //     }                
            // }
            // else{
            //     return response()->json([
            //         "message" => "Select the image",
            //         "status" => 404
            //     ]);
            // }

            $customer->customerLogo = $imagePath;
            $customer->customerTheme = $request->customerTheme;   
            $customer->alertLogInterval = 5;  
            $customer->deviceLogInterval = 5;  
            $customer->sensorLogInterval = 5;
            $customer->periodicBackupInterval = 2;
            $customer->dataRetentionPeriodInterval = 2;


            $customer->save();


            $password = $this->getPassword(10);  
            $encryptedPassword = Hash::make($password);

            $data = [
                'userid'=>$customer->email,
                'subject' => 'Application Login Credentials',
                'body' =>$password
            ];

            Mail::send('credentialmail',$data, function($messages) use ($customer){
                $messages->to($customer->email);
                $messages->subject('Application login credentials');        
            });

            $user = new User;
            $user->name = "systemSpecialist";
            $user->email = $request->email;
            $user->mobileno = $request->phoneNo;
            $user->employeeId = "00000";
            $user->password = $encryptedPassword;
            $user->user_role = "systemSpecialist";
            $user->companyCode = $request->customerId;
            $user->save();    

            return response()->json(["message"=>"Customer added succesfully"], 200); 
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);            
        $oldCompanycode = $customer->customerId;
        $oldemail = $customer->email;
        $oldPhoneNo = $customer->phoneNo;

        if($customer){            
            
            $customer->customerName = $request->customerName;
            $customer->email = $request->email;
            $customer->phoneNo = $request->phoneNo;  
            $customer->address = $request->address;
            $customer->customerId = $request->customerId;  
            // $customer->customerLogo = $request->customerLogo;
            
            $image = $request->customerLogo;  // your base64 encoded
            if($image){
                $image = str_replace('data:image/png;base64,', '', $request->customerLogo);
                $image = str_replace(' ', '+', $image);
                $imageName =  "customerLogo.png";
                //$picture   = date('His').'-'.$filename;                
                $path = "Customers/".$request->customerId."/logo";     
                $imagePath = $path."/".$imageName;        
                Storage::disk('public_uploads')->put($path."/".$imageName, base64_decode($image));   
                $customer->customerLogo = $imagePath;             
            }
           
            $customer->customerTheme = $request->customerTheme;
            // $customer->alertLogInterval = $request->alertLogInterval;  
            // $customer->deviceLogInterval = $request->deviceLogInterval;  
            // $customer->sensorLogInterval = $request->sensorLogInterval;  
            
            
            
            $customer->update();      
         
            if($oldCompanycode != $request->customerId){
                $user = DB::table('users')
                    ->where('companyCode', '=', $oldCompanycode)           
                    ->update(array(
                        'companyCode' => $request->customerId,                
                )); 
            }

            if($oldemail != $request->email){
                $user = DB::table('users')
                    ->where('companyCode', '=', $oldCompanycode)  
                    ->where('employeeId', '=', '000')             
                    ->update(array(
                        'email' => $request->email,                
                ));
            }

            if($oldPhoneNo != $request->phoneNo){
                $user = DB::table('users')
                    ->where('companyCode', '=', $oldCompanycode)
                    ->where('employeeId', '=', '000')         
                    ->update(array(
                        'mobileno' => $request->phoneNo,                
                ));                  
            }                    

            return response()->json(
                [
                    "message" =>"Data Updated succesfully",                          
                ]
            );
        }
        else{
            return response()->json(["message" =>"Customer not Found"]);
        }        


    }    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $customer = Customer::find($id);        

        if($customer){   
            $oldCompanycode = $customer->customerId;            
            $customer->delete();               
            if($oldCompanycode){
                $user = DB::table('users')
                    ->where('companyCode', '=', $oldCompanycode)           
                    ->delete(); 
            } 
            return response()->json(
                [
                    "message" =>"Data Deleted succesfully",                          
                ]
            );
        }
        else{
            return response()->json(["message" =>"Customer not Found"]);
        }

        return response($response,$status);
    }

    public function updateCustomerSettings(Request $request){
        try{
            $customer = DB::table('customers')
                    ->where('customerId', '=', $this->companyCode)->first();

            if(!$customer){
                throw new Exception("Customer not found");
            }

            if($customer){
                $id = $customer->id;
                $customers = Customer::find($id);   
                $customers->alertLogInterval =  $request->alertLogInterval;  
                $customers->deviceLogInterval = $request->deviceLogInterval;  
                $customers->sensorLogInterval = $request->sensorLogInterval;
                $customers->periodicBackupInterval = $request->periodicBackupInterval;
                $customers->dataRetentionPeriodInterval = $request->dataRetentionPeriodInterval;
                $customers->save();

                $response = [
                    "message"=>"Updated Customer settings Successfully"
                ];
                $status = 200;
            }

        }catch(Exception $e){
            $response = [
                "error"=>true,
                "message"=>$e->getMessage()              
            ];
            $status = 401;            
        }

        return response($response,$status); 
        

    }

    public function customerCustomData(Request $request){

        //includes search, sort, and pagination which is page data

        $query = Customer::query();

        if($s = $request->input(key:'s')){
            $query->whereRaw(sql:"customerName LIKE '%". $s ."%'")
                ->orWhereRaw(sql:"email LIKE  '%". $s ."%'");
        }

        $getData = new DataUtilityController($request,$query);
        $response = $getData->getData();
        $status = 200;

        return response($response,$status);
    }


    public function uploadImageFile(Request $request){                  
        
        $image = $request->image;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $request->image);
        $image = str_replace(' ', '+', $image);
        $imageName =  '1.png';
        if($image){           
            $path = "Customers/ssl/logo/";               
           
            Storage::disk('public_uploads')->put($path."/".$imageName, base64_decode($image));
        }
      
    }    
}




