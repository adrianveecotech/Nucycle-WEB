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
                                <th><span>Recycle Item</span></th>
                                <th><span>Point</span></th>
                                <th><span>Start Date</span></th>
                                <th><span>End Date</span></th>
                                <th><span>Active</span></th>
                                @if(in_array(4, Auth::user()->users_roles_id()) && $hub->read_only != 0)
                                @else
                                <th><span>Action</span></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hub_recycles as $hub_recycle)
                            <tr>
                                <td class="lalign">
                                    {{$hub_recycle[0]->recycle_type->name}}
                                </td>
                                <td> @foreach($hub_recycle as $hub_recycle1)
                                    {{$hub_recycle1->point}}<br>
                                    @endforeach
                                </td>
                                <td> @foreach($hub_recycle as $hub_recycle1)
                                    {{$hub_recycle1->start_date}}<br>
                                    @endforeach
                                </td>
                                <td> @foreach($hub_recycle as $hub_recycle1)
                                    {{$hub_recycle1->end_date}}<br>
                                    @endforeach
                                </td>
                                <td> @foreach($hub_recycle as $hub_recycle1)
                                    {{$hub_recycle1->is_active == 0 ? 'Inactive' : 'Active'}}<br>
                                    @endforeach
                                </td>
                                @if(in_array(4, Auth::user()->users_roles_id()) && $hub->read_only != 0)
                                @else
                                <td>
                                    <a href="{{route('collection_hub_recycle_type.view', ['id' => $hub_recycle[0]->recycle_type_id,'hub_id' => $hub_recycle[0]->collection_hub_id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                    <a href="{{route('collection_hub_recycle_type.edit', ['id' => $hub_recycle[0]->recycle_type_id,'hub_id' => $hub_recycle[0]->collection_hub_id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                    <a href="{{route('collection_hub_recycle_type.delete', ['id' => $hub_recycle[0]->recycle_type_id,'hub_id' => $hub_recycle[0]->collection_hub_id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>