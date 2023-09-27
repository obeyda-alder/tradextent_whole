@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card" style="background-color: transparent;box-shadow:none;border:none">
        <div class="card-header mb-3" style="background-color: #fff;">
            <h5 class="mb-0 h6">{{ translate('Purchase History') }}</h5>
        </div>
        @if (count($orders) > 0)
        @foreach ($orders as $key => $order)
        <div class="card-body mb-3" style="background-color: #fff;
        ">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th style="padding:1rem .40rem">{{ translate('Code')}}</th>
                            <th data-breakpoints="md" style="padding:1rem .40rem">{{ translate('Date')}}</th>
                            <th data-breakpoints="md" style="padding:1rem .40rem">{{ translate('Products')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th data-breakpoints="md" style="padding:1rem .40rem">{{ translate('Delivery Status')}}</th>
                            <th data-breakpoints="md" style="padding:1rem .40rem">{{ translate('Payment Status')}}</th>
                            <th class="text-right" style="padding:1rem .40rem">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                            @if (count($order->orderDetails) > 0)
                                <tr>
                                    <td style="padding:1rem .40rem">
                                        <a href="{{route('purchase_history.details', encrypt($order->id))}}">{{ $order->code }}</a>
                                    </td>
                                    <td style="padding:1rem .40rem">{{ date('d-m-Y', $order->date) }}</td>
                                    <td style="padding:1rem .40rem">{{ count($order->orderDetails) }}</td>
                                    <td style="padding:1rem .40rem">
                                        {{ single_price($order->grand_total) }}
                                    </td>
                                    <td style="padding:1rem .40rem">
                                        {{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}
                                        @if($order->delivery_viewed == 0)
                                            <span class="ml-2" style="color:green"><strong>*</strong></span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem .40rem">
                                        @if ($order->payment_status == 'paid')
                                            <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                                        @else
                                            <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                                        @endif
                                        @if($order->payment_status_viewed == 0)
                                            <span class="ml-2" style="color:green"><strong>*</strong></span>
                                        @endif
                                    </td>
                                    <td class="text-right" style="padding:1rem .40rem">
                                        @if ($order->delivery_status == 'pending' && $order->payment_status == 'unpaid')
                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('purchase_history.destroy', $order->id)}}" title="{{ translate('Cancel') }}">
                                               <i class="las la-trash"></i>
                                           </a>
                                        @endif
                                        <a href="{{route('purchase_history.details', encrypt($order->id))}}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Order Details') }}">
                                            <i class="las la-eye"></i>
                                        </a>
                                        <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                            <i class="las la-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                    
                                
                            @endif
                        </tbody>
                    </table>
                    <hr>
                    @foreach ($order->orderDetails as $key => $orderDetail)
                        <div class="mb-1" style="display: flex;
                        align-items: center;
                        flex-direction: row;    justify-content: space-between;">
                            @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                            <span style="flex:2">{{ translate('Product Name') }}: 
                                <a href="{{ route('product', $orderDetail->product->slug) }}"
                                    target="_blank"> {{ $orderDetail->product->getTranslation('name') }}</a>
                            </span>
                            <span style="flex:1">{{ $orderDetail->quantity }} {{ translate('Items') }}</span>
                            <span class="avatar avatar-md m-1" style="border-radius:10%;">
                                <img src={{ uploaded_asset($orderDetail->product->thumbnail_img) }} class="image" style="border-radius:10%">
                            </span>
                            
                            @else
                                <strong>{{ translate('Product Unavailable') }}</strong>
                            @endif
                        </div>
                        <hr>
                        @endforeach
                </div>
                    @endforeach
                    <div class="card-body">
                <div class="aiz-pagination">
                    {{ $orders->links() }}
              	</div>
              	</div>
        @endif
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')

    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        $('#order_details').on('hidden.bs.modal', function () {
            location.reload();
        })
    </script>

@endsection
