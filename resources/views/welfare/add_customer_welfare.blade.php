@extends('layouts.master')

@section('title', '新增客戶福利')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                編輯
                <small></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{route('welfare_status.index')}}"><i class="fa fa-shopping-bag"></i> 客戶福利</a></li>
                <li class="active">新增客戶福利</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">

            <!--------------------------
              | Your Page Content Here |
              -------------------------->
            <div class="container">

                <form class="well form-horizontal" action="{{route('welfare_status.update_customer_welfare')}}"
                      method="post">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h4><i class="icon fa fa-ban"></i> 錯誤！</h4>
                            請修正以下表單錯誤：
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <fieldset>


                        <div class="form-group">
                            <label class="col-md-4 control-label">客戶名稱</label>
                            <div class="col-md-4 inputGroupContainer">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <select id="select_customer" placeholder="Select a customer" name="customer_id">
                                        <option value="">Select a customer...</option>
                                        @foreach($customers as $customer)

                                            <option value="{{$customer->id}}">{{$customer->name}}</option>
                                        @endforeach
                                    </select>
                                    <script>
                                        var customer_select_id;
                                        $('#select_customer').selectize({
                                            onChange: function (value) {
                                                customer_select_id = value;
                                                console.log(customer_select_id);
                                                $.ajax({
                                                    url: '/ajax/get_customer_welfare_status',
                                                    data: {customer_select_id: customer_select_id}
                                                })
                                                    .done(function (res) {
                                                        console.log(res);
                                                        const myNode = document.getElementById("dynamic_welfare_status");
                                                        myNode.innerHTML = '';
                                                        html = '<select id="welfare_status_id" name="welfare_status_id" class="form-control" onchange="welfare_purpose_onchange(this)">';
                                                        html += '<option value=-1>請選擇福利目的</option>'
                                                        for (let [key, value] of Object.entries(res)) {
                                                            html += '<option value=\"' + key + '\">' + value + '</option>'

                                                        }
                                                        html += '</select>'
                                                        $('#dynamic_welfare_status').append(html);
                                                    })
                                            }
                                        });
                                        function welfare_purpose_onchange(selectObj){
                                            console.log(selectObj.options[selectObj.selectedIndex].value);
                                            var selected_value = selectObj.options[selectObj.selectedIndex].value;
                                            document.getElementById('welfare_status_id').value = selected_value;

                                            $.ajax({
                                                url: '/ajax/get_welfare_purpose_budget_status',
                                                data: {welfare_status_id: selected_value}
                                            })
                                                .done(function (res) {
                                                    console.log(res);
                                                    console.log(res.budget);
                                                    document.getElementById('budget').value = res.budget;
                                                    var selectObj = document.getElementById("status");
                                                    selectObj.options[res.track_status].selected = true;

                                                })
                                        }

                                    </script>
{{--                                    <input id="welfare_status_id" name="welfare_status_id" hidden="true" type="text" value="-1">--}}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">目的</label>
                            <div class="col-md-4 inputGroupContainer">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <div id="dynamic_welfare_status">
                                        <select disabled name="welfare_status_id" id="welfare_status_id" class="form-control">
                                            <option value="-1">請選擇福利目的</option>
                                        </select>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">福利類別</label>
                            <div class="col-md-4 selectContainer">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                                    <select id="welfare_type_select" name="welfare_types[]" class="form-control"
                                            multiple>
                                        @foreach($welfare_type_names as $welfare_type_name)
                                            <option @if($welfare_type_name->is_deleted) disabled
                                                    @endif value="{{ $welfare_type_name->id}}"> {{ $welfare_type_name->name }}</option>
                                        @endforeach
                                    </select>
                                    <script>
                                        $(function () {
                                            $("#welfare_type_select").attr("size", $("#welfare_type_select option").length);
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-4 control-label">預算</label>
                            <div class="col-md-4 inputGroupContainer">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                                    <input type="text" class="form-control" name="budget" id="budget"
                                           placeholder="請輸入預算" value="{{ old('budget') }}">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-4 control-label">交易狀況</label>
                            <div class="col-md-4 selectContainer">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                                    <select id="status" name="status" class="form-control selectpicker">

                                        @foreach(range(0,count($status_names)-1) as $st_id)
                                            <option
                                                value="{{ $st_id }}"{{ (old('st_id') == $st_id)? ' selected' : '' }}>{{ $status_names[$st_id] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label"></label>
                            <div class="col-md-4">
                                <a class="btn btn-danger" href="{{ URL::previous() }}">取消</a>
                                <button type="submit" class="btn btn-primary">更新</button>
                            </div>
                        </div>

                    </fieldset>
                </form>
            </div>

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
