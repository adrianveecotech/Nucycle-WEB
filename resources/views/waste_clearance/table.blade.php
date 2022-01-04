@if (\Session::has('successMsg'))
<div class="alert alert-success">
    <ul>
        <li>{!! \Session::get('successMsg') !!}</li>
    </ul>
</div>
@endif

@if (\Session::has('warningMsg'))
<div class="alert alert-danger">
    {!! \Session::get('warningMsg') !!}
</div>
@endif
<div class="row justify-content-center">
    <div class="col">
        <div id="wrapper">
            <div class="tbl-header">
                <table class="table table-striped" id="tableMain">
                    <thead>
                        <tr>
                            <th><span>ID</span></th>
                            <th><span>Collection Time</span></th>
                            <th><span>Collection Hub</span></th>
                            <th><span>Buyer Name</span></th>
                            <th><span>Buyer Contact Number</span></th>
                            <th><span>Pin Code</span></th>
                            <th><span>Status</span></th>
                            <th><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                        <tr>
                            <td class="lalign">{{$schedule->id}}</td>
                            <td>{{$schedule->collection_time}}</td>
                            <td>{{optional($schedule->collection_hub)->hub_name}}</td>
                            <td>{{$schedule->buyer_name}}</td>
                            <td>{{$schedule->buyer_phone_number}}</td>
                            <td>{{$schedule->pin_code}}</td>
                            <td>@if($schedule->status == 1) Pending @elseif($schedule->status == 2) Completed @elseif($schedule->status == 3) Cancelled @endif </td>
                            <td>
                                @if($schedule->collection_hub)
                                <a href="#" onclick="copySchedule(<?php echo htmlspecialchars(json_encode($schedule)) ?> ,<?php echo htmlspecialchars(json_encode($schedule->collection_hub)) ?>,<?php echo htmlspecialchars(json_encode($schedule->collection_hub->state)) ?>,<?php 
                                $items = array(); 
                                foreach ($schedule->items as $key => $value) {
                                    $items[] = $value->recycle_type->name . ' - ' . $value->weight.'kg' ;
                                };
                                echo htmlspecialchars(json_encode(implode(',',$items))); ?>)" id="btnCopy" class="btn btn-xs btn-success"><i class="nav-icon fa fa-clone"></i></a>
                                @endif
                                <a href="{{route('waste_clearance.view', ['id' => $schedule->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                @if($schedule->status == 1)
                                <a href="{{route('waste_clearance.edit', ['id' => $schedule->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                <a href="{{route('waste_clearance.cancel', ['id' => $schedule->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-ban"></i></a>
                                @elseif($schedule->status == 2)
                                <a href="{{route('waste_clearance.view_statement', ['id' => $schedule->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-file"></i></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>