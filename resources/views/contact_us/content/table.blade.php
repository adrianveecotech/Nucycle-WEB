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
                    <table cellpadding="0" cellspacing="0" border="0" id="tableMain">
                        <thead>
                            <tr>
                                <th><span>Facebook URL</span></th>
                                <th><span>Instagram URL</span></th>
                                <th><span>Website URL</span></th>
                                <th><span>Phone</span></th>
                                <th><span>Email</span></th>
                                <th><span>Address</span></th>
                                <th><span>Action</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="lalign">{{$content->facebook_url}}</td>
                                <td>{{$content->instagram_url}}</td>
                                <td>{{$content->website_url}}</td>
                                <td>{{$content->phone}}</td>
                                <td>{{$content->email}}</td>
                                <td>{{$content->address}}</td>
                                <td>
                                    <a href="{{route('contact_us.content.edit')}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-edit"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>