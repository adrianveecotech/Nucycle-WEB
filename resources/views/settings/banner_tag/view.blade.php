@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Banner Tag') }}</div>

                <div class="card-body">
                    <input type="hidden" value="{{ $id }}" name="id">
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                        <div class="col-md-6  col-form-label ">
                            {{ $banner_tag->name }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Active') }}</label>

                        <div class="col-md-6  col-form-label ">
                            {{ $banner_tag-> is_active == 1 ? 'Active' : 'Inactive' }}
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{route('settings.banner_tag.edit', ['id' => $banner_tag->id])}}" class="btn btn-xs btn-success pull-right">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection