@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Waste Clearance Schedule<small class="ml-3 mr-3"></small></h1>
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
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>Waste Clearance Schedule List</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{!! route('waste_clearance.create') !!}"><i class="fa fa-plus mr-2"></i>Schedule Waste Clearance</a>
        </li>
        <div id="hubInfo"></div>
      </ul>
    </div>
    <div class="card-body">
      @include('waste_clearance.table')
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

  function copySchedule(schedule, hub, hub_state, items) {
    var x = document.createElement("textarea");
    var collection_time = "Collection time : " + schedule.collection_time;
    var hub_info = "\nCollection Hub Name : " + hub.hub_name + '\nAddress : ' + hub.hub_address + ', ' + hub.hub_postcode + ', ' + hub_state.name;
    var operating_time = ("\nOperating time : " + hub.operating_day + ', ' + hub.operating_hours);
    var phone = ("\nContact Number : " + hub.contact_number);
    var pin = ("\nPin Code : " + schedule.pin_code + "\n\n");
    items = items.replace(',', "\n");
    // items.forEach(element => {
    //   item += ("\nItem : " + items.pin_code );
    // });
    document.body.appendChild(x);
    x.value = collection_time + hub_info + operating_time + phone + pin + items;
    x.select();
    document.execCommand("copy");
    document.body.removeChild(x);
  }
</script>
@endsection