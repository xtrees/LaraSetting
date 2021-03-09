<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>设置管理 - {{ env('APP_NAME')}}</title>
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.0.7/vendor/fontawesome-free/css/all.min.css"
          rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
          rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.0.7/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
</head>
<body id="page-top">
<div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-laugh-wink"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3">{{ env('APP_NAME') }}</div>
                </a>
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
            </nav>
            <div class="container-fluid">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">配置项</h6>
                    </div>
                    <div class="card-body" style="min-height: 600px;">
                        <div class="row mb-3">
                            <div class="col-12">
                                <button class="btn btn-success float-right" data-toggle="modal"
                                        data-target="#group-create">
                                    <span class="icon "><i class="fas fa-object-group"></i></span>
                                    添加分组
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <div class="nav flex-column nav-pills" id="g-nav" aria-orientation="vertical">
                                    @foreach($groups as $gg)
                                        <a class="nav-link float-left {{ object_get($group,'key') == $gg->key? 'active':''}}"
                                           data-id="{{$gg->id}}"
                                           href="{{route('lara.setting.index',['group'=>$gg->key])}}">
                                            <i class="fas fa-arrows-alt handle"></i> &nbsp;&nbsp;
                                            {{$gg->title}}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="btn-group" role="group">
                                            <button id="group-edit" type="button" class="btn btn-outline-secondary">
                                                编辑分组
                                            </button>
                                            <button id="setting-create" data-group="{{ object_get($group,'key') }}"
                                                    type="button" class="btn btn-outline-info">新建配置
                                            </button>
                                            <button type="button" class="btn btn-outline-danger">删除分组</button>
                                        </div>
                                    </div>
                                </div>
                                @foreach($settings as $setting)
                                    <form>
                                        <input name="group" value="{{ $setting->group }}" type="hidden">
                                        <input name="key" value="{{ $setting->key }}" type="hidden">
                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label class="d-block">{{ $setting->title }}
                                                    <small>{{ $setting->fullKey }}</small>
                                                </label>
                                                @if($setting->type =='textarea')
                                                    <textarea class="form-control" title="" rows="5"
                                                              name="value">{{ $setting->value }}</textarea>
                                                @elseif($setting->type  =='bool')
                                                    <input name="value" type="hidden" value="0">
                                                    <input name="value" class="form-control"
                                                           type="checkbox" title="" value="1"
                                                           data-toggle="toggle" {{ $setting->value?'checked':'' }}>
                                                @else
                                                    <input name="value" class="form-control" title=""
                                                           type="text" value="{{ $setting->value }}">
                                                @endif
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="eager" class="d-block">预加载</label>
                                                <input type="hidden" name="eager" value="0">
                                                <input name="eager" class="form-control eager" title="" type="checkbox"
                                                       data-toggle="toggle" {{ $setting->eager?'checked':'' }} value="1">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="d-block">操作</label>
                                                <button type="button" class="btn btn-sm btn-danger single-delete">删除
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary single-save">保存
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    Copyright &copy;
                    <a href="https://github.com/xtrees/larasetting">
                        <span>LaraSetting</span>
                    </a>2018-{{ date('Y') }}
                    Theme by
                    <a href="https://startbootstrap.com/themes/sb-admin-2/">
                        <span>SB-ADMIN-2</span>
                    </a>
                </div>
            </div>
        </footer>
    </div>
</div>
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>
<div id="group-create" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加分组</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="group-create-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label>名称</label>
                        <input type="text" class="form-control" title="group_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Key</label>
                        <input type="text" class="form-control" title="group_key" name="key" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="group-create-save">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="setting-create-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">新建设置</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <input id="setting-create-group" type="hidden" name="group" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label>名称</label>
                        <input type="text" class="form-control" title="group_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Key</label>
                        <input type="text" class="form-control" title="group_key" name="key" required>
                    </div>
                    <div class="form-group">
                        <label>类型</label>
                        <select name="type" title="" class="form-control" id="setting-type-select">
                            <option value="text" selected>文本</option>
                            <option value="textarea">文本框</option>
                            <option value="bool">布尔值</option>
                        </select>
                    </div>
                    <div class="form-group" id="value-form-holder">
                        <label class="d-block">值</label>
                        <input id="value-form-field" type="text" class="form-control" name="value" required>
                    </div>

                    <label for="eager" class="d-block">预加载</label>
                    <input type="hidden" name="eager" value="0">
                    <input name="eager" class="form-control" title="" type="checkbox" data-toggle="toggle" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="setting-create-btn">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.0.7/vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.0.7/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Core plugin JavaScript-->
<script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.0.7/vendor/jquery-easing/jquery.easing.min.js"></script>
<!-- Custom scripts for all pages-->
<script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.0.7/js/sb-admin-2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.10.1/Sortable.min.js"></script>

<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
      rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap4-notify@4.0.3/bootstrap-notify.min.js"></script>
<!-- Page level custom scripts -->
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let sortable = Sortable.create(document.getElementById('g-nav'), {
        animation: 150,
        onEnd: function (/**Event*/evt) {
            $.ajax({
                url: "{{ route('lara.setting.group.sort') }}",
                type: "POST",
                data: {sort: sortable.toArray()},
                success: function (r) {
                    if (r.code !== 200) {
                        $.notify(r.msg, {type: 'danger'});
                    } else {
                        $.notify('更新成功');
                    }
                },
                complete: function () {
                }
            })
        },
    });
    //Update
    // $.notify('I have a progress bar', {showProgressbar: true});

    $('#group-create-save').click(function () {
        let btn = $(this);
        btn.attr('disabled', true);
        let data = btn.parents('form').serialize();

        $.ajax({
            url: "{{ route('lara.setting.group.create') }}",
            type: "PUT",
            data: data,
            success: function (r) {
                if (r.code !== 200) {
                    $.notify(r.msg, {type: 'warning'});
                } else {
                    $.notify('添加成功，1S后刷新页面');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                }
            },
            complete: function () {
                btn.removeAttr('disabled');
            }
        })
    });

    $('.single-save').click(function () {
        let btn = $(this);
        btn.attr('disabled', true);
        $.ajax({
            url: "{{ route('lara.setting.update') }}",
            type: "POST",
            data: btn.parents('form').serialize(),
            success: function (r) {
                if (r.code !== 200) {
                    $.notify(r.msg, {type: 'warning'});
                } else {
                    $.notify('更新成功');
                }
            },
            error: function () {
                $.notify('服务器挂壁了', {type: 'danger'});
            },
            complete: function () {
                btn.removeAttr('disabled');
            }
        })
    });

    $('#setting-create').click(function () {
        $('#setting-create-group').val($(this).data('group'));
        $('#setting-create-modal').modal('show');
        $('#setting-type-select').trigger('change');
    });
    $('#setting-type-select').change(function () {

        let txt = $('<input id="value-form-field" type="text" class="form-control" name="value" required>');
        let txtarea = $('<textarea id="value-form-field" class="form-control" name="value" required></textarea>');
        let bool = $('<select id="value-form-field" class="form-control" name="value">' +
            '<option value="1" selected>是</option>' +
            '<option value="0">否</option>' +
            '</select>');
        let holder = $('#value-form-holder');
        let type = $(this).val();

        $('#value-form-field').remove();
        if (type === 'textarea') {
            txtarea.appendTo(holder);
        } else if (type === 'bool') {
            bool.appendTo(holder);

        } else {
            txt.appendTo(holder)
        }
    });
    $('#setting-create-btn').click(function () {
        let data = $(this).parents('form').serialize();
        let btn = $(this);
        btn.attr('disabled', true);
        $.ajax({
            url: "{{ route('lara.setting.create') }}",
            type: "PUT",
            data: btn.parents('form').serialize(),
            success: function (r) {
                $('#setting-create-modal').modal('show');
                if (r.code !== 200) {
                    $.notify(r.msg, {type: 'warning'});
                } else {
                    $.notify('添加成功，1S后刷新页面');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                }
            },
            error: function () {
                $.notify('服务器挂壁了', {type: 'danger'});
            },
            complete: function () {
                btn.removeAttr('disabled');
            }
        })
    });
</script>
</body>
</html>
