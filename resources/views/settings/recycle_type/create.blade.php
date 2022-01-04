@extends('layouts.app')
@section('content')

<div class="container">
<div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Add New Recycle Type') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('settings.recycle_type.insert_db') }}">
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
                            <label for="recycle_category_id" class="col-md-4 col-form-label text-md-right">{{ __('Recycle Category') }}</label>

                            <div class="col-md-6">
                                <select id="recycle_category_id" name="recycle_category_id">
                                    @foreach ($recycle_categories as $recycle_category)
                                    <option value='{{$recycle_category->id}}'>{{$recycle_category->name}}</option>
                                    @endforeach
                                </select>
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