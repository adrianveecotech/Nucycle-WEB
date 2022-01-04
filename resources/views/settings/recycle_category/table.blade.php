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
                                <th><span>Name</span></th>
                                @foreach($indicators as $indicator)
                                <th><span>{{$indicator->name}}</span></th>
                                @endforeach
                                <!-- <th><span>Cars Removed</span></th>
                                <th><span>Household Saving</span></th>
                                <th><span>Water Saved</span></th>
                                <th><span>Wheelie Bins</span></th> -->
                                <th><span>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recycle_categories as $recycle_category)
                            <tr>
                                <td class="lalign">{{$recycle_category->id}}</td>
                                <td>{{$recycle_category->name}}</td>
                                @foreach($indicators as $indicator)
                                <td>{{$category_indicator_values->where('recycle_category_id',$recycle_category->id)->where('indicator_id',$indicator->id)->first()->value}}</td>
                                @endforeach
                                <!-- <td>{{$recycle_category->cars_removed}}</td>
                                <td>{{$recycle_category->household_saving}}</td>
                                <td>{{$recycle_category->water_saved}}</td>
                                <td>{{$recycle_category->wheelie_bins}}</td> -->
                                <td>
                                    <a href="{{route('settings.recycle_category.view', ['id' => $recycle_category->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                    <a href="{{route('settings.recycle_category.edit', ['id' => $recycle_category->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                    <!-- <a href="{{route('settings.recycle_category.delete', ['id' => $recycle_category->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a> -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>