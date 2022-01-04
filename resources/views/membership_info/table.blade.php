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
                            <th><span>Content</span></th>
                            <th><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="lalign">{!!$membership->content!!}</td>
                            <td>
                                <a href="{{route('membership_info.edit')}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>