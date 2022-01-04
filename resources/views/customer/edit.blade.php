@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit User') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('customer.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value={{$id}} name="customer_id">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name',$customer->name)  }}" autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$customer->email}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>

                            <div class="col-md-6">
                                <input id="phone" value="{{ old('phone',$customer->phone) }}" type="number" class="form-control @error('phone') is-invalid @enderror" name="phone">

                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>

                            <div class="col-md-6">
                                <input id="address" type="text" value="{{ old('address',$customer->address) }}" class="form-control @error('address') is-invalid @enderror" name="address">

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
                                <input id="postcode" type="number" value="{{ old('postcode',$customer->postcode) }}" class="form-control @error('postcode') is-invalid @enderror" name="postcode">

                                @error('postcode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="state" class="col-md-4 col-form-label text-md-right">{{ __('State') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="state" name="state">
                                    @foreach ($states as $key=>$state)
                                    @if (old('state') == $state->id || $customer->state == $state->id)
                                    <option value="{{ $state->id }}" selected>{{ $state ->name }}</option>
                                    @else
                                    <option value="{{ $state->id }}">{{ $state ->name }}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="city" class="col-md-4 col-form-label text-md-right">{{ __('City') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="city" name="city">
                                    @foreach ($cities as $key=>$city)
                                    @if (old('city') == $city->id || $customer->city == $city->id)
                                    <option value="{{ $city->id }}" selected>{{ $city ->name }}</option>
                                    @else
                                    <option value="{{ $city->id }}">{{ $city ->name }}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="isIndividual" class="col-md-4 col-form-label text-md-right">{{ __('Individual or Company') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="isIndividual" name="isIndividual">
                                    @if($customer->isIndividual == 1)
                                    <option value="1" selected>Individual</option>
                                    <option value="0">Company</option>
                                    @elseif($customer->isIndividual == 0)
                                    <option value="1">Individual</option>
                                    <option value="0" selected>Company</option>
                                    @else
                                    <option value="1">Individual</option>
                                    <option value="0">Company</option>
                                    @endif
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="referral_code" class="col-md-4 col-form-label text-md-right">{{ __('Referral Code') }}</label>

                            <div class="col-md-6">
                                <input id="referral_code" type="text" value="{{ old('referral_code',$customer->referral_code) }}" class="form-control @error('referral_code') is-invalid @enderror" name="referral_code">

                                @error('referral_code')
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
        getCity();

        jQuery('#state').change(function() {
            getCity();
        });

        function getCity() {
            var customer_city = '';
            <?php if ($customer->city) ?>
            customer_city = <?php echo $customer->city ?>;
            $('#city').append('<option value="1">None</option>');
            var state_id = $('#state').find(":selected").val();
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
                    selected_city_id: customer_city,
                },
                success: function(data) {
                    $('#city').empty();
                    $('#city').append(data.html);
                },
                error: function(data) {
                    console.log(data.responseJSON.message);
                }

            });

            // console.log(pausecontent);
            // recycle[hub_id].forEach(element => {
            //     console.log(element);
            //     // $("#hubRecycle tbody").append("<tr>" +
            //     //     "<td class='lalign'>" + element. + "</td>" +
            //     //     "<td>" + $("#introdate").val() + "</td>" +
            //     //     "<td>" + $("#url").val() + "</td>" +
            //     //     "</tr>");
            // });
        };

    });
</script>
@endsection