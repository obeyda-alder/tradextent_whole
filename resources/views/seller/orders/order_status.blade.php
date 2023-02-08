@extends('seller.layouts.app')

@section('panel_content')

    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">{{ translate('Order Status') }}</h1>
        </div>

        <div class="card-body">
            <div class="row gutters-5 mb-2">
                @php
                    // $delivery_status = $order->delivery_status;
                    $seller_status = $orderDetail->seller_status;
                    // $payment_status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->payment_status;
                @endphp
                <div class="col-md-3">
                    <label for="update_seller_order_status">{{ translate('Order Status') }}</label>
                    @if ($seller_status == 'pending')
                        <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                            id="update_seller_order_status">
                            <option value="pending" @if ($seller_status == 'pending') selected @endif>
                                {{ translate('Pending') }}</option>
                            <option value="accepted" @if ($seller_status == 'accepted') selected @endif>
                                {{ translate('Accepted') }}</option>
                            <option value="accepted with note" @if ($seller_status == 'accepted with note') selected @endif>
                                {{ translate('Accepted with note') }}</option>
                            <option value="cancelled" @if ($seller_status == 'cancelled') selected @endif>
                                {{ translate('Cancelled') }}</option>
                        </select>
                    @else
                        <input type="text" class="form-control" value="{{ $seller_status }}" disabled>
                    @endif
                </div>
               
            </div>
            <div class="row gutters-5 mt-2">
            </div>
            <div class="row gutters-5 mt-2">
                <div class="col text-md-left">
                    @if($seller_status == 'accepted with note')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Order Note')}}</h5>
                        </div>
                        <div class="card-body">
                        <form class="form form-horizontal mar-top" action="{{route('seller.orders.seller_order_note')}}" method="POST">
                            @csrf
                            <input type="hidden" class="form-control" name="order_detail_id" value={{$orderDetail->id}}>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Total quantity')}}</label>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" name="note_quantity" value="{{ $orderDetail->note_quantity }}" placeholder="{{ translate('Total quantity') }}" required {{$orderDetail->note_quantity ? 'readonly' : ''}}>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Unit Price')}}</label>
                                <div class="col-md-6">
                                    <input type="number" lang="en" min="0" step="0.01" class="form-control" name="note_price" value="{{ $orderDetail->note_price }}" placeholder="{{ translate('Unit Price') }}" required {{$orderDetail->note_price ? 'readonly' : ''}}>
                                </div>
                                <span class="col-md-3" style="align-self: center;">
                                    {{get_system_default_currency()->name}}
                                    ({{get_system_default_currency()->symbol}})
                                </span>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Shipping Days')}}</label>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" name="note_shipping_dayes" placeholder="{{ translate('Shipping Days') }}" value="{{ $orderDetail->note_shipping_dayes }}" required {{$orderDetail->note_shipping_dayes ? 'readonly' : ''}}>
                                </div>
                            </div>
                            <div class="mar-all text-center mb-2">
                                <button type="submit" name="button" class="btn btn-success action-btn">{{ translate('Submit') }}</button>
                            </div>
                        </form>
                        </div>
                    </div>
                    @endif
                    
                </div>
               
            </div>

            <hr class="new-section-sm bord-no">
            
      

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        $('#update_seller_order_status').on('change', function() {
            var order_id = {{ $orderDetail->id }};
            var status = $('#update_seller_order_status').val();
            $.post('{{ route('seller.orders.update_seller_order_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                console.log(data);
                AIZ.plugins.notify('success', '{{ translate('Order status has been updated') }}');
                location.reload().setTimeOut(500);
            });
        });
    </script>
@endsection
