@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Add New Waste Clearance Schedule') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('waste_clearance.insert_db') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="collection_time" class="col-md-4 col-form-label text-md-right">{{ __('Collection Time') }}</label>

                            <div class="col-md-6">
                                <input id="collection_time" type="datetime-local" value="{{ old('collection_time') }}" class="form-control @error('collection_time') is-invalid @enderror" name="collection_time">
                                @error('collection_time')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="hub" class="col-md-4 col-form-label text-md-right">{{ __('Collection Hub') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="hub" name="hub" class="form-control @error('hub') is-invalid @enderror">
                                    <option value="">Select a collection hub</option>
                                    @foreach ($hubs as $key=>$hub)
                                    @if (old('hub') == $hub->id)
                                    <option value="{{ $hub->id }}" selected>{{ $hub ->hub_name }}</option>
                                    @else
                                    <option value="{{ $hub->id }}">{{ $hub ->hub_name }}</option>
                                    @endif

                                    @endforeach
                                </select>
                                @error('hub')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                                <div id="hubInfo">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="hub" class="col-md-4 col-form-label text-md-right">{{ __('Items') }}</label>

                            <div class="col-md-5">
                                <select class="form-control" id="items_dropdodwn">
                                    <option id="default" value="" selected disabled hidden>Select items</option>
                                </select>

                                @error('hub')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col">
                                <button type="button" name="add_items" id="add_items" class="btn btn-success centered col-4">Add</button></td>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="hub" class="col-md-4 col-form-label text-md-right">{{ __('Item Lists') }}</label>

                            <div class="col-md-6">
                                <table class="col-10 centered" id="items_field"></table>
                                @if(Session::has('failMsg'))
                                <div class="alert alert-error" id='itemErrorMsg'>
                                    {!! \Session::get('failMsg') !!}
                                </div>
                                @endif
                            </div>


                        </div>

                        <div class="form-group row">
                            <label for="buyer_name" class="col-md-4 col-form-label text-md-right">{{ __('Buyer Name') }}</label>

                            <div class="col-md-6">
                                <input id="buyer_name" class="form-control @error('buyer_name') is-invalid @enderror" name="buyer_name" value="{{ old('buyer_name')  }}">

                                @error('buyer_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="buyer_phone_number" class="col-md-4 col-form-label text-md-right">{{ __('Buyer Phone Number') }}</label>

                            <div class="col-md-6">
                                <input type="number" id="buyer_phone_number" class="form-control @error('buyer_phone_number') is-invalid @enderror" name="buyer_phone_number" value="{{ old('buyer_phone_number')  }}">

                                @error('buyer_phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="pin_code" class="col-md-4 col-form-label text-md-right">{{ __('Pin Code') }}</label>

                            <div class="col-md-6">
                                <input id="pin_code" readonly class="form-control" value="<?php echo random_int(100000, 999999); ?>" name="pin_code">

                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success pull-right" type="submit">{{ __('Schedule') }}</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    function getHubInfo() {
        var hub_id = $('#hub').find(":selected").val();
        if (hub_id == '') {
            $('#hubInfo').empty();
            return;
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route("waste_clearance.get_hub_info") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                hub_id: hub_id,
            },
            success: function(data) {
                var itemsSelect = $('#items_dropdodwn');
                itemsSelect.empty();

                $.each(data[2], function(key, value) {
                    itemsSelect
                        .append($("<option></option>")
                            .attr("value", value.recycle_type_id)
                            .text(value.name));
                });
                var address = document.createTextNode("Address : " + data[0].hub_address + ', ' + data[0].hub_postcode + ', ' + data[1].name);
                var operating_time = document.createTextNode("Operating time : " + data[0].operating_day + ', ' + data[0].operating_hours);
                var phone = document.createTextNode("Phone Number : " + data[0].contact_number);
                $('#hubInfo').empty();
                $('#hubInfo').append('<br>');
                $('#hubInfo').append(address);
                $('#hubInfo').append('<br>');
                $('#hubInfo').append(operating_time);
                $('#hubInfo').append('<br>');
                $('#hubInfo').append(phone);
            },
            error: function(data) {
                console.log(data.responseJSON.message);
            }

        });

    }
    $(document).ready(function() {
        getHubInfo();

        $('#hub').change(function() {
            getHubInfo();
        })

        var i = 1;
        $('#add_items').click(function() {
            var material_id = $('#items_dropdodwn :selected').val();
            var material_name = $('#items_dropdodwn :selected').text();

            if (material_id == "") {
                alert("Please select an item to add.");
                return;
            }

            let str = $('#items_field input[name*=items]');
            let check = false;
            $.each(str, function(key, value) {
                if ($(value).data("items") == material_id) {
                    check = true;
                }
            });
            if (check) {
                alert("Same item cannot be added more than once.");
                return;
            }
            $('#itemErrorMsg').remove();
            $('#items_field').append(
                '<div class="row centered" style="width: 100%;" id="rowi' + i + '">' +
                '<p class="col-6 " style="width: 100%; "><b>Item name: ' + material_name + '</p>' +
                '<p class="col-2 " style="width: 100%;"> Weight(kg)</p>' +
                '<div><input class="form-control form-inline" style="width:150px" type="number"  step="0.01" data-items="' + material_id + '" name="items[' + material_id + ']" value="0"/></div>' +
                '<button class="btn btn-danger btn_remove_item" type="button" style="min-width:15px; margin: 0px 5px;" name="remove" id="i' + i + '">X</button>' +
                '</div>');

            i++;

        });

        $(document).on('click', '.btn_remove_item', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
        });
    });
</script>
@endsection