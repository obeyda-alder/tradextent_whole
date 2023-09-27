<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tax;
use DB;
use App\Models\CategoryTax;
use App\Models\ProductTax;

class TaxController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:vat_&_tax_setup'])->only('index','create','edit','destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all_taxes = Tax::orderBy('created_at', 'desc')->get();
        return view('backend.setup_configurations.tax.index', compact('all_taxes'));
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
    public function store(Request $request)
    {
        $tax = new Tax;
        $tax->name = $request->name;
//        $pickup_point->address = $request->address;
        
        if ($tax->save()) {

            flash(translate('Tax has been inserted successfully'))->success();
            return redirect()->route('tax.index');

        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tax = Tax::findOrFail($id);
        return view('backend.setup_configurations.tax.edit', compact('tax'));
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
        $tax = Tax::findOrFail($id);
        $tax->name = $request->name;
//        $language->code = $request->code;
        if($tax->save()){
            flash(translate('Tax has been updated successfully'))->success();
            return redirect()->route('tax.index');
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }
    
    public function change_tax_status(Request $request) {
        $tax = Tax::findOrFail($request->id);
        if($tax->tax_status == 1) {
            $tax->tax_status = 0;
        } else {
            $tax->tax_status = 1;
        }
        
        if($tax->save()) {
            return 1;
        } 
        return 0;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        if($id == 3 || $id == 4 ||$id == 5 ){
            flash(translate('Can\'t be deleted!'))->error();
            return back();
        }
        DB::beginTransaction();
        try{
            Tax::destroy($id);
            $prod_taxs = ProductTax::where('tax_id',$id)->get();
            $cat_taxs = CategoryTax::where('tax_id',$id)->get();
            foreach($prod_taxs as $p_tax){
                $p_tax->delete();
            }
            foreach($cat_taxs as $c_tax){
                $c_tax->delete();
            }
            DB::commit();
            flash(translate('Tax has been deleted successfully'))->success();
            return redirect()->route('tax.index');

        }catch (\Exception $e) {
            DB::rollback();
            flash(translate('Something went wrong'))->error();
            return back();
        }
        // if(Tax::destroy($id)){
        // }
        // else{
        //     flash(translate('Something went wrong'))->error();
        //     return back();
        // }
    }
}
