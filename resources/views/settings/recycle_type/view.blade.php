@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Recycle Type') }}</div>

                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$recycle_type->name }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="wheelie_bins" class="col-md-4 col-form-label text-md-right">{{ __('Recycle Category') }}</label>

                        <div class="col-md-6 col-form-label">
                            <label>{{$recycle_type->recycle_category->name}}</label>
                        </div>
                    </div>


                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{route('settings.recycle_type.edit', ['id' => $recycle_type->id])}}" class="btn btn-xs btn-success">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection