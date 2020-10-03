<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json(
            [
                'status' => 'success',
                'users' => $users->toArray()
            ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json(
            [
                'status' => 'success',
                'user' => $user->toArray()
            ], 200);
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

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        try {
            User::find($id)->update($data);
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            User::find($id)->delete();
            return ['status'=>'success'];
        } catch (ModelNotFoundException $e) {
            return ['status'=>'error'];
        }
    }

    /*
    AJAX request for Census pagination
    */
    public function pagnination(Request $request) {

        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        ## Custom Field value
        //$searchByEmpID  = $request->get('searchByEmpID');

        // Total records
        $totalRecords = User::select('count(*) as allcount');

        $data = User::select('users.*')
            ->orderBy($columnName,$columnSortOrder)
            ->where(function($q) use($searchValue){
                $q->where('name', 'like', '%' .$searchValue . '%')
                ->orWhere('email', 'like', '%' .$searchValue . '%')
                ->orWhere('created_at', 'like', '%' .$searchValue . '%');
            });

        //Custom Search
        /*if($searchByEmpID != ''){
            $data = $data->where('employer_id', $searchByEmpID);
        }*/

        $totalRecords = $totalRecords->count();
        $totalRecordswithFilter = $data->count();

        $records = $data->skip($start)->take($rowperpage)->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $key => $record){
           
            $data_arr[] = array(
              "id" => ++$key,
              "name" => $record->name,
              "email" => $record->email,
              "created_at" => $record->created_at->format('Y-m-d'),
              "action" => $record,
            );
        }

        $res = [
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                "aaData" => $data_arr
            ];

        return $res;
         
    }

}
