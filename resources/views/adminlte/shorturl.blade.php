@extends('adminlte.layout.app')

@section('title', 'ShortUrl')

@push('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>URL Shortener</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">URL Shortener</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">URL Shortener</h4>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form class="form-horizontal">
                            <div class="card-body">
                                <div id="successInsert"></div>
                                <div class="form-group row">
                                    <label for="title" class="col-sm-2 col-form-label">Title</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="title" placeholder="Title">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="url" class="col-sm-2 col-form-label">Url</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="url" placeholder="Url">
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-default">Cancel</button>
                                <button type="submit" class="btn btn-info float-right" id="addShortUrl">Add</button>
                            </div>
                            <!-- /.card-footer -->
                        </form>
                    </div>
                    <!-- /.card -->

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">DataTable with default features</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>URL</th>
                                        <th>Short URL</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Title</th>
                                        <th>URL</th>
                                        <th>Short URL</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>

                </div>
                <!--/.col (left) -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@push('scripts')
<!-- DataTables -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var colum = '<button class="btn btn-success" id="copyurl">copy</button>';
        var table = $("#example1").DataTable({
            "responsive": true,
            "autoWidth": false,
            'autoWidth': false,
            "ajax": {
                "url": "{{ route('urls') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "title"
                },
                {
                    "data": "url"
                },
                {
                    "data": "short_url"
                },
                {
                    "data": "created_at"
                },
                {
                    "data": ""
                }
            ],
            "columnDefs": [{
                "targets": -1,
                "data": null,
                "defaultContent": colum
            }]
        });

        $('#example1 tbody').on('click', '#copyurl', function() {
            var data = table.row($(this).parents('tr')).data();
            var shortUrl = document.createElement("input");
            shortUrl.setAttribute("value", data['short_url']);
            document.body.appendChild(shortUrl);
            shortUrl.select();
            shortUrl.setSelectionRange(0, 99999)
            document.execCommand("copy");
            document.body.removeChild(shortUrl);
        });

        $("#addShortUrl").click(function(e) {
            e.preventDefault();
            var title = $("#title").val();
            var url = $("#url").val();
            $("#addShortUrl").prop("disabled", true);
            $.ajax({
                type: "POST",
                url: "{{ route('store') }}",
                data: {
                    title: title,
                    url: url
                },
                timeout: 60000,
                success: function(data) {
                    $("#addShortUrl").prop("disabled", false);
                    successHtml = '<div class="alert alert-success alert-dismissible">';
                    successHtml += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-check"></i> Alert!</h4>';
                    successHtml += data.msg;
                    $('#successInsert').html(successHtml);
                    table.ajax.reload();
                },
                error: function(data) {
                    $("#addShortUrl").prop("disabled", false);
                    var errors = data.responseJSON;
                    errorsHtml = '<div class="alert alert-danger alert-dismissible">';
                    errorsHtml += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-ban"></i> Alert!</h4><ul>';
                    $.each(errors.msg, function(key, value) {
                        errorsHtml += '<li>' + value + '</li>';
                    });
                    errorsHtml += '</ul></div>';
                    $('#successInsert').html(errorsHtml);
                    //console.log(errors.msg);
                }
            });
        });
    });
</script>
@endpush