@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">        <div class="col">
            <form method="POST" action="{{ route('voucher.edit_db') }}">
                <!-- <form method="POST"> -->
                @csrf
                <div class="card">
                    <div class="card-header">{{ __('Edit Voucher') }}</div>
                    <div class="card-body">
                        <div id="body">
                            <input type="hidden" name="reward_id" value="{{$id}}">
                            @if(count($vouchers) == 0)
                            <div id="dynamicInput[0]" style="margin-top:50px">
                                <div class="form-group row">
                                    <label for="code" class="col-md-4 col-form-label text-md-right">{{ __("Code") }}</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" name="codeNew[]">
                                        <input type="hidden" name="indexNew[]" value=0>
                                        @error("code")
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="is_redeem" class="col-md-4 col-form-label text-md-right">{{ __("Is redeem?") }}</label>

                                    <div class="col-md-6">
                                        <input type="checkbox" name="active_statusNew[]" value=0>
                                    </div>
                                    <button class="btn btn-xs btn-success pull-right" type="button" onclick="addInput();"><i class="fa fa-plus"></i></button>
                                    <button class="btn btn-xs btn-danger" type="button" onclick="removeInput(0);"><i class="fa fa-minus"></i></button>
                                </div>

                            </div>
                            @endif
                            @foreach($vouchers as $key => $voucher)
                            <div id="dynamicInput[<?php echo $key ?>]">
                                <input type="hidden" name="index[]" value="{{$voucher->id}}">

                                <div class="form-group row mt-5">
                                    <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Code') }}</label>

                                    <div class="col-md-6">
                                        <input type="text" value="{{$voucher->code}}" class="form-control @error('code') is-invalid @enderror" name="code[]">

                                        @error('code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="is_redeem" class="col-md-4 col-form-label text-md-right">{{ __('Is redeem?') }}</label>

                                    <div class="col-md-6">
                                        @if($voucher->is_redeem == 1)
                                        <input type="checkbox" name="is_redeem[]" value="{{$voucher->id}}" checked>
                                        @elseif($voucher->is_redeem == 0)
                                        <input type="checkbox" name="is_redeem[]" value="{{$voucher->id}}">
                                        @endif

                                    </div>
                                    <button class="btn btn-xs btn-success pull-right" type="button" onclick="addInput();"><i class="fa fa-plus"></i></button>
                                    <button class="btn btn-xs btn-danger" type="button" onclick="removeInput(<?php echo $key ?>);"><i class="fa fa-minus"></i></button>

                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success pull-right" type="submit">{{ __('Submit') }}</button>
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
    var counter = <?php echo count($vouchers); ?>;
    counter = counter + 1;
    var dynamicInput = [];

    function addInput() {
        var newdiv = document.createElement('div');
        newdiv.id = "dynamicInput[" + counter + "]";
        newdiv.style.cssText = 'margin-top:50px';
        newdiv.innerHTML = '<div class="form-group row">' +
            '<label for="code" class="col-md-4 col-form-label text-md-right">{{ __("Code") }}</label>' +
            '<div class="col-md-6">' +
            '<input type="text" class="form-control @error("code") is-invalid @enderror" name="codeNew[]">' +
            '<input type="hidden" name = "indexNew[]" value = "' + counter + '">' +
            '@error("code")' +
            '<span class="invalid-feedback" role="alert">' +
            '<strong>{{ $message }}</strong>' +
            '</span>' +
            '@enderror' +
            '</div>' +
            '</div>' +
            '<div class="form-group row">' +
            '<label for="is_redeem" class="col-md-4 col-form-label text-md-right">{{ __("Is redeem?") }}</label>' +

            '<div class="col-md-6">' +
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