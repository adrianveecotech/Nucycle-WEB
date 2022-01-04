@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Tag<small class="ml-3 mr-3"></small></h1>
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
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>Tag List</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{!! route('settings.banner_tag.create') !!}"><i class="fa fa-plus mr-2"></i>Create Tag</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      @include('settings.banner_tag.table')
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