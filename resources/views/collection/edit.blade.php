@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Collection') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('collection.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value={{$id}} name="collection_id">
                        <div class="form-group row">
                            <label for="customer" class="col-md-4 col-form-label text-md-right">{{ __('Customer') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="customer" name="customer">
                                    @foreach ($customers as $key=>$customer)
                                    @if (old('customer') == $customer->id || $collection->customer_id == $customer->id)
                                    <option value="{{ $customer->id }}" selected>{{ $customer ->name }}</option>
                                    @else
                                    <option value="{{ $customer->id }}">{{ $customer ->name }}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="collector" class="col-md-4 col-form-label text-md-right">{{ __('Customer') }}</label>
                            <div class="col-md-6 col-form-label">
                                <select id="collector" name="collector">
                                    @foreach ($collectors as $key=>$collector)
                                    @if (old('collector') == $collector->id || $collection->collector_id == $collector->id)
                                    <option value="{{ $collector->id }}" selected>{{ $collector ->name }}</option>
                                    @else
                                    <option value="{{ $collector->id }}">{{ $collector ->name }}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="hub" class="col-md-4 col-form-label text-md-right">{{ __('Collection Hub') }}</label>
                            <div class="col-md-6 col-form-label">
                                @if(in_array(1, Auth::user()->users_roles_id()))
                                <select id="hub" name="hub">
                                    @foreach ($hubs as $key=>$hub)
                                    @if (old('hub') == $hub->id || $collection->collection_hub_id == $hub->id)
                                    <option value="{{ $hub->id }}" selected>{{ $hub ->hub_name }}</option>
                                    @else
                                    <option value="{{ $hub->id }}">{{ $hub ->hub_name }}</option>
                                    @endif

                                    @endforeach
                                </select>
                                @endif
                                @if(in_array(4, Auth::user()->users_roles_id()))
                                <div class="col-md-6 ">
                                    {{$collection->collection_hub->hub_name}}
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="total_point" class="col-md-4 col-form-label text-md-right">{{ __('Item Point') }}</label>

                            <div class="col-md-6">
                                <input id="total_point" type="number" value="{{ old('total_point') ? old('total_point') : $collection->total_point }}" class="form-control @error('total_point') is-invalid @enderror" name="total_point">

                                @error('total_point')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bonus_point" class="col-md-4 col-form-label text-md-right">{{ __('Bonus Point') }}</label>

                            <div class="col-md-6">
                                <input id="bonus_point" type="number" value="{{ old('bonus_point')? old('bonus_point') : $collection->bonus_point }}" class="form-control @error('bonus_point') is-invalid @enderror" name="bonus_point">

                                @error('bonus_point')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="all_point" class="col-md-4 col-form-label text-md-right">{{ __('All Point') }}</label>

                            <div class="col-md-6">
                                <input id="all_point" type="number" value="{{ old('all_point') ?  old('all_point') : $collection->all_point }}" class="form-control @error('all_point') is-invalid @enderror" name="all_point">

                                @error('all_point')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="total_weight" class="col-md-4 col-form-label text-md-right">{{ __('Total Weight (kg)') }}</label>

                            <div class="col-md-6">
                                <input id="total_weight" type="text" value="{{ old('total_weight') ? old('total_weight') : $collection->total_weight }}" class="form-control @error('total_weight') is-invalid @enderror" name="total_weight">

                                @error('total_weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        @foreach($collection->collection_detail as $item)
                        <div class="form-group row mt-5">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Item') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$item->recycle_type->name}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="total_weight" class="col-md-4 col-form-label text-md-right">{{ __('Weight (kg)') }}</label>

                            <div class="col-md-6">
                                <input id="total_weight" type="text" value="{{ old('total_weight') ? old('total_weight') : $item->weight }}" class="form-control @error('total_weight') is-invalid @enderror" name="total_weight">

                                @error('total_weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="total_weight" class="col-md-4 col-form-label text-md-right">{{ __('Point') }}</label>

                            <div class="col-md-6">
                                <input id="total_weight" type="text" value="{{ old('total_weight') ? old('total_weight') : $item->weight }}" class="form-control @error('total_weight') is-invalid @enderror" name="total_weight">

                                @error('total_weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        @endforeach

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
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            } else {
                $('#imagePreview').attr('src', '');
            }
        }

        $("#image").change(function() {
            readURL(this);
        });
    });
</script>
@endsection