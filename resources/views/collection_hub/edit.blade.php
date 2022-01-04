@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Collection Hub') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('collection_hub.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ $id }}" name="hub_id">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $hub->hub_name }}" autofocus>

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
                                <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ $hub->hub_address }}">

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
                                <input id="postcode" type="text" class="form-control @error('postcode') is-invalid @enderror" name="postcode" value="{{ $hub->hub_postcode }}">

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
                                    @if($state->id == $hub->hub_state_id)
                                    <option value='{{$state->id}}' selected>{{$state->name}}</option>
                                    @else
                                    <option value='{{$state->id}}'>{{$state->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <Button type="button" id="buttonGetCoordinates" class="btn btn-xs btn-success pull-right">{{ __('Get Coordinates') }}</Button>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="city_id" class="col-md-4 col-form-label text-md-right">{{ __('City') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="city_id" name="city_id">
                                    @foreach ($cities as $key=>$city)
                                    @if (old('city_id') == $city->id || $hub->hub_city_id == $city->id)
                                    <option value="{{ $city->id }}" selected>{{ $city ->name }}</option>
                                    @else
                                    <option value="{{ $city->id }}">{{ $city ->name }}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="latitude" class="col-md-4 col-form-label text-md-right">{{ __('Latitude') }}</label>

                            <div class="col-md-6">
                                <input id="latitude" type="text" class="form-control @error('latitude') is-invalid @enderror" value="{{ $hub->latitude }}" name="latitude">

                                @error('latitude')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="longitude" class="col-md-4 col-form-label text-md-right">{{ __('Longitude') }}</label>

                            <div class="col-md-6">
                                <input id="longitude" type="text" class="form-control @error('longitude') is-invalid @enderror" value="{{ $hub->longitude }}" name="longitude">

                                @error('longitude')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="contact_number" class="col-md-4 col-form-label text-md-right">{{ __('Contact Number') }}</label>

                            <div class="col-md-6">
                                <input id="contact_number" type="text" class="form-control @error('contact_number') is-invalid @enderror" name="contact_number" value="{{ $hub->contact_number }}">

                                @error('contact_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="operating_day" class="col-md-4 col-form-label text-md-right">{{ __('Operating Days') }}</label>

                            <div class="col-md-6">
                                <input id="operating_day" class="form-control @error('operating_day') is-invalid @enderror" name="operating_day" value="{{ $hub->operating_day }}">

                                @error('operating_day')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="operating_hour" class="col-md-4 col-form-label text-md-right">{{ __('Operating Hours') }}</label>
                            <?php $operating_hour_start = explode(' - ', $hub->operating_hours)[0];
                            $operating_hour_end = explode(' - ', $hub->operating_hours)[1]; ?>
                            <div class="col-md-6">
                                <input id="operating_hour_start" type="time" class="form-control @error('operating_hour_start') is-invalid @enderror" name="operating_hour_start" value="{{ $operating_hour_start }}">
                                @error('operating_hour_start')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                to <input id="operating_hour_end" type="time" class="form-control @error('operating_hour_end') is-invalid @enderror" name="operating_hour_end" value="{{ $operating_hour_end }}">
                                @error('operating_hour_end')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('Type') }}</label>

                            <div class="col-md-6">
                                <select id="type" name="type">
                                    @if($hub->type == 0)
                                    {
                                    <option value='0' selected>Station</option>
                                    <option value='1'>Mobile</option>
                                    }
                                    @else if ($hub->type == 1)
                                    {
                                    <option value='0'>Station</option>
                                    <option value='1' selected>Mobile</option>
                                    }
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Active') }}</label>

                            <div class="col-md-6">
                                @if($hub->is_active == 1)
                                <input type="checkbox" id="active_status" name="active_status" value="active" checked>
                                @else
                                <input type="checkbox" id="active_status" name="active_status" value="active">
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="read_only" class="col-md-4 col-form-label text-md-right">{{ __('Read Only') }}</label>

                            <div class="col-md-6">
                                @if($hub->read_only == 1)
                                <input type="checkbox" id="read_only" name="read_only" value="readonly" checked>
                                @else
                                <input type="checkbox" id="read_only" name="read_only" value="readonly">
                                @endif
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
                                <img id="imagePreview" width="200" src="<?php echo env('APP_URL') . '/nucycle-admin/images/hub_logo/' . $hub->image ?>">
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


        getCity();

        jQuery('#state_id').change(function() {
            getCity();
        });

        function getCity() {
            var hub_city = '';
            <?php if ($hub->hub_city_id) ?>
            hub_city = <?php echo $hub->hub_city_id ?>;
            $('#city_id').append('<option value="1">None</option>');
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
                    selected_city_id: hub_city,
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

        $('#buttonGetCoordinates').on('click', function() {
            var address = $('#address').val() + ',' + $('#postcode').val() + ',' + $('#state option:selected').text();
            $.ajax({
                type: "POST",
                url: 'https://app.nucycle.com.my/nucycle-admin/get_coordinates.php',
                data: {
                    'action': 'get_coordinates',
                    'address': address,
                    'gakey': 'AIzaSyDoBGhfkwBZ_XpOFb9YhAaWdNwVn5en79A'
                },
                success: function(html) {
                    html = (JSON.parse(html));
                    $('#latitude').val(html.split(',')[0]);
                    $('#longitude').val(html.split(',')[1]);
                }
            });
        });
    });
</script>
@endsection