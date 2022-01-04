@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Collection Hub Recycle Item<small class="ml-3 mr-3"></small></h1>
            </div><!-- /.col -->

        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<div class="content">
    <div class="clearfix"></div>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                <li class="nav-item">
                    <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>Item List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('collection_hub_recycle_type.create') !!}"><i class="fa fa-plus mr-2"></i>Create Item</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @include('collection_hub_recycle_type.table_admin')
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    jQuery(document).ready(function() {
        getItem();
        
        jQuery('#hub').change(function() {
            getItem();
        });
        

        function getItem() {
            var hub_id = $('#hub').find(":selected").val();
            if (hub_id == "Select") {
                $('#hubRecycle').DataTable().destroy();
                $('#hubRecycle tbody').empty();
                return;
            }
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: '{{ route("collection_hub_recycle_type.get_hub_recycle") }}',
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    hub_id: hub_id
                },
                success: function(data) {
                    if (data.html) {
                        // jQuery.noConflict();
                        $('#hubRecycle').DataTable().destroy();
                        $('#hubRecycle tbody').empty();
                        $('#hubRecycle tbody').append(data.html);
                        // jQuery.noConflict();
                        $('#hubRecycle').DataTable();
                    } else {
                        $('#hubRecycle tbody').empty();
                    }

                },
                error: function(data) {
                    console.log(data.responseJSON.message);
                }

            });
        }


    });
</script>
@endsection