@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Be Our Partner Content') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('be_our_partner.content.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value={{$id}} name="content_id">
                        <div class="form-group row">
                            <label for="question" class="col-md-4 col-form-label text-md-right">{{ __('Type') }}</label>
                            <div class="col-md-6">
                                {{$content->be_our_partner_type->name}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="content" class="col-md-4 col-form-label text-md-right">{{ __('Content') }}</label>

                            <div class="col-md-6">
                                <textarea class="content" id="content" class="form-control @error('content') is-invalid @enderror" name="content" value="{{ old('content')  }}"></textarea>
                                @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success" type="submit">{{ __('Submit') }}</button>

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
        var javaScriptVar = <?php echo str_replace("'","\'",json_encode($content->content)); ?>;
        $('#content').summernote('code',javaScriptVar,{
            height: 400,
            callbacks: {
                onImageUpload: function(files, editor, welEditable) {
                    that = $(this);
                    sendFile(files[0], that);
                }
            }
        });

        function sendFile(file, that) {
            data = new FormData();
            data.append("file", file);
            data.append('locations', 'promotion_image');
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