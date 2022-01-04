@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Collection Hub Bin') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('collection_hub_bin.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value={{$id}} name="bin_id">
                        <div class="form-group row">
                            <label for="hub" class="col-md-4 col-form-label text-md-right">{{ __('Collection Hub') }}</label>

                            <div class="col-md-6 col-form-label">
                                {!!$bin->collection_hub->hub_name!!}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="item" class="col-md-4 col-form-label text-md-right">{{ __('Recycle Item') }}</label>

                            <div class="col-md-6 col-form-label">
                                {!!$bin->recycle_type->name!!}
                            </div>
                        </div>

                        @if(in_array(1, Auth::user()->users_roles_id()))
                        <div class="form-group row">
                            <label for="capacity_weight" class="col-md-4 col-form-label text-md-right">{{ __('Capacity Weight') }}</label>

                            <div class="col-md-6">
                                <input type="number" id="capacity_weight" step="0.01" class="form-control @error('capacity_weight') is-invalid @enderror" name="capacity_weight" value="{{ old('capacity_weight') ?  old('capacity_weight') : $bin->capacity_weight  }}">

                                @error('capacity_weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        @elseif(in_array(4, Auth::user()->users_roles_id()))
                        <div class="form-group row">
                            <label for="item" class="col-md-4 col-form-label text-md-right">{{ __('Capacity Weight') }}</label>

                            <div class="col-md-6 col-form-label">
                                {!!$bin->capacity_weight!!}
                            </div>
                        </div>

                        @endif
                        <div class="form-group row">
                            <label for="current_weight" class="col-md-4 col-form-label text-md-right">{{ __('Current Weight') }}</label>

                            <div class="col-md-6">
                                <input type="number" id="current_weight" step="0.01" class="form-control @error('current_weight') is-invalid @enderror" name="current_weight" value="{{ old('current_weight') ?  old('current_weight') : $bin->current_weight  }}">

                                @error('current_weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

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

    });
</script>
@endsection