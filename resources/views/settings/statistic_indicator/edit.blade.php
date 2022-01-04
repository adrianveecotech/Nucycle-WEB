@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Statistic Indicator') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('settings.statistic_indicator.edit_db') }}">
                        @csrf
                        <input type="hidden" value="{{ $id }}" name="id">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') == '' ? $indicator->name : old('name') }}" autofocus>

                                @error('name')
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
