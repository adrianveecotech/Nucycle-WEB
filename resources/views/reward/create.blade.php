@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <form method="POST" action="{{ route('reward.insert_db') }}" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">{{ __('Add New Reward') }}</div>

                    <div class="card-body">


                        <div class="form-group row">
                            <label for="merchant" class="col-md-4 col-form-label text-md-right">{{ __('Merchant') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="merchant" name="merchant">
                                    @foreach ($merchants as $merchant)
                                    <option value='{{$merchant->id}}'>{{$merchant ->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="category" class="col-md-4 col-form-label text-md-right">{{ __('Reward Category') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="category" name="category">
                                    @foreach ($categories as $category)
                                    <option value='{{$category->id}}'>{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>

                            <div class="col-md-6">
                                <input type="text" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" name="title">

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
                                <img id="imagePreview" width="100%" src="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="point" class="col-md-4 col-form-label text-md-right">{{ __('Point') }}</label>

                            <div class="col-md-6">
                                <input id="point" type="number" value="{{ old('point') }}" class="form-control @error('point') is-invalid @enderror" name="point" autofocus>

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
                                <input id="redemption" type="number" value="{{ old('redemption') }}" class="form-control @error('redemption') is-invalid @enderror" name="redemption" autofocus>

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
                                <input id="tag" type="text" value="{{ old('tag') }}" class="form-control @error('tag') is-invalid @enderror" name="tag" autofocus>
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
                                <input id="start_date" type="date" value="{{ old('start_date') }}" class="form-control @error('start_date') is-invalid @enderror" name="start_date" autofocus>

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
                                <input id="end_date" type="date" value="{{ old('end_date') }}" class="form-control @error('end_date') is-invalid @enderror" name="end_date" autofocus>

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
                                <input id="terms" type="text" value="{{ old('terms') }}" class="form-control @error('terms') is-invalid @enderror" name="terms" autofocus>

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
                                @if (old('status') == 'Draft' || old('status') == '')
                                <input type="radio" id="draft" name="status" value="Draft" checked>
                                @else
                                <input type="radio" id="draft" name="status" value="Draft">
                                @endif
                                <label for="draft">Draft</label><br>

                                @if (old('status') == 'Publish')
                                <input type="radio" id="publish" name="status" value="Publish" checked>
                                @else
                                <input type="radio" id="publish" name="status" value="Publish">
                                @endif
                                <label for="publish">Publish</label><br>

                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
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
            '<label for="code" class="col-md-4 col-form-label text-md-right">{{ __("Code") }}</label>' +
            '<div class="col-md-6">' +
            '<input type="text" class="form-control @error("code") is-invalid @enderror" name="code[]">' +

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
            '<input type="checkbox" name="is_redeem[]" value="active' + counter + '">' +
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