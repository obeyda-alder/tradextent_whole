

{{-- <div class="card">
	<div class="card-header">
		<h5 class="mb-0 h6">{{translate('VAT & Tax')}}</h5>
	</div>
	<div class="card-body">
		@foreach(\App\Models\Tax::where('tax_status', 1)->where('id','!=', 5)->get() as $tax)
		<label for="name">
			{{$tax->name}}
			<input type="hidden" value="{{$tax->id}}" name="tax_id[]">
		</label>

		<div class="form-row">
			<div class="form-group col-md-6">
				<input type="number" lang="en" min="0" value="0" step="0.01"
					placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control" required>
			</div>
			<div class="form-group col-md-6">
				<select class="form-control aiz-selectpicker" name="tax_type[]">
					<option value="amount">{{translate('Flat')}}</option>
					<option value="percent">{{translate('Percent')}}</option>
				</select>
			</div>
		</div>
		@endforeach
	</div>
</div>





 --}}



<div class="card">
	<div class="card-header">
		<h5 class="mb-0 h6">{{translate('VAT & Tax')}}</h5>
	</div>
	<div class="card-body">
		@foreach($cat_taxs as $tax)
		<input type="hidden" value="{{$tax->tax_id}}" name="tax_id[]">
		@if($tax->tax_id != 5)
		<label for="name">
			@if($tax->tax_rec != null)
			{{$tax->tax_rec->name}}
			@endif
		</label>
		<div class="form-row">
			<div class="form-group col-md-6">
				<input type="number" lang="en" min="0" value="{{$tax->tax}}" step="0.01" placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control" required>
			</div>
			<div class="form-group col-md-6">
				<select class="form-control aiz-selectpicker" name="tax_type[]">
					<option value="amount" {{$tax->tax_type == "amount" ? 'selected':''}}>{{translate('Flat')}}</option>
					<option value="percent" {{$tax->tax_type == "percent" ? 'selected':''}}>{{translate('Percent')}}</option>
				</select>
			</div>
		</div>
		@else
		<div class="form-row d-none">
			<div class="form-group col-md-6">
				<input type="number" lang="en" min="0" value="{{$tax->tax}}" step="0.01" placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control" required>
			</div>
			<div class="form-group col-md-6">
				<select class="form-control aiz-selectpicker" name="tax_type[]">
					<option value="percent" selected>{{translate('Percent')}}</option>
				</select>
			</div>
		</div>
		
		@endif
		@endforeach
	</div>
</div> 