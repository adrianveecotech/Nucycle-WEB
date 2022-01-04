@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Add New Merchant') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('settings.merchant.insert_db') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" name="name" autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>

                            <div class="col-md-6">
                                <input id="address" type="text" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" name="address" autofocus>

                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="postcode" class="col-md-4 col-form-label text-md-right">{{ __('Postcode') }}</label>

                            <div class="col-md-6">
                                <input id="postcode" type="numeric" value="{{ old('postcode') }}" class="form-control @error('postcode') is-invalid @enderror" name="postcode" autofocus>

                                @error('postcode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="state_id" class="col-md-4 col-form-label text-md-right">{{ __('State') }}</label>

                            <div class="col-md-6">
                                <select id="state_id" name="state_id">
                                    @foreach ($states as $state)
                                    <option value='{{$state->id}}'>{{$state->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="city_id" class="col-md-4 col-form-label text-md-right">{{ __('City') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="city_id" name="city_id">
                                    @foreach ($cities as $key=>$city)
                                    @if (old('city_id') == $city->id )
                                    <option value="{{ $city->id }}" selected>{{ $city ->name }}</option>
                                    @else
                                    <option value="{{ $city->id }}">{{ $city ->name }}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone_number" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>

                            <div class="col-md-6">
                                <input id="phone_number"  value="{{ old('phone_number') }}" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" autofocus>

                                @error('phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" name="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="url" class="col-md-4 col-form-label text-md-right">{{ __('URL') }}</label>

                            <div class="col-md-6">
                                <input id="url" type="text" value="{{ old('url') }}" class="form-control @error('url') is-invalid @enderror" name="url" autofocus>

                                @error('url')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Active') }}</label>

                            <div class="col-md-6">
                                <input type="checkbox" id="active_status" name="active_status" value="active">
                            </div>
                        </div>
                        <br>
                        <div class="col-md-4 col-form-label text-md-right">
                            <h5>Report settings</h5>
                        </div>
                        <div class="form-group row">
                            <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Basic Report') }}</label>
                            <div class="col-md-6 col-form-label">
                                <label class="switch">
                                    <input type="checkbox" id="basic_report" name="basic_report" value="active">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Ads Campaign Report') }}</label>
                            <div class="col-md-6 col-form-label">
                                <label class="switch">
                                    <input type="checkbox" id="ads_report" name="ads_report" value="active">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Subscription Report') }}</label>
                            <div class="col-md-6 col-form-label">
                                <label class="switch">
                                    <input type="checkbox" id="subscription_report" name="subscription_report" value="active">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success pull-right" type="submit">{{ __('Create') }}</button>

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
        getCity();

        jQuery('#state_id').change(function() {
            getCity();
        });

        function getCity() {
            var merchant_city = '';
            var state_id = $('#state_id').find(":selected").val();
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: '{{ route("get_city_by_state") }}',
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    state_id: state_id,
                    selected_city_id: merchant_city,
                },
                success: function(data) {
                    $('#city_id').empty();
                    $('#city_id').append(data.html);
                },
                error: function(data) {
                    console.log(data.responseJSON.message);
                }

            });
        };

    });
</script>
@endsection