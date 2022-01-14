@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Guideline') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('guideline.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value={{$id}} name="guideline_id">
                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>
                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title',$guideline->title)  }}" autofocus>

                                @error('title')
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
                            <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Image') }}</label>

                            <div class="col-md-6">
                                <input id="image" type="file" name="image" accept="image/*" class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}">

                                @if ($errors->has('image'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('image') }}</strong>
                                </span>
                                @endif
                                <img id="imagePreview" width="100%" src="<?php echo env('APP_URL') . '/nucycle-admin/images/guideline_image/' . $guideline->image ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="banner" class="col-md-4 col-form-label text-md-right">{{ __('Banner Tag') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="banner" name="banner">
                                    <option value="">None</option>
                                    @foreach ($banners as $key=>$banner)
                                    @if (old('banner') == $banner->id || $guideline->banner_tag_id == $banner->id)
                                    <option value="{{ $banner->id }}" selected>{{ $banner ->name }}</option>
                                    @else
                                    <option value="{{ $banner->id }}">{{ $banner ->name }}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __('Start Date') }}</label>

                            <div class="col-md-6">
                                <input id="start_date" type="datetime-local" value="{{ old('start_date',date('Y-m-d\TH:i',strtotime($guideline['start_date']))) }}" class="form-control @error('start_date') is-invalid @enderror" name="start_date">
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
                                <input id="end_date" type="datetime-local" value="{{ old('end_date',date('Y-m-d\TH:i',strtotime($guideline['end_date']))) }}" class="form-control @error('end_date') is-invalid @enderror" name="end_date">
                                @error('end_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                            <div class="col-md-6 col-form-label">
                                @if (old('status') == 'Draft' || $guideline->status == 0)
                                <input type="radio" id="draft" name="status" value="Draft" checked>
                                @else
                                <input type="radio" id="draft" name="status" value="Draft">
                                @endif
                                <label for="draft">Draft</label><br>

                                @if (old('status') == 'Publish' || $guideline->status == 1)
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

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Target User') }}</label>

                            <div class="col-md-6">
                                <input type="checkbox" id="for_customer" name="for_customer" value="2" <?php if (strpos($guideline->target_role, '2') !== false) {
                                                                                                            echo 'checked';
                                                                                                        } ?>>
                                <label for="for_customer">User</label><br>
                                <input type="checkbox" id="for_collector" name="for_collector" value="3" <?php if (strpos($guideline->target_role, '3') !== false) {
                                                                                                                echo 'checked';
                                                                                                            } ?>>
                                <label for="for_collector">Collector</label><br>
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

        var javaScriptVar = '<?php echo str_replace("'","\'",$guideline->description); ?>';
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
            data.append('locations', 'guideline_image');
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