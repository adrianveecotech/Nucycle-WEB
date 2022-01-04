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
                                <th><span>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rewards_categories as $rewards_category)
                            <tr>
                                <td class="lalign">{{$rewards_category->id}}</td>
                                <td>{{$rewards_category->name}}</td>
                                <td>
                                    <a href="{{route('settings.rewards_category.view', ['id' => $rewards_category->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                    <a href="{{route('settings.rewards_category.edit', ['id' => $rewards_category->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                    <a href="{{route('settings.rewards_category.delete', ['id' => $rewards_category->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
             
            </div>
        </div>
    </div>