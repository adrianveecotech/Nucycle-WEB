@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Waste Clearance Schedule') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('waste_clearance.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value={{$id}} name="schedule_id">
                        <div class="form-group row">
                            <label for="collection_time" class="col-md-4 col-form-label text-md-right">{{ __('Collection Time') }}</label>
                            <div class="col-md-6">
                                <input id="title" type="datetime-local" class="form-control @error('collection_time') is-invalid @enderror" name="collection_time" value="{{ old('collection_time') ? old('collection_time') :date('Y-m-d\TH:i',strtotime($schedule->collection_time))  }}" autofocus>
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
                                    @if (old('hub') == $hub->id || $schedule->collection_hub_id == $hub->id)
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
                            <label for="buyer_name" class="col-md-4 col-form-label text-md-right">{{ __('Buyer Name') }}</label>

                            <div class="col-md-6">
                                <input id="buyer_name" class="form-control @error('buyer_name') is-invalid @enderror" name="buyer_name" value="{{ old('buyer_name') ? old('buyer_name') : $schedule->buyer_name }}">

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
                                <input type="number" id="buyer_phone_number" class="form-control @error('buyer_phone_number') is-invalid @enderror" name="buyer_phone_number" value="{{ old('buyer_phone_number') ?  old('buyer_phone_number') : $schedule->buyer_phone_number  }}">

                                @error('buyer_phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="pin_code" class="col-md-4 col-form-label text-md-right">{{ __('Pin Code') }}</label>

                            <div class="col-md-4 col-form-label">
                                {{$schedule->pin_code}}

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="item_lists" class="col-md-4 col-form-label text-md-right">{{ __('Item lists') }}</label>

                            <div class="col-md-6 col-form-label">
                                @foreach($items as $item)
                                <p class="col-6" style="width: 100%; ">{{$item->name}} - {{$item->weight}} kg</p>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>

                            <div class="col-md-6 col-form-label">
                                @if($schedule->status == 1)
                                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                                    @if (old('status') == 1 || $schedule->status == 1)
                                    <option value="1" selected>Pending</option>
                                    @else
                                    <option value="1">Pending</option>
                                    @endif

                                    @if (old('status') == 2 || $schedule->status == 2)
                                    <option value="2" selected>Completed</option>
                                    @else
                                    <option value="2">Completed</option>
                                    @endif

                                    @if (old('status') == 3 || $schedule->status == 3)
                                    <option value="3" selected>Cancelled</option>
                                    @else
                                    <option value="3">Cancelled</option>
                                    @endif

                                </select>
                                @endif
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

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            } else {
                $('#imagePreview').attr('src', '');
            }
        }

        $("#image").change(function() {
            readURL(this);
        });

        var javaScriptVar = '<?php echo str_replace("'","\'",$schedule->description); ?>';
        $('#description').summernote({
            height: 400,
            callbacks: {
                onImageUpload: function(files, editor, welEditable) {
                    console.log('test');
                    that = $(this);
                    sendFile(files[0], that);
                }
            }
        });
        $('#description').summernote('code', javaScriptVar);

        function sendFile(file, that) {
            data = new FormData();
            data.append("file", file);
            data.append('locations', 'schedule_image');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                }
            });
            $.ajax({
                data: data,
                type: "POST",
                url: "https://app.nucycle.com.my/summernoteUploadImage",
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                    $(that).summernote('insertImage', url, '')
                }
            });
        }
    });
</script>
@endsection