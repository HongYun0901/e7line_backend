@extends('layouts.master')

@section('title', '編輯客戶福利')

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
                <li class="active">編輯客戶福利</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">

            <!--------------------------
              | Your Page Content Here |
              -------------------------->
            <div class="container">
                <script>
                    function check_select(){
                        var option_arr = document.getElementsByName("welfare_type_option");
                        // console.log(option_arr);
                        for (var i=0;i<option_arr.length;++i) {
                            // console.log(option);
                            option_arr[i].disabled=false;
                        }
                        // console.log(option_arr);
                        return true;

                    }
                </script>

                <form class="well form-horizontal" action="{{route('welfare_status.update',$welfare_status->id)}}" method="post" id="contact_form" onsubmit="return check_select()">

                    @csrf
                    @method('PATCH')

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
                                    <input type="text" class="form-control" id="title" name="name" placeholder="請輸入名稱"
                                           disabled
                                           value="{{ $welfare_status->customer->name }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">目的</label>
                            <div class="col-md-4 inputGroupContainer">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <input type="text" class="form-control" disabled
                                           value="{{ $welfare_status->welfare_name }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">福利類別</label>
                            <div class="col-md-4 selectContainer">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                                    @php($welfare_type_array = [])
                                    @foreach($welfare_status->welfare_types as $welfare_type)
                                        <?php
                                            array_push($welfare_type_array, $welfare_type->welfare_type_name_id)
                                        ?>
                                    @endforeach
                                    <select id="welfare_type_select" name="welfare_types[]" class="form-control" multiple>
                                        @foreach($welfare_type_names as $welfare_type_name)
                                            <option name="welfare_type_option" @if($welfare_type_name->is_deleted) disabled @endif value="{{ $welfare_type_name->id}}" @if( in_array($welfare_type_name->id,$welfare_type_array)) selected @endif> {{ $welfare_type_name->name }}</option>
                                        @endforeach
                                    </select>
                                    <script>
                                        $(function () {
                                            $("#welfare_type_select").attr("size",$("#welfare_type_select option").length);
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
                                    <input type="text" class="form-control"  name="budget"
                                           placeholder="請輸入預算" value="{{ old('budget', $welfare_status->budget) }}">
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
                                                value="{{ $st_id }}"{{ (old('$st_id', $welfare_status->track_status) == $st_id)? ' selected' : '' }}>{{ $status_names[$st_id] }}</option>
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
