@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Reward<small class="ml-3 mr-3"></small></h1>
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
                    <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>Reward List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('reward.create') !!}"><i class="fa fa-plus mr-2"></i>Create Reward</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @include('reward.table')
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    jQuery(document).ready(function() {
        getItem();

        jQuery('#merchant').change(function() {
            getItem();
        });

        jQuery('#filter').change(function() {
            getItem();
        });


        function getItem() {
            var merchant_id = $('#merchant').find(":selected").val();
            var status_filter = $('#filter').find(":selected").val();
            if (merchant_id == "Select") {
                // jQuery.noConflict();
                $('#reward_table').DataTable().destroy();
                $('#reward_table tbody').empty();
                return;
            }
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: '{{ route("reward.get_reward_by_merchant") }}',
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    merchant_id: merchant_id,
                    status_filter: status_filter
                },
                success: function(data) {
                    if (data.html) {
                        // jQuery.noConflict();
                        $('#reward_table').DataTable().destroy();
                        $('#reward_table tbody').empty();
                        $('#reward_table tbody').append(data.html);
                        // jQuery.noConflict();
                        $('#reward_table').DataTable();
                    } else {
                        $('#reward_table tbody').empty();
                    }
                },
                error: function(data) {
                    console.log(data.responseJSON.message);
                }

            });

            // console.log(pausecontent);
            // recycle[hub_id].forEach(element => {
            //     console.log(element);
            //     // $("#hubRecycle tbody").append("<tr>" +
            //     //     "<td class='lalign'>" + element. + "</td>" +
            //     //     "<td>" + $("#introdate").val() + "</td>" +
            //     //     "<td>" + $("#url").val() + "</td>" +
            //     //     "</tr>");
            // });
        };
    });
</script>
@endsection