@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Merchant') }}</div>

                <div class="card-body">
                    <input type="hidden" value="{{ $id }}" name="id">
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{ $merchant->name }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{ $merchant->address }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="postcode" class="col-md-4 col-form-label text-md-right">{{ __('Postcode') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{ $merchant->postcode }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="state" class="col-md-4 col-form-label text-md-right">{{ __('State') }}</label>
                        <div class="col-md-6 col-form-label">
                            @foreach ($states as $state)
                            @if($state->id == $merchant->state_id)
                            {{$state->name}}
                            @endif
                            @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="phone_number" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{ $merchant->phone_number }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="url" class="col-md-4 col-form-label text-md-right">{{ __('URL') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{ $merchant->url }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Active') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$merchant->is_active == 1 ? 'Active' : 'Inactive'}}
                        </div>
                    </div>

                    <h5>Report settings</h5>
                    <div class="form-group row">
                        <label for="basic_report" class="col-md-2 col-form-label text-md-left">{{ __('Basic Report') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$merchant->basic_report == 1 ? 'On' : 'Off'}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="ads_report" class="col-md-2 col-form-label text-md-left">{{ __('Ads Campaign Report') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$merchant->ads_report == 1 ? 'On' : 'Off'}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="subscription_report" class="col-md-2 col-form-label text-md-left">{{ __('Subscription Report') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$merchant->subscription_report == 1 ? 'On' : 'Off'}}
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{route('settings.merchant.edit', ['id' => $merchant->id])}}" class="btn btn-xs btn-success">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection