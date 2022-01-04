@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Collection Hub') }}</div>

                <div class="card-body">
                    <input type="hidden" value="{{ $id }}" name="hub_id">
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$hub->hub_name}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$hub->hub_address}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="postcode" class="col-md-4 col-form-label text-md-right">{{ __('Postcode') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$hub->hub_postcode}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="state" class="col-md-4 col-form-label text-md-right">{{ __('State') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$hub->state->name}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="state" class="col-md-4 col-form-label text-md-right">{{ __('City') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$hub->city->name}}
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="latitude" class="col-md-4 col-form-label text-md-right">{{ __('Latitude') }}</label>


                        <div class="col-md-6 col-form-label">
                            {{$hub->latitude}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="longitude" class="col-md-4 col-form-label text-md-right">{{ __('Longitude') }}</label>


                        <div class="col-md-6 col-form-label">
                            {{$hub->longitude}}
                        </div>
                    </div>



                    <div class="form-group row">
                        <label for="contact_number" class="col-md-4 col-form-label text-md-right">{{ __('Contact Number') }}</label>


                        <div class="col-md-6 col-form-label">
                            {{$hub->contact_number}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="operating_hour" class="col-md-4 col-form-label text-md-right">{{ __('Operating Hours') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$hub->operating_hours}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Active') }}</label>


                        <div class="col-md-6 col-form-label">
                            {{$hub->is_active == 1 ? 'Active' : 'Inactive'}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Logo') }}</label>

                        <div class="col-md-6">
                            <img id="imagePreview" width="200" src="<?php echo env('APP_URL') . '/nucycle-admin/images/hub_logo/' . $hub->image ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('collection_hub.edit', $hub->id) }}" class="btn btn-xs btn-success pull-right" type="button">{{ __('Edit') }}</a>

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

</script>
@endsection