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
                            <th><span>Title</span></th>
                            <th><span>Image</span></th>
                            <th><span>Banner Tag</span></th>
                            <th><span>Start Date</span></th>
                            <th><span>End Date</span></th>
                            <th><span>Target User</span></th>
                            <th><span>Status</span></th>
                            <th><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guidelines as $guideline)
                        <tr>
                            <td class="lalign">{{$guideline->title}}</td>
                            <td width='15%'><img width="100%" src=<?php echo env('APP_URL') . '/nucycle-admin/images/guideline_image/' . $guideline->image; ?>></td>
                            @if($guideline->banner_tag_id == null)
                            <td></td>
                            @else
                            <td>{{optional($guideline->banner_tag)->name}}</td>
                            @endif
                            <td>{{$guideline->start_date}}</td>
                            <td>{{$guideline->end_date}}</td>
                            <td><?php echo str_replace(
                                    array("2", "3", ','),
                                    array("User", "Collector", ", "),
                                    $guideline->target_role
                                ); ?></td>
                            <td>{{$guideline->status == 1? 'Published' : 'Draft'}}</td>
                            <td>
                                <a href="{{route('guideline.view', ['id' => $guideline->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                <a href="{{route('guideline.edit', ['id' => $guideline->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                <a href="{{route('guideline.delete', ['id' => $guideline->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>