@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Collection Hub Collector') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('collection_hub_collector.edit_db') }}">
                        @csrf
                        <input type="hidden" value="{{ $id }}" name="user_id">
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input disabled id="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ $collector->email}}" name="email" required autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $collector->name}}" name="name" required>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="hub" class="col-md-4 col-form-label text-md-right">{{ __('Collection Hub') }}</label>

                            <div class="col-md-6">
                                <select id="hub" name="hub">
                                    @if(in_array(1, Auth::user()->users_roles_id()))
                                        @foreach ($hubs as $hub)
                                            @if($hub->id == $collector->collection_hub_id)
                                                <option value='{{$hub->id}}' selected>{{$hub->hub_name}}</option>
                                            @else
                                                <option value='{{$hub->id}}'>{{$hub->hub_name}}</option>
                                            @endif
                                        @endforeach
                                    @elseif(in_array(4, Auth::user()->users_roles_id()))
                                        @foreach ($hubs as $hub)
                                            @if($hub->id == $collector->collection_hub_id)
                                                <option value='{{$hub->id}}' selected>{{$hub->hub_name}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
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