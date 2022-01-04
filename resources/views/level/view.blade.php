@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Level') }}</div>

                <div class="card-body">
                    <input type="hidden" value={{$id}} name="level_id">
                    <div class="form-group row">
                        <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$level->name}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                        <div class="col-md-6 col-form-label">
                            {!!$level->description!!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Image') }}</label>

                        <div class="col-md-6 col-form-label">
                            <img id="imagePreview" width="40%" src="<?php echo env('APP_URL') . '/nucycle-admin/images/avatar/' . $level->image ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="points_from" class="col-md-4 col-form-label text-md-right">{{ __('Points From') }}</label>

                        <div class="col-md-6 col-form-label">
                            {!!$level->points_from!!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="points_to" class="col-md-4 col-form-label text-md-right">{{ __('Points To') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$level->points_to}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="multiplier" class="col-md-4 col-form-label text-md-right">{{ __('Multiplier') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$level->multiplier}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="free_monthly_voucher" class="col-md-4 col-form-label text-md-right">{{ __('Free Monthly Voucher') }}</label>
                        
                        <div class="col-md-6 col-form-label">
                            {{$level->free_monthly_voucher}}
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('level.edit', $id) }}" class="btn btn-xs btn-success pull-right" type="button">{{ __('Edit') }}</a>

                        </div>
                    </div>
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