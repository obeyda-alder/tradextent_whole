@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">{{ translate('Order Item Details') }}</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table-bordered aiz-table invoice-summary table">
                        <thead>
                            <tr class="bg-trans-dark">
                                <th width="10%">{{ translate('Photo') }}</th>
                                <th class="text-uppercase">{{ translate('Description') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Qty') }}
                                </th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Unit Price') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Sub Total (tax/profit NOT included)') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Profit rate per piece') }}</th> 
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Profit value per piece') }}</th> 
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Total Profit') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Total Tax') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    {{ translate('Grand Total (tax/profit included)') }}</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            // $product = App\Models\Product::find(14);
                            //                     $profit= $product->taxes->where('tax_id',5)->first();
                            //                     $profit?dd($profit->tax):dd(0);
                                                // dd($profit->tax);
                            @endphp
                                <tr>
                                    <td>
                                        @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                            <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">
                                                <img height="50" src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}">
                                            </a>
                                        @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                            <a href="{{ route('auction-product', $orderDetail->product->slug) }}" target="_blank">
                                                <img height="50" src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}">
                                            </a>
                                        @else
                                            <strong>{{ translate('N/A') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                            <strong>
                                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank"
                                                    class="text-muted">
                                                    {{ $orderDetail->product->getTranslation('name') }}
                                                </a>
                                            </strong>
                                            <small>
                                                {{ $orderDetail->variation }}
                                            </small>
                                            <br>
                                            <small>
                                                @php
                                                    $product_stock = json_decode($orderDetail->product->stocks->first(), true);
                                                @endphp
                                                {{translate('SKU')}}: {{ $product_stock['sku'] }}
                                            </small>
                                        @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                            <strong>
                                                <a href="{{ route('auction-product', $orderDetail->product->slug) }}" target="_blank"
                                                    class="text-muted">
                                                    {{ $orderDetail->product->getTranslation('name') }}
                                                </a>
                                            </strong>
                                        @else
                                            <strong>{{ translate('Product Unavailable') }}</strong>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center">
                                        {{ $orderDetail->quantity }}
                                    </td>
                                    <td class="text-center">
                                        {{ single_price($orderDetail->price / $orderDetail->quantity) }}
                                    </td>
                                    <td class="text-center">
                                        {{ single_price($orderDetail->price) }}
                                    </td>
                                    <td class="text-center">
                                        {{$orderDetail->profit}}%
                                    </td>
                                    <td class="text-center">
                                        @php
                                        $profit_val = ($orderDetail->price * $orderDetail->profit) / 100;
                                        @endphp
                                        {{single_price($profit_val / $orderDetail->quantity)}}
                                    </td>
                                    <td class="text-center">
                                        {{single_price($profit_val)}}
                                    </td>
                                    <td class="text-center">
                                        {{single_price($orderDetail->tax - $profit_val)}}
                                    </td>
                                    <td class="text-center">
                                        {{ single_price($orderDetail->price + $orderDetail->tax) }}
                                    </td>
                                    
                                    
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        @if($orderDetail->seller_status == 'accepted with note')
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Order Note')}}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label">{{translate('Total quantity')}}</label>
                    <div class="col-md-8">
                        <input type="number" class="form-control" name="note_quantity" value="{{ $orderDetail->note_quantity }}" placeholder="{{ translate('Total quantity') }}" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-from-label">{{translate('Unit Price')}}</label>
                    <div class="col-md-6">
                        <input type="number" lang="en" min="0" step="0.01" class="form-control" name="note_price" value="{{ $orderDetail->note_price }}" placeholder="{{ translate('Unit Price') }}" readonly>
                    </div>
                    <span class="col-md-3" style="align-self: center;">
                        {{get_system_default_currency()->name}}
                        ({{get_system_default_currency()->symbol}})
                    </span>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-from-label">{{translate('Shipping Days')}}</label>
                    <div class="col-md-8">
                        <input type="number" class="form-control" name="note_shipping_dayes" placeholder="{{ translate('Shipping Days') }}" value="{{ $orderDetail->note_shipping_dayes }}" readonly>
                    </div>
                </div>
                </div>
                <div class="mar-all text-center mb-2">
                <a class="btn btn-danger btn-md" href="{{route('orders.confirm_edit_order', ['id'=>$orderDetail->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Confirm order edit') }}">
                    {{translate('Confirm order edit')}}
                </a>
                </div>
        </div>
        
        @endif
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Edit Profit')}}</h5>
            </div>
            <form class="form form-horizontal mar-top" action="{{route('orders.edit_order_profit')}}" method="POST">
                <div class="card-body">
                    @csrf
                    <input type="hidden" class="form-control" name="order_detail_id" value={{$orderDetail->id}} required>
                    <input type="hidden" class="form-control" name="order_id" value={{$order->id}} required>
                    <div class="form-group row">
                    <label class="col-md-3 col-from-label">{{translate('Current profit rate per piece')}}</label>
                        <div class="form-group col-md-6">
                            <input type="number" class="form-control" name="old_profit" value='{{$orderDetail->profit}}' placeholder="{{ translate('Profit value per piece') }}" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="text" class="form-control" name="tax_type" value='Percent' placeholder="{{ translate('Percent') }}" readonly>
                        </div>                        
                </div>
                @php
                    $products = \App\Models\Product::where('wholesale_product', 1)->orderBy('created_at', 'desc')->get();
                    @endphp
                <div class="product-choose-list">
                    <div class="product-choose">
                        <div class="form-group row">
                            <label class="col-lg-3 control-label" for="name">{{translate('New profit rate per piece')}}</label>
                            <div class="form-group col-md-6">
                                <input type="number" class="form-control" name="new_profit" placeholder="{{ translate('New profit rate per piece') }}">
                            </div>
                            <div class="form-group col-md-3">
                                <input type="text" class="form-control" name="tax_type" value='Percent' placeholder="{{ translate('Percent') }}" readonly>
                            </div> 
                        </div>
                    </div>
                </div>   
            </div>
            <div class="mar-all text-center mb-2">
                <button class="btn btn-danger btn-md" title="{{ translate('Create new order') }}" type="submit">
                    {{translate('Submit')}}
                </button>
            </div>
        </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Create Order')}}</h5>
            </div>
            <form class="form form-horizontal mar-top" action="{{route('orders.add_order_admin')}}" method="POST">
                <div class="card-body">
                    @csrf
                    <input type="hidden" class="form-control" name="order_detail_id" value={{$orderDetail->id}} required>
                    <input type="hidden" class="form-control" name="order_id" value={{$order->id}} required>
                    <div class="form-group row">
                    <label class="col-md-3 col-from-label">{{translate('Quantity')}}</label>
                    <div class="col-md-8">
                        <input type="number" class="form-control" name="quantity" placeholder="{{ translate('Quantity') }}" required>
                    </div>
                </div>
                @php
                    $products = \App\Models\Product::where('wholesale_product', 1)->orderBy('created_at', 'desc')->get();
                    @endphp
                    {{-- {{dd($products[0]->user->name)}} --}}
                <div class="product-choose-list">
                    <div class="product-choose">
                        <div class="form-group row">
                            <label class="col-lg-3 control-label" for="name">{{translate('Product')}}</label>
                            <div class="col-lg-9">
                                <select name="product_id" class="form-control product_id aiz-selectpicker" data-live-search="true" data-selected-text-format="count" required>
                                    @foreach($products as $key => $product)
                                        <option value="{{$product->id}}">{{$product->getTranslation('name')}} -  {{$product->user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>   
            </div>
            <div class="mar-all text-center mb-2">
                <button class="btn btn-danger btn-md" title="{{ translate('Create new order') }}" type="submit">
                    {{translate('Submit')}}
                </button>
            </div>
        </form>
        </div>
        </div>  
        </div>  
           
    </div>
@endsection
