@extends('backend.layouts.app')

@section('content')


<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Category Edit TAX Information')}}</h5> 
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                
                <form class="p-4" action="{{ route('categories.update-tax', $category->id) }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    
                    <div class="card shadow-none" id="vat-tax">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('VAT & Tax')}}</h5>
                        </div>
                        <div class="card-body">
                            @foreach(\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                            <label for="name">
                                {{$tax->name}}
                                <input type="hidden" value="{{$tax->id}}" name="tax_id[]">
                            </label>
    
                            @php
                            $tax_amount = 0;
                            $tax_type = '';
                            foreach($tax->category_taxes as $row) {
                                if($category->id == $row->category_id) {
                                    $tax_amount = $row->tax;
                                    $tax_type = $row->tax_type;
                                }
                            }
                            @endphp
    
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <input type="number" lang="en" min="0" value="{{ $tax_amount }}" step="0.01" placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control" required>
                                </div>
                                @if($tax->id != 5)
                                <div class="form-group col-md-6">
                                    <select class="form-control aiz-selectpicker" name="tax_type[]">
                                        <option value="amount" @if($tax_type == 'amount') selected @endif>
                                            {{translate('Flat')}}
                                        </option>
                                        <option value="percent" @if($tax_type == 'percent') selected @endif>
                                            {{translate('Percent')}}
                                        </option>
                                    </select>
                                </div>
                                @else
                                <div class="form-group col-md-6">
                                    <select class="form-control aiz-selectpicker" name="tax_type[]">
                                        <option value="percent" selected>
                                            {{translate('Percent')}}
                                        </option>
                                    </select>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

