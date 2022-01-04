@extends('layouts.app')
@section('content')

<div class="container">
<div class="row ">
        <div class="col">
            <form method="POST" action="{{ route('collection_hub_recycle_type.insert_db') }}">
                @csrf
                <div class="card">
                    <div class="card-header">{{ __('Add New Collection Hub Recycle Type') }}</div>

                    <div class="card-body">


                        <div class="form-group row">
                            <label for="recycle_type" class="col-md-4 col-form-label text-md-right">{{ __('Recycle Type') }}</label>

                            <div class="col-md-6">
                                <select id="recycle_type" name="recycle_type">
                                    @foreach ($recycle_types as $recycle_type)
                                    <option value='{{$recycle_type->id}}'>{{$recycle_type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if(in_array(1, Auth::user()->users_roles_id()))
                        <div class="form-group row">
                            <label for="hub" class="col-md-4 col-form-label text-md-right">{{ __('Collection Hub') }}</label>

                            <div class="col-md-6">
                                <select id="hub" name="hub">
                                    @foreach ($hubs as $hub)
                                    <option value='{{$hub->id}}'>{{$hub->hub_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                <br>
                <br>
                <div class="card">
                    <div class="card-header">{{ __('Add Point') }}</div>
                    <div class="card-body">
                        <div id="body">
                            <div class="form-group row">
                                <label for="point" class="col-md-4 col-form-label text-md-right">{{ __('Point') }}</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('point') is-invalid @enderror" name="point[]">

                                    @error('point')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __('Start Date') }}</label>

                                <div class="col-md-6">
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" name="start_date[]">

                                    @error('start_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="end_date" class="col-md-4 col-form-label text-md-right">{{ __('End Date') }}</label>

                                <div class="col-md-6">
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" name="end_date[]">

                                    @error('end_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                            </div>

                            <div class="form-group row">
                                <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Active') }}</label>

                                <div class="col-md-6">
                                    <input type="checkbox" name="active_status[]" value="active0">
                                </div>
                                <button class="btn btn-xs btn-success pull-right" type="button" onclick="addInput();"><i class="fa fa-plus"></i></button>

                            </div>


                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success pull-right" type="submit">{{ __('Create') }}</button>

                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var counter = 1;
    var dynamicInput = [];

    function addInput() {
        var newdiv = document.createElement('div');
        newdiv.id = "dynamicInput[" + counter + "]";
        newdiv.style.cssText = 'margin-top:50px';
        newdiv.innerHTML = '<div class="form-group row">' +
            '<label for="point" class="col-md-4 col-form-label text-md-right">{{ __("Point") }}</label>' +
            '<div class="col-md-6">' +
            '<input type="text" class="form-control @error("point") is-invalid @enderror" name="point[]">' +

            '@error("point")' +
            '<span class="invalid-feedback" role="alert">' +
            '<strong>{{ $message }}</strong>' +
            '</span>' +
            '@enderror' +
            '</div>' +
            '</div>' +
            '<div class="form-group row">' +
            '<label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __("Start Date") }}</label>' +

            '<div class="col-md-6">' +
            '<input type="datetime-local" class="form-control @error("start_date") is-invalid @enderror" name="start_date[]">' +

            '@error("start_date")' +
            '<span class="invalid-feedback" role="alert">' +
            '<strong>{{ $message }}</strong>' +
            '</span>' +
            '@enderror' +
            '</div>' +
            '</div>' +

            '<div class="form-group row">' +
            '<label for="end_date" class="col-md-4 col-form-label text-md-right">{{ __("End Date") }}</label>' +

            '<div class="col-md-6">' +
            '<input type="datetime-local" class="form-control @error("end_date") is-invalid @enderror" name="end_date[]">' +

            '@error("end_date")' +
            '<span class="invalid-feedback" role="alert">' +
            '<strong>{{ $message }}</strong>' +
            '</span>' +
            '@enderror' +
            '</div>' +
            '</div>' +
            '<div class="form-group row">' +
            '<label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __("Active") }}</label>' +

            '<div class="col-md-6">' +
            '<input type="checkbox" name="active_status[]" value="active' + counter + '">' +
            '</div>' +
            '<button class="btn btn-xs btn-success pull-right" type="button" onclick="addInput();"><i class="fa fa-plus"></i></button>' +
            '<button class="btn btn-xs btn-danger" type="button" onclick="removeInput(' + counter + ');"><i class="fa fa-minus"></i></button>' +
            '</div>' +
            '</div>';

        document.getElementById('body').appendChild(newdiv);
        counter++;
    }

    function removeInput(id) {
        var elem = document.getElementById('dynamicInput[' + id + ']');
        return elem.parentNode.removeChild(elem);
    }
</script>
@endsection