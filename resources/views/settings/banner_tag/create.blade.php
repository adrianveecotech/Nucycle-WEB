@extends('layouts.app')
@section('content')

<div class="container">
<div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Add New Banner Tag') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('settings.banner_tag.insert_db') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" value = "{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" name="name" autofocus>

                                @error('name')
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

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            <button class="btn btn-xs btn-success pull-right"  type="submit">{{ __('Create') }}</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection