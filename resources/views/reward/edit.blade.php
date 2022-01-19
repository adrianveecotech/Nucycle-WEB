@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <form method="POST" action="{{ route('reward.edit_db') }}" enctype="multipart/form-data">
                <!-- <form method="POST"> -->
                @csrf
                <div class="card">
                    <div class="card-header">{{ __('Edit Reward') }}</div>
                    <div class="card-body">
                        <div id="body">
                            <div class="form-group row">
                                <input type="hidden" name="reward_id" value="{{$id}}">
                                <label for="hub_recycle" class="col-md-4 col-form-label text-md-right">{{ __('Merchant') }}</label>

                                <div class="col-md-6  col-form-label">
                                    <select id="merchant" name="merchant">

                                        @foreach ($merchants as $merchant)
                                        @if($merchant->id == $reward->merchant_id)
                                        <option value='{{$merchant->id}}' selected>{{$merchant->name}}</option>
                                        @else
                                        <option value='{{$merchant->id}}'>{{$merchant->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="hub_recycle" class="col-md-4 col-form-label text-md-right">{{ __('Reward Category') }}</label>

                                <div class="col-md-6  col-form-label">
                                    <select id="category" name="category">
                                        @foreach ($categories as $category)
                                        @if($category->id == $reward->reward_category_id)
                                        <option value='{{$category->id}}' selected>{{$category->name}}</option>
                                        @else
                                        <option value='{{$category->id}}'>{{$category->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>

                                <div class="col-md-6">
                                    <input type="text" value="{{ old('title') ? old('title') : $reward['title'] }}" class="form-control @error('title') is-invalid @enderror" name="title">

                                    @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Image') }}</label>

                                <div class="col-md-6">
                                    <input id="image" type="file" name="image" accept="image/*" class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}">

                                    @if ($errors->has('image'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('image') }}</strong>
                                    </span>
                                    @endif
                                    <img id="imagePreview" width="100%" src="<?php echo env('APP_URL') . '/nucycle-admin/images/reward_image/' . $reward->image ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="point" class="col-md-4 col-form-label text-md-right">{{ __('Point') }}</label>

                                <div class="col-md-6">
                                    <input id="point" type="number" value="{{ old('point') ? old('point') : $reward['point'] }}" class="form-control @error('point') is-invalid @enderror" name="point" autofocus>

                                    @error('point')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="redemption" class="col-md-4 col-form-label text-md-right">{{ __('Redemption per user') }}</label>

                                <div class="col-md-6">
                                    <input id="redemption" type="number" value="{{ old('redemption')  ? old('redemption') : $reward['redemption_per_user']}}" class="form-control @error('redemption') is-invalid @enderror" name="redemption" autofocus>

                                    @error('redemption')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                                <div class="col-md-6">
                                    <textarea class="content" id="description" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description')  }}"></textarea>
                                    @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="tag-typher" class="col-md-4 col-form-label text-md-right">{{ __('Tag') }}</label>
                                <!-- </div>
                        <div class="form-group row"> -->
                                <!-- <div class="col-md-6">
                                <div id="tags">
                                    <input id="tag-typer" name="tag-typher" type="text" placeholder="Add tag..." />
                                </div>
                            </div> -->
                                <div class="col-md-6">
                                    <input id="tag" type="text" value="{{ old('tag',$reward['tag']) }}" class="form-control @error('tag') is-invalid @enderror" name="tag" autofocus>
                                    <span>
                                        <small>Press enter to enter a new tag.</small>
                                    </span>

                                    @error('tag')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __('Start Date') }}</label>

                                <div class="col-md-6">
                                    <input id="start_date" type="date" value="{{ old('start_date',date('Y-m-d',strtotime($reward['start_date']))) }}" class="form-control @error('start_date') is-invalid @enderror" name="start_date" autofocus>

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
                                    <input id="end_date" type="date" value="{{ old('end_date',date('Y-m-d',strtotime($reward['end_date'])))}}" class="form-control @error('end_date') is-invalid @enderror" name="end_date" autofocus>

                                    @error('end_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="terms" class="col-md-4 col-form-label text-md-right">{{ __('Terms') }}</label>

                                <div class="col-md-6">
                                    <input id="terms" type="text" value="{{ old('terms') ? old('terms') : $reward['terms'] }}" class="form-control @error('terms') is-invalid @enderror" name="terms" autofocus>

                                    @error('terms')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>

                                <div class="col-md-6 col-form-label">
                                    @if($reward->status == 0)
                                    <input type="radio" id="draft" name="status" value="Draft" checked>
                                    <label for="draft">Draft</label><br>
                                    <input type="radio" id="publish" name="status" value="Publish">
                                    <label for="publish">Publish</label><br>
                                    @elseif($reward->status == 1)
                                    <input type="radio" id="draft" name="status" value="Draft">
                                    <label for="draft">Draft</label><br>
                                    <input type="radio" id="publish" name="status" value="Publish" checked>
                                    <label for="publish">Publish</label><br>
                                    @elseif($reward->status == 2)
                                    <input type="radio" id="draft" name="status" value="Draft">
                                    <label for="draft">Draft</label><br>
                                    <input type="radio" id="publish" name="status" value="Publish">
                                    <label for="publish">Publish</label><br>
                                    <input type="radio" id="expired" name="status" value="Expired" checked>
                                    <label for="expired">Expired</label><br>
                                    @endif

                                    @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

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
    $(document).ready(function() {
        $("#tag").keypress(function(event) {
            var key = event.which;
            if (key == 13 || key == 44) {
                event.preventDefault();
                var tag = $(this).val();
                if (tag.length > 0) {

                    // $("<span class='tag' style='display:none'><span class='close'>&times;</span>" + tag + " </span>").insertBefore(this).fadeIn(100);
                    $(this).val(tag + ' ; ');
                }
            }
        });

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

        var javaScriptVar = <?php echo str_replace("'","\'",json_encode($reward->description)); ?>;
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
            data.append('locations', 'reward_image');
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