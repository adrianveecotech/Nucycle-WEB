@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Recycle Category') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('settings.recycle_category.edit_db') }}">
                        @csrf
                        <input type="hidden" value="{{ $id }}" name="id">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" value="{{ old('name') ? old('name') : $recycle_category->name }}" class="form-control @error('name') is-invalid @enderror" name="name" autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        @foreach ($recycle_category->recycle_category_statistic_indicator as $value)
                        <div class="form-group row">
                            <label for="value" class="col-md-4 col-form-label text-md-right"> {{$value->statistic_indicator->name}}</label>

                            <div class="col-md-6">
                                <input id="{{$value->statistic_indicator->id}}" type="number" step="0.000001" value="{{ old($value->statistic_indicator->id) ? old($value->statistic_indicator->id) : $value->value }}" class="form-control @error('value') is-invalid @enderror" name="{{'id' . $value->statistic_indicator->id}}" autofocus>

                                @error('id'.$value->statistic_indicator->id)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        @endforeach

                        <!-- <div class="form-group row">
                            <label for="cars_removed" class="col-md-4 col-form-label text-md-right">{{ __('Cars Removed') }}</label>

                            <div class="col-md-6">
                                <input id="cars_removed" type="number" step="0.00001" value = "{{ old('cars_removed')  ? old('cars_removed') : $recycle_category->cars_removed }}"  class="form-control @error('cars_removed') is-invalid @enderror" name="cars_removed">

                                @error('cars_removed')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="household_saving" class="col-md-4 col-form-label text-md-right">{{ __('Household Saving') }}</label>

                            <div class="col-md-6">
                                <input id="household_saving" type="number" step="0.00001" value = "{{ old('household_saving') ? old('household_saving') : $recycle_category->household_saving }}" class="form-control @error('household_saving') is-invalid @enderror" name="household_saving">

                                @error('household_saving')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="water_saved" class="col-md-4 col-form-label text-md-right">{{ __('Water Saved') }}</label>

                            <div class="col-md-6">
                                <input id="water_saved" type="number" step="0.00001" value = "{{ old('water_saved') ? old('household_saving') : $recycle_category->water_saved}}" class="form-control @error('water_saved') is-invalid @enderror" name="water_saved">

                                @error('water_saved')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        	
                        <div class="form-group row">
                            <label for="wheelie_bins" class="col-md-4 col-form-label text-md-right">{{ __('Wheelie Bins') }}</label>

                            <div class="col-md-6">
                                <input id="wheelie_bins" type="number" step="0.00001" value = "{{ old('wheelie_bins') ? old('wheelie_bins') : $recycle_category->wheelie_bins }}" class="form-control @error('wheelie_bins') is-invalid @enderror" name="wheelie_bins">

                                @error('wheelie_bins')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div> -->


                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success pull-right" type="submit">{{ __('Edit') }}</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection