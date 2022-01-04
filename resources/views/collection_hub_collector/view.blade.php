@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Collection Hub Collector') }}</div>

                <div class="card-body">
                    <input type="hidden" value="{{ $id }}" name="user_id">
                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{ $collector->email}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{ $collector->name}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="hub" class="col-md-4 col-form-label text-md-right">{{ __('Collection Hub') }}</label>

                        <div class="col-md-6 col-form-label">
                            @foreach ($hubs as $hub)
                            @if($hub->id == $collector->collection_hub_id)
                            {{$hub->hub_name}}
                            @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('collection_hub_collector.edit', $id) }}" class="btn btn-xs btn-success pull-right" type="button">{{ __('Edit') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection