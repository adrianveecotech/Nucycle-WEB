@extends('layouts.auth.default')
@section('content')
<div class="card-body login-card-body">
    <div class="card-body login-card-body">
        <p class="login-box-msg">{{__('Login')}}</p>

        <form method="POST" action="{{ route('login') }}">
            {!! csrf_field() !!}

            <div class="input-group mb-3">
                <input value="{{ old('email') }}" type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="{{ __('Email') }}" aria-label="{{ __('Email') }}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                </div>
                @if ($errors->has('email'))
                <div class="invalid-feedback">
                    {{ $errors->first('email') }}
                </div>
                @endif
            </div>

            <div class="input-group mb-3">
                <input value="{{ old('password') }}" type="password" class="form-control  {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{__('Password')}}" aria-label="{{__('Password')}}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                @if ($errors->has('password'))
                <div class="invalid-feedback">
                    {{ $errors->first('password') }}
                </div>
                @endif
            </div>

            <div class="row mb-2">
                <div class="col-8">
                    <div class="checkbox icheck">
                        <label> <input type="checkbox" name="remember"> {{__('Remember Me')}}
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">{{__('Login')}}</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <!-- <p class="mb-1 text-center">
            <a href="{{ url('/password/reset') }}">{{__('Forgot Password')}}</a>
        </p> -->
        <!-- <p class="mb-0 text-center">
            <a href="{{ url('/register') }}" class="text-center">{{__('auth.register_new_member')}}</a>
        </p> -->
    </div>
</div>
@endsection