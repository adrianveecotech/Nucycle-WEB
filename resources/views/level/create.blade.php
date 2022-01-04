@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Level') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('level.insert_db') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name')  }}" autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                            <div class="col-md-6">
                                <textarea rows="10" cols="70" class="content" id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description') }}</textarea>

                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Image') }}</label>

                            <div class="col-md-6">
                                <input id="image" type="file" name="image" accept="image/*" class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}">

                                @if ($errors->has('image'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('image') }}</strong>
                                </span>
                                @endif

                                @if (Session::has('image'))
                                <div class="alert alert-error">
                                    {{ Session::get('image')}}
                                </div>
                                @endif

                                <img id="imagePreview" width="40%" src="">

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="points_from" class="col-md-4 col-form-label text-md-right">{{ __('Points From') }}</label>

                            <div class="col-md-6">
                                <input id="points_from" value="{{ old('points_from') }}" type="text" class="form-control @error('points_from') is-invalid @enderror" name="points_from">

                                @error('points_from')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="points_to" class="col-md-4 col-form-label text-md-right">{{ __('Points To') }}</label>

                            <div class="col-md-6">
                                <input id="points_to" type="text" value="{{ old('points_to') }}" class="form-control @error('points_to') is-invalid @enderror" name="points_to">

                                @error('points_to')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="multiplier" class="col-md-4 col-form-label text-md-right">{{ __('Multiplier') }}</label>

                            <div class="col-md-6">
                                <input id="multiplier" type="text" value="{{ old('multiplier') }}" class="form-control @error('multiplier') is-invalid @enderror" name="multiplier">

                                @error('multiplier')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="free_monthly_voucher" class="col-md-4 col-form-label text-md-right">{{ __('Free Monthly Voucher') }}</label>

                            <div class="col-md-6">
                                <input id="free_monthly_voucher" type="text" value="{{ old('free_monthly_voucher') }}" class="form-control @error('free_monthly_voucher') is-invalid @enderror" name="free_monthly_voucher">

                                @error('free_monthly_voucher')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success" type="submit">{{ __('Submit') }}</button>

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