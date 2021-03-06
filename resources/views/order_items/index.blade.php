@extends('layouts.master')

@section('title', '訂單細項列表')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <a href="{{route('order_items.index')}}">訂單細項列表</a>
                <small></small>
            </h1>
            <ol class="breadcrumb">
                <li><i class="fa fa-shopping-bag"></i> 交易狀況</li>
                <li class="active">訂單細項列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">

            <!--------------------------
              | Your Page Content Here |
              -------------------------->

            <script>
                function product_change(product_select) {
                    var product_detail_id = '{{$product_detail_id}}';
                    console.log("product_detail_id");
                    console.log(product_detail_id);
                    var product_id = product_select.options[product_select.selectedIndex].value;
                    // console.log(product_id);
                    if (product_id > 0) {
                        $.ajax({
                            url: '/ajax/get_product_details',
                            data: {product_id: product_id}
                        })
                            .done(function (res) {
                                console.log(res);
                                var myNode;
                                myNode = document.getElementById("product_select_div");
                                // console.log(myNode);
                                myNode.innerHTML = '';
                                html = '<select class="form-control" id="product_detail_id" name="product_detail_id">';
                                html += '<option value=-1>請選擇產品</option>';
                                for (let [key, value] of Object.entries(res)) {
                                    html += '<option value=\"' + key + '\">' + value[0] + '</option>'
                                }
                                html += '</select>';
                                // console.log(myNode);
                                myNode.innerHTML = html;

                                var product_detail_select = document.getElementById("product_detail_id");
                                console.log(product_detail_select);
                                for (var i = 0; i < product_detail_select.options.length; i++) {
                                    if (product_detail_select.options[i].value == product_detail_id) {
                                        product_detail_select.selectedIndex = i;
                                        break;
                                    }
                                }
                            })

                    } else {
                        var product_detail_select = document.getElementById("product_detail_id");
                        // console.log(product_detail_select);
                        var i, L = product_detail_select.options.length - 1;
                        for (i = L; i > 0; i--) {
                            product_detail_select.remove(i);
                        }
                        product_detail_select.selectedIndex = 0;
                        // console.log("<0");
                        // console.log(product_detail_select);


                    }
                }

                {{--function computeQty() {--}}
                {{--    // var all_td = document.getElementsByName("quantity");--}}
                {{--    // console.log(all_td);--}}
                {{--    // var count = 0;--}}
                {{--    // for (var i = 0; i < all_td.length; i++) {--}}
                {{--    //     count += parseInt(all_td[i].innerText);--}}
                {{--    // }--}}
                {{--    // alert("總共數量為: " + count);--}}
                {{--    var request = window.location.search.substring(1);--}}
                {{--    // console.log(request);--}}
                {{--    $.ajax({--}}
                {{--        url: '{{route('order_items.compute_quantity')}}',--}}
                {{--        data: request,--}}
                {{--    })--}}
                {{--        .done(function (res) {--}}
                {{--            console.log(res);--}}

                {{--        })--}}

                {{--}--}}

            </script>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <div class="row">
                                <form name="filter_form" action="{{route('order_items.index')}}" method="get">
                                    <div class="col-md-1">
                                        <label>業務</label>
                                        <select name="user_filter" class="form-control form-control-sm">
                                            <option value="-1" @if($user_filter==-1) selected @endif>All</option>
                                            @foreach($users as $user)
                                                <option @if($user->id==$user_filter) selected
                                                        @endif value="{{$user->id}}">{{$user->name}}</option>
                                            @endforeach

                                        </select>


                                    </div>
                                    <div class="col-md-1">
                                        <label>細項狀態</label>
                                        <select name="status_filter" class="form-control form-control-sm">
                                            <option value="-1" @if(-1==$status_filter) selected
                                                @endif>All
                                            </option>
                                            @foreach($order_item_status_names as $status_name)
                                                <option @if($loop->index==$status_filter) selected
                                                        @endif value="{{$loop->index}}">{{$status_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>日期篩選</label>
                                        <input type="date" class="form-control" name="date_from"
                                               value="@if($date_from != null){{($date_from)}}@endif">
                                        至
                                        <input type="date" class="form-control" name="date_to"
                                               value="@if($date_to != null){{$date_to}}@endif">


                                    </div>
                                    <div class="col-md-2">
                                        <label>商品篩選</label>
                                        <select onchange="product_change(this)" id="product_select" name="product_id">
                                            <option value="-1">請選擇商品</option>
                                            @foreach($products as $product)
                                                <option value="{{$product->id}}">{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                        <script>
                                            var product_id = '{{$product_id}}';
                                            console.log("now product_id");
                                            console.log(product_id);
                                            var select = $("#product_select").selectize();
                                            select[0].selectize.setValue(product_id);
                                        </script>
                                        <div id="product_select_div">
                                            <select class="form-control" id="product_detail_select"
                                                    name="product_detail_id">
                                                <option selected value="-1">請選擇商品</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>排序方式 及 訂單種類</label>
                                        <select name="sortBy" class="form-control form-control-sm">
                                            @foreach(['create_date','receive_date'] as $col)
                                                <option @if($sortBy == $col) selected
                                                        @endif value="{{$col}}">{{$sortBy_text[$loop->index]}}</option>
                                            @endforeach
                                        </select>
                                        {{--                                        <br>--}}
                                        {{--                                        <label>訂單種類</label>--}}
                                        <select name="senao_order_filter" class="form-control " id="senao_order_filter">
                                            <option value="-1" @if($senao_order_filter==-1) selected @endif>所有訂單
                                            </option>
                                            <option value="0" @if($senao_order_filter==0) selected @endif>一般訂單
                                            </option>
                                            <option value="1" @if($senao_order_filter==1) selected @endif>神腦訂單
                                            </option>
                                        </select>
                                        {{--                                        <br>--}}
                                        <button type="submit" class=" btn btn-sm bg-blue" style="width: 100%">篩選
                                        </button>
                                    </div>

                                </form>
                                <div class="col-md-3">
                                    <!-- search form (Optional) -->
                                    <form roe="form" action="{{route('order_items.index')}}" method="get">
                                        <label>搜尋</label><br>

                                        <div class="form-inline">
                                            <select name="search_type" class="form-group form-control"
                                                    style="width: 100%;">
                                                <option value="1" @if(request()->get('search_type')==1) selected @endif>
                                                    訂單編號
                                                </option>
                                                <option value="2" @if(request()->get('search_type')==2) selected @endif>
                                                    客戶名稱
                                                </option>
                                                <option value="3" @if(request()->get('search_type')==3) selected @endif>
                                                    訂購人姓名
                                                </option>
                                            </select>

                                            <div class="inline">
                                                <input type="text" name="search_info" class="form-control"
                                                       style="width: 80%"
                                                       placeholder="Search..."
                                                       value="@if(request()->get('search_info')) {{request()->get('search_info')}} @endif">
                                                <button type="submit" id="search-btn" style="cursor: pointer"
                                                        style="width: 20%"
                                                        class="btn btn-flat"><i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <input hidden name="user_filter" value="{{$user_filter}}">
                                        <input hidden name="status_filter" value="{{$status_filter}}">
                                        <input hidden name="date_from" value="{{$date_from}}">
                                        <input hidden name="date_to" value="{{$date_to}}">
                                        <input hidden name="sortBy" value="{{$sortBy}}">
                                        <input hidden name="senao_order_filter" value="{{$senao_order_filter}}">


                                    </form>
                                    <!-- /.search form -->

                                </div>

                            </div>


                        </div>
                        <div id="detail_msg"></div>


                        <!-- /.box-header -->
                        <div class="box-body ">
                            <hr>
                            <table class="table table-bordered table-hover" width="100%">
                                <thead style="background-color: lightgray">
                                <tr>
                                    <input type="hidden" id="selected_count" value="0">
                                    <th id="show_selected" class="text-center" style="width:4%">0/{{count($order_items)}}</th>
                                    <th class="text-center" style="width:15%">Order</th>
                                    <th class="text-center" style="width:10%">建單日期</th>
                                    <th class="text-center" style="width:15%">Product(規格)</th>
                                    <th class="text-center" style="width:8%">Status</th>
                                    <th class="text-center" style="width:8%">Qty</th>
                                    <th class="text-center" style="width:8%">Amount</th>
                                    <th class="text-center" style="width:8%">Sales</th>
                                    <th class="text-center" style="width:8%">收貨日期</th>
                                    {{--                                    <th class="text-center" style="width:10%">Other</th>--}}
                                </tr>
                                </thead>

                                <script>
                                    function select_all() {
                                        var select_boxes = document.getElementsByName("change_item_status");
                                        for (var i = 0; i < select_boxes.length; i++) {
                                            select_boxes[i].checked = 1;
                                        }
                                        document.getElementById('selected_count').value = '{{count($order_items)}}';
                                        change_show_selected();
                                    }

                                    function unselect_all() {
                                        var select_boxes = document.getElementsByName("change_item_status");
                                        for (var i = 0; i < select_boxes.length; i++) {
                                            select_boxes[i].checked = 0;
                                        }
                                        document.getElementById('selected_count').value = 0;
                                        change_show_selected();
                                    }

                                    function go_to_detail(order_id) {
                                        // console.log(order_id);
                                        window.location.href = '/orders/' + order_id + '/detail';

                                        // console.log(window.location.href);
                                    }
                                    function change_show_selected(){
                                        var total = '{{count($order_items)}}';
                                        var selected = document.getElementById('selected_count').value;
                                        var str = selected + '/' + total;
                                        var show_selected_node = document.getElementById('show_selected');
                                        show_selected_node.text = str;
                                        console.log(show_selected_node.text);
                                        document.getElementById('show_selected').innerText = str;

                                    }

                                    function check_box_changed(check_box){
                                        var checked = check_box.checked;
                                        if(checked){
                                            document.getElementById('selected_count').value = parseInt(document.getElementById('selected_count').value) + 1;
                                        }
                                        else{
                                            document.getElementById('selected_count').value = parseInt(document.getElementById('selected_count').value) - 1;

                                        }
                                        change_show_selected();

                                    }

                                    function change_status() {
                                        var inputs = document.getElementsByName("change_item_status");
                                        var status_select = document.getElementById("status_select");
                                        var status = status_select.options[status_select.selectedIndex].value;
                                        console.log(status);
                                        var ids = [];
                                        for (var i = 0; i < inputs.length; i++) {
                                            if (inputs[i].checked ? 1 : 0) {
                                                ids.push(inputs[i].id);
                                            }
                                        }
                                        $.ajax({
                                            type: "POST",
                                            url: '/ajax/change_item_status',
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                                            },
                                            data: {
                                                ids: ids,
                                                status: status,
                                            },
                                            success: function (msg) {
                                                if (msg.success) {
                                                    if (msg.msg != '') {
                                                        alert(msg.msg);
                                                    }
                                                    window.location.reload();

                                                }
                                                console.log(msg)
                                            }
                                        });
                                    }

                                    function showDetail(msg) {
                                        // console.log(msg);
                                        html = '<div class="alert-success alert text-center">';
                                        html += msg;
                                        html += '</div>';
                                        var node = document.getElementById("detail_msg");
                                        node.innerHTML = html;
                                        // alert(msg);
                                    }

                                    function clearCountResult() {
                                        var node = document.getElementById("detail_msg");
                                        // node.style.display = "";
                                        node.innerHTML = "";
                                    }

                                    window.showDetail = showDetail;

                                </script>
                                <button onclick="select_all()" class="btn-sm btn-dark">全選</button>
                                <button onclick="unselect_all()" class="btn-sm btn-dark">取消全選</button>

                                <form action="{{route('senao_orders.exportItems')}}" method="post" name="exportForm"
                                      id="exportForm" onsubmit="return exportFormSubmit()">
                                    @csrf

                                <div style="float: right">
                                    <select id="status_select" name="status_select"
                                            class="select2-container float-right">
                                        @foreach($order_item_status_names as $order_item_status_name)
                                            <option value="{{$loop->index}}">{{$order_item_status_name}}</option>
                                        @endforeach
                                    </select>
                                    @if(Auth::user()->level >=1 )
                                        <button type="button" class="btn-sm btn-dark" onclick="change_status()">更改選中狀態</button>
                                    @endif
                                    <button type="button" class="btn-sm btn-dark" onclick="showDetail({{json_encode($msg)}})">顯示詳細數量
                                    </button>
                                    <button class="btn-sm btn-dark" type="submit">匯出叫貨單(神腦)
                                    </button>

                                </div>
                                <script>
                                    function exportFormSubmit() {
                                        var inputs = document.getElementsByName("change_item_status");
                                        var ids = [];
                                        for (var i = 0; i < inputs.length; i++) {
                                            if (inputs[i].checked ? 1 : 0) {
                                                ids.push(inputs[i].id);
                                            }
                                        }
                                        var get_code_type_select = document.getElementById('senao_order_filter');
                                        var get_code_type = get_code_type_select.options[get_code_type_select.selectedIndex].value;
                                        console.log(get_code_type);
                                        if ('{{$senao_order_filter}}' != 1 || get_code_type != 1) {
                                            alert('此功能只能適用神腦訂單，請先篩選訂單種類！');
                                            return false;
                                        }

                                        var exportForm = document.getElementById('exportForm');
                                        var input = document.createElement('input');//prepare a new input DOM element
                                        input.setAttribute('name', 'ids');//set the param name
                                        input.setAttribute('value', ids);//set the value
                                        input.setAttribute('type', 'hidden');//set the type, like "hidden" or other
                                        exportForm.appendChild(input);//append the input to the form
                                        return true;
                                    }
                                </script>




                                    @foreach ($order_items as $order_item)
                                        {{--                                    @if($order->is_deleted)--}}
                                        {{--                                        @continue--}}
                                        {{--                                    @endif--}}

                                        @php($order = $order_item->order)

                                        <tr ondblclick="go_to_detail({{$order->id}})" class="text-center">
                                            <td>
                                                <input type="checkbox" id="{{$order_item->id}}"
                                                       name="change_item_status" onchange="check_box_changed(this)">
                                            </td>

                                            <td class="text-left">#{{ $order->no}} &nbsp &nbsp
                                                @if($order->code)
                                                    <span style="color: red;font-weight: bold">
                                                {{$order->code}}
                                                </span>
                                                @endif


                                                <br>
                                                @if($order->email)
                                                    {{$order->email}}
                                                @else
                                                    no email
                                                @endif
                                                <br>
                                                by &nbsp &nbsp
                                                @if($order->business_concat_person)
                                                    {{$order->business_concat_person->name}}
                                                @else
                                                    {{$order->other_concat_person_name}}
                                                @endif
                                            </td>
                                            <td class="text-center">{{date("Y-m-d", strtotime($order->create_date))}}</td>
                                            <td>
                                                {{$order_item->product_relation->product->name}}
                                                {{$order_item->product_relation->product_detail->name}}
                                                @if($order_item->spec_name)
                                                    ({{$order_item->spec_name}})

                                                @endif

                                            </td>
                                            @switch($order_item->status)
                                                @case(0)
                                                @php($css='label label-danger')
                                                @break
                                                @case(1)
                                                @php($css='label label-warning')
                                                @break
                                                @case(2)
                                                @php($css='label label-info')
                                                @break
                                                @case(3)
                                                @php($css='label label-success')
                                                @break
                                                @case(4)
                                                @php($css='label label-primary')
                                                @break
                                                @default
                                                @break
                                            @endswitch
                                            <td class="align-middle " style="vertical-align: middle"><label
                                                    class="label{{$css}}"
                                                    style="min-width:60px;display: inline-block">{{ $order_item_status_names[$order_item->status] }}</label>

                                            <td name="quantity">{{round($order_item->quantity)}}</td>
                                            <td>${{$order_item->quantity * $order_item->price}}</td>
                                            <td>
                                                {{$order->user->name}}
                                            </td>
                                            <td>
                                                @if($order->receive_date)
                                                    {{date("Y-m-d", strtotime($order->receive_date))}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </form>
                            </table>
                        </div>

                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                            {{--                            {{ $concat_persons->appends(request()->input())->links() }}--}}
                            {{--                                {{$order_items->links()}}--}}
                            {{ $order_items->appends(request()->input())->links() }}
                        </div>
                    </div>

                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        {{--    </div>--}}
        <!-- /.row -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
