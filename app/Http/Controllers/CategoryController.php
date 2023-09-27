<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\CategoryTax;
use App\Models\ProductTax;
use App\Models\CategoryTranslation;
use App\Utility\CategoryUtility;
use App\Services\ProductTaxService;
use Illuminate\Support\Str;
use Cache;
use DB;

class CategoryController extends Controller
{
    protected $productTaxService;

    public function __construct(ProductTaxService $productTaxService) {
        // Staff Permission Check
        $this->middleware(['permission:view_product_categories'])->only('index');
        $this->middleware(['permission:add_product_category'])->only('create');
        $this->middleware(['permission:edit_product_category'])->only('edit');
        $this->middleware(['permission:delete_product_category'])->only('destroy');

        $this->productTaxService = $productTaxService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $categories = Category::orderBy('order_level', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $categories = $categories->where('name', 'like', '%'.$sort_search.'%');
        }
        $categories = $categories->paginate(15);
        return view('backend.product.categories.index', compact('categories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category = new Category;
        $category->name = $request->name;
        $category->order_level = 0;
        if($request->order_level != null) {
            $category->order_level = $request->order_level;
        }
        $category->digital = $request->digital;
        $category->banner = $request->banner;
        $category->icon = $request->icon;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        if ($request->parent_id != "0") {
            $category->parent_id = $request->parent_id;

            $parent = Category::find($request->parent_id);
            $category->level = $parent->level + 1 ;
        }

        if ($request->slug != null) {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        }
        else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }
        if ($request->commision_rate != null) {
            $category->commision_rate = $request->commision_rate;
        }

        $category->save();


        
        $request->merge(['category_id' => $category->id]);

        //VAT & Tax
        if($request->tax_id) {
            $this->productTaxService->storeCatTax($request->only([
                'tax_id', 'tax', 'tax_type', 'category_id'
            ]));
        }
        

        $category->attributes()->sync($request->filtering_attributes);

        $category_translation = CategoryTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'category_id' => $category->id]);
        $category_translation->name = $request->name;
        $category_translation->save();

        flash(translate('Category has been inserted successfully'))->success();
        return redirect()->route('categories.index');
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
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $category = Category::findOrFail($id);
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->whereNotIn('id', CategoryUtility::children_ids($category->id, true))->where('id', '!=' , $category->id)
            ->orderBy('name','asc')
            ->get();

