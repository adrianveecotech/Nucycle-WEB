@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Collection Hub Recycle Type ') }}</div>
                <div class="card-body">
                    <div id="body">
                        <div class="form-group row">
                            <label for="hub_recycle" class="col-md-4 col-form-label text-md-right">{{ __('Recycle Type') }}</label>

                            <div class="col-md-6  col-form-label">
                                {{$hub_recycles[0]->recycle_type->name}}
                            </div>
                        </div>
                        @foreach($hub_recycles as $key => $hub_recycle)
                        <div id="dynamicInput[<?php echo $key ?>]">
                            <div class="form-group row mt-5">
                                <label for="point" class="col-md-4 col-form-label text-md-right">{{ __('Point') }}</label>

                                <div class="col-md-6  col-form-label">
                                    {{$hub_recycle->point}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __('Start Date') }}</label>
                                <div class="col-md-6  col-form-label">
                                    {{$hub_recycle->start_date}}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="end_date" class="col-md-4 col-form-label text-md-right">{{ __('End Date') }}</label>

                                <div class="col-md-6  col-form-label">
                                    {{$hub_recycle->end_date}}
                                </div>

                            </div>

                            <div class="form-group row">
                                <label for="active_status" class="col-md-4 col-form-label text-md-right">{{ __('Active') }}</label>
                                <div class="col-md-6  col-form-label">
                                    {{$hub_recycle->is_active == 1 ? 'Active' : 'Inactive'}}
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('collection_hub_recycle_type.edit', ['id'=> $id, 'hub_id' => $hub_id]) }}" class="btn btn-xs btn-success pull-right" type="button">{{ __('Edit') }}</a>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var counter = <?php echo count($hub_recycles); ?>;
    counter = counter + 1;
    var dynamicInput = [];

    function addInput() {
        var newdiv = document.createElement('div');
        newdiv.id = "dynamicInput[" + counter + "]";
        newdiv.style.cssText = 'margin-top:50px';
        newdiv.innerHTML = '<div class="form-group row">' +
            '<label for="point" class="col-md-4 col-form-label text-md-right">{{ __("Point") }}</label>' +
            '<div class="col-md-6">' +
            '<input type="text" class="form-control @error("point") is-invalid @enderror" name="pointNew[]">' +

            '@error("point")' +
            '<span class="invalid-feedback" role="alert">' +
            '<strong>{{ $message }}</strong>' +
            '</span>' +
            '@enderror' +
            '</div>' +
            '</div>' +
            '<div class="form-group row">' +
            '<label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __("Start Date") }}</label>' +
            '<input type="hidden" name = "indexNew[]" value = "' + counter + '">' +
            '<div class="col-md-6">' +
            '<input type="datetime-local" class="form-control @error("start_date") is-invalid @enderror" name="start_dateNew[]">' +

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
            '<input type="datetime-local" class="form-control @error("end_date") is-invalid @enderror" name="end_dateNew[]">' +

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
            // '<input type="hidden" name="active_status[]" value="0">' +
            // '<input type="checkbox" name="active_status[]" value="1">' +
            '<input type="checkbox" name="active_statusNew[]" value="' + counter + '">' +
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

    // $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    // });

    // $('#form').on('submit', function(event) {
    //     alert('test');
    //     return;
    //     event.preventDefault();
    //     $.ajax({
    //         url: '{{ route("collection_hub_recycle_type.edit_db") }}',
    //         method: 'post',
    //         data: FormData,
    //         success: function(data) {
    //             console.log('test');
    //         }
    //     });
    // });
</script>
@endsection