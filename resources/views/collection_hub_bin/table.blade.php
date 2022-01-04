@if (\Session::has('successMsg'))
<div class="alert alert-success">
    {!! \Session::get('successMsg') !!}
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
                            <th><span>Collection Hub</span></th>
                            <th><span>Recycle Item</span></th>
                            <th><span>Capacity Weight (kg)</span></th>
                            <th><span>Current Weight (kg)</span></th>
                            <th><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bins as $bin)
                        @if( $bin->capacity_weight!=0)
                        @if($bin->current_weight / $bin->capacity_weight >= 0.9 )
                        <tr style="color: red">
                            @else
                        <tr>
                            @endif
                            @elseif( $bin->capacity_weight == 0)
                            @if($bin->current_weight > $bin->capacity_weight)
                        <tr style="color: red">
                            @endif
                            @endif
                            <td class="lalign">{{$bin->id}}</td>
                            <td>{{$bin->collection_hub->hub_name}}</td>
                            <td>{{$bin->recycle_type->name}}</td>
                            <td>{{$bin->capacity_weight}}</td>
                            <td>{{$bin->current_weight}}</td>
                            <td>
                                <a href="{{route('collection_hub_bin.edit', ['id' => $bin->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                <a href="{{route('collection_hub_bin.reset', ['id' => $bin->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-refresh"></i></a>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>