        return view('backend.product.categories.edit', compact('category', 'categories', 'lang'));
    }
    public function edit_tax(Request $request, $id)
    {
        $lang = $request->lang;
        $category = Category::findOrFail($id);
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->whereNotIn('id', CategoryUtility::children_ids($category->id, true))->where('id', '!=' , $category->id)
            ->orderBy('name','asc')
            ->get();

        return view('backend.product.categories.edit_tax', compact('category', 'categories', 'lang'));
    }
    public function update_tax(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->merge(['category_id' => $category->id]);
        
        //VAT & Tax
        if ($request->tax_id) {

            $collection = collect($request->only([
                'tax_id', 'tax', 'tax_type', 'category_id'
            ]));

            if ($collection['tax_id']) {
                DB::beginTransaction();
                try {          
                foreach ($collection['tax_id'] as $key => $val) {
                    $cate_tax_old = CategoryTax::where('category_id', $category->id)->where('tax_id',$val)->first();
                    if($cate_tax_old){
                        $product_taxs =ProductTax::where('tax_id',$val)->where('tax',$cate_tax_old->tax)->where('tax_type',$cate_tax_old->tax_type)->get();
                    }else{
                        $product_taxs=[];
                    }
                    foreach($product_taxs as $prod_tax){
                        if($prod_tax->product->category_id == $category->id){
                            $prod_tax->tax = $collection['tax'][$key];
                            $prod_tax->tax_type  = $collection['tax_type'][$key];
                            $prod_tax->save();
                        }
                    } 
                    $test = CategoryTax::where('category_id', $category->id)->where('tax_id',$val)->delete();
                    
                    $category_tax = new CategoryTax();
                    $category_tax->tax_id = $val;
                    $category_tax->category_id = $collection['category_id'];
                    $category_tax->tax = $collection['tax'][$key];
                    $category_tax->tax_type = $collection['tax_type'][$key];
                    $category_tax->save();
                    // the tax is new so add it to all products under this category
                    if($test == 0){
                        $products_with_cat = Product::where('category_id',$category->id)->get();
                        foreach($products_with_cat as $product){
                            $product_tax = new ProductTax();
                            $product_tax->tax_id = $category_tax->tax_id;
                            $product_tax->product_id = $product->id;
                            $product_tax->tax = $category_tax->tax;
                            $product_tax->tax_type = $category_tax->tax_type;
                            $product_tax->save();
                        }
                        
                        
                    }
                }
                DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    flash(translate('Something went wrong'))->error();
                    return back();
                }
            }
        }
        flash(translate('Category tax has been updated successfully'))->success();
        return back();
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
        $category = Category::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $category->name = $request->name;
        }
        if($request->order_level != null) {
            $category->order_level = $request->order_level;
        }
        $category->digital = $request->digital;
        $category->banner = $request->banner;
        $category->icon = $request->icon;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        $previous_level = $category->level;

        if ($request->parent_id != "0") {
            $category->parent_id = $request->parent_id;

            $parent = Category::find($request->parent_id);
            $category->level = $parent->level + 1 ;
        }
        else{
            $category->parent_id = 0;
            $category->level = 0;
        }

        if($category->level > $previous_level){
            CategoryUtility::move_level_down($category->id);
        }
        elseif ($category->level < $previous_level) {
            CategoryUtility::move_level_up($category->id);
        }

        if ($request->slug != null) {
            $category->slug = strtolower($request->slug);
        }
        else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }


        if ($request->commision_rate != null) {
            $category->commision_rate = $request->commision_rate;
        }

        $category->save();

        $request->merge(['category_id' => $category->id]);
        //VAT & Tax
        if ($request->tax_id) {

            $collection = collect($request->only([
                'tax_id', 'tax', 'tax_type', 'category_id'
            ]));

            if ($collection['tax_id']) {
                foreach ($collection['tax_id'] as $key => $val) {
                    $cate_tax_old = CategoryTax::where('category_id', $category->id)->where('tax_id',$val)->first();
                    $product_taxs =ProductTax::where('tax_id',$val)->where('tax',$cate_tax_old->tax)->where('tax_type',$cate_tax_old->tax_type)->get();
                    foreach($product_taxs as $prod_tax){
                        if($prod_tax->product->category_id == $category->id){
                            $prod_tax->tax = $collection['tax'][$key];
                            $prod_tax->tax_type  = $collection['tax_type'][$key];
                            $prod_tax->save();
                        }
                    } 
                    CategoryTax::where('category_id', $category->id)->where('tax_id',$val)->delete();
                    $category_tax = new CategoryTax();
                    $category_tax->tax_id = $val;
                    $category_tax->category_id = $collection['category_id'];
                    $category_tax->tax = $collection['tax'][$key];
                    $category_tax->tax_type = $collection['tax_type'][$key];
                    $category_tax->save();
                }
            }
        }

        $category->attributes()->sync($request->filtering_attributes);

        $category_translation = CategoryTranslation::firstOrNew(['lang' => $request->lang, 'category_id' => $category->id]);
        $category_translation->name = $request->name;
        $category_translation->save();

        Cache::forget('featured_categories');
        flash(translate('Category has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if(Product::where('category_id', $category->id)->get()->isNotEmpty()){
            flash(translate('Category has products can NOT be deleted'))->error();
            return redirect()->route('categories.index');
        }
        $category->attributes()->detach();

        // Category Translations Delete
        foreach ($category->category_translations as $key => $category_translation) {
            $category_translation->delete();
        }

        foreach (Product::where('category_id', $category->id)->get() as $product) {
            $product->category_id = null;
            $product->save();
        }

        CategoryUtility::delete_category($id);
        Cache::forget('featured_categories');

        flash(translate('Category has been deleted successfully'))->success();
        return redirect()->route('categories.index');
    }

    public function updateFeatured(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $category->featured = $request->status;
        $category->save();
        Cache::forget('featured_categories');
        return 1;
    }
}
