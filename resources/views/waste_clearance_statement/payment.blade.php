@extends('layouts.app')
@section('content')
@if (\Session::has('successMsg'))
<div class="alert alert-success">
    <ul>
        <li>{!! \Session::get('successMsg') !!}</li>
    </ul>
</div>
@endif

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Add New Waste Clearance Statement Payment') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('waste_clearance_statement.insert_payment') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="invoice_date" class="col-md-4 col-form-label text-md-right">{{ __('Invoice Date') }}</label>

                            <div class="col-md-6">
                                <input id="invoice_date" type="date" value="{{ old('invoice_date') }}" class="form-control @error('invoice_date') is-invalid @enderror" name="invoice_date" required>
                                @error('invoice_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        <p><strong>Item No</strong></p>
                                    </td>
                                    <td>
                                        <p><strong>Description</strong></p>
                                    </td>
                                    <td>
                                        <p><strong>UOM</strong></p>
                                    </td>
                                    <td>
                                        <p><strong>Qty</strong></p>
                                    </td>
                                    <td>
                                        <p><strong>Unit Price</strong></p>
                                </tr>
                                @foreach($items as $index => $item)
                                <tr class="calculate">
                                    <td>
                                        <p>{{$index + 1}}</p>
                                    </td>
                                    <td>
                                        <input type="text" name="unitname[]" value="{{$item->name}}" readonly>
                                    </td>
                                    <td>
                                        <p>Kg</p>
                                    </td>
                                    <td>
                                        <p class="qty{{$index+1}}">{{number_format((float)$item->weight, 2, '.', '')}}</p>
                                    </td>
                                    <td>
                                        <input type="number" step=".01" class="price{{$index+1}}" name="unitprice[]" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="form-group row">
                            <label for="total_price" class="col-md-4 col-form-label text-md-right">{{ __('Total Price') }}</label>

                            <div class="col-md-6">
                                <input id="total_price" type="number" value=""  step=".01" class="form-control @error('total_price') is-invalid @enderror" name="total_price" required>
                                @error('total_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="receipt_date" class="col-md-4 col-form-label text-md-right">{{ __('Receipt Date') }}</label>

                            <div class="col-md-6">
                                <input id="receipt_date" type="date" value="{{ old('receipt_date') }}" class="form-control @error('receipt_date') is-invalid @enderror" name="receipt_date" required>
                                @error('receipt_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="receipt_number" class="col-md-4 col-form-label text-md-right">{{ __('Receipt Number') }}</label>

                            <div class="col-md-6">
                                <input id="receipt_number" type="text" value="{{ old('receipt_number') }}" class="form-control @error('receipt_number') is-invalid @enderror" name="receipt_number" required>
                                @error('receipt_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="total_amount" class="col-md-4 col-form-label text-md-right">{{ __('Total Amount') }}</label>

                            <div class="col-md-6">
                                <input id="total_amount" type="number" value="{{ old('total_amount') }}" step=".01" class="form-control @error('total_amount') is-invalid @enderror" name="total_amount" required>
                                @error('total_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('File upload') }}</label>

                            <div class="col-md-6">
                                <input id="image" type="file" name="image" accept="image/*" class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}">

                                @if ($errors->has('image'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('image') }}</strong>
                                </span>
                                @endif
                                <img id="imagePreview" width="100%" src="">
                            </div>
                        </div>

                        <input type="hidden" name="statement_id" value="{{$id}}">
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success pull-right" type="submit">{{ __('Submit') }}</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    
    $(document).ready(function() {
        
        $("table").on("change", "input", function() {

            var total=0;
            for(var i = 1; i<= {{$items->count()}}; i++)
            {
                var qty_name = ".qty"+i;
                var price_val = ".price"+i;
               // $(test).html();
                var qty = $(qty_name).html();
                var price = $(price_val).val();
                var each_total = qty * price;
                console.log(qty);
                console.log(price);
                console.log(each_total);
                total += each_total;
            }
            $('#total_price').val(total);
            
        });

    });
</script>
@endsection