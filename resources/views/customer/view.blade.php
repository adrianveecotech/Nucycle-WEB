@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View User') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('customer.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value={{$id}} name="user_id">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$customer->name}}
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
                            <div class="col-md-6 col-form-label">
                                {{$customer->phone}}
                            </div>
                        </div>
  
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Profile Picture') }}</label>
                            <div class="col-md-6 col-form-label">
                                <img id="imagePreview" width="40%" src="{{URL::to('/').'/nucycle-customer/images/profile_picture/'.$customer->profile_picture}}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$customer->address}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="postcode" class="col-md-4 col-form-label text-md-right">{{ __('Postcode') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$customer->postcode}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="city" class="col-md-4 col-form-label text-md-right">{{ __('City') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$customer->city}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="state" class="col-md-4 col-form-label text-md-right">{{ __('State') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$customer->state}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="isIndividual" class="col-md-4 col-form-label text-md-right">{{ __('Individual or Company') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$customer->isIndividual == 1 ? 'Individual' : 'Company'}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="referral_code" class="col-md-4 col-form-label text-md-right">{{ __('Referral code') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$customer->referral_code}}
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-xs btn-success pull-right" type="button">{{ __('Edit') }}</a>

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
</script>
@endsection