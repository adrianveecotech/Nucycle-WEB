@if (\Session::has('successMsg'))
<div class="alert alert-success">
    <ul>
        <li>{!! \Session::get('successMsg') !!}</li>
    </ul>
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
                            <th><span>Customer</span></th>
                            @if(in_array(1, Auth::user()->users_roles_id()))
                            <th><span>Collection Hub</span></th>
                            @endif
                            <th><span>Collector</span></th>
                            <th><span>Point</span></th>
                            <th><span>Total Weight(kg)</span></th>
                            <th><span>Status</span></th>
                            <th><span>Created At</span></th>
                            <th><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($collections as $collection)
                        <tr>
                            <td class="lalign">{{$collection->id}}</td>
                            <td>{{$collection->customer->name}}</td>
                            @if(in_array(1, Auth::user()->users_roles_id()))
                            <td>{{$collection->collection_hub->hub_name}}</td>
                            @endif
                            <td>{{$collection->collector->name}}</td>
                            <td>{{$collection->all_point}}</td>
                            <td>{{$collection->total_weight}}</td>
                            <td>{{$collection->status == 0 ? 'Cancelled' : 'Normal'}}</td>
                            <td>{{$collection->created_at}}</td>
                            <td>
                                <a href="{{route('collection.view', ['id' => $collection->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                <a href="{{route('collection.edit', ['id' => $collection->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                @if($collection->status == 1)
                                <a href="{{route('collection.cancel', ['id' => $collection->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-ban"></i></a>
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