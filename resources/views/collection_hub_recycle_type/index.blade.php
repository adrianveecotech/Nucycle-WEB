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
                @if(in_array(4, Auth::user()->users_roles_id()) && $hub->read_only != 0)

                @else
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('collection_hub_recycle_type.create') !!}"><i class="fa fa-plus mr-2"></i>Create Item</a>
                </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            @include('collection_hub_recycle_type.table')
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#tableMain').DataTable();
    });
</script>
@endsection