<?php

namespace App\Http\Controllers;

use App\Category;
use App\ConcatRecord;
use App\Customer;
use App\Member;
use App\Order;
use App\Sale;
use App\Type;
use App\WelfareStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

//        its for concat record
        $query = Customer::query();
        $query->join('concat_records','customers.id','=','concat_records.customer_id');
//        $query->join('business_concat_persons','business_concat_persons.customer_id','=','customers.id');

        if(Auth::user()->level==2){
            $query->Where('concat_records.status','=','1')->Where('concat_records.is_deleted','=',0);

        }
        else{
            $query->Where('customers.user_id','=',Auth::user()->id)->Where('concat_records.status','=','1')
            ->Where('concat_records.is_deleted','=',0);

        }

//        $query->orderBy('concat_records.update_date','ASC');
//        $query->orderBy('concat_records.track_date','ASC');



        $query->select('customers.id as customer_id','customers.name as customer_name',
            'concat_records.status as track_status','track_date','concat_records.track_content',
            'concat_records.id as concat_record_id'
        );

//        $query_future = clone $query;
//        $query_past  = clone $query;
////
//        $future_track_customers = $query_future->WhereNotNull('concat_records.track_date')
//            ->whereDate('concat_records.track_date', '>=', Carbon::now())
//            ->orderBy('concat_records.track_date', 'asc')
//            ->get();
//
//        $past_track_customers = $query_past->WhereNotNull('concat_records.track_date')
//            ->whereDate('concat_records.track_date', '<', Carbon::now())
//            ->orderBy('concat_records.track_date', 'desc')
//            ->get();
//
//
////        dump($future_track_customers);
////        dump($past_track_customers);
//
//        $customers = $future_track_customers->concat($past_track_customers);

        $query->orderBy('concat_records.track_date', 'asc');
        $customers = $query->get();

//        dd(count($customers));


        $count= count($customers);
        $rev = '6';
        $sums = ceil($count/$rev);
        session_start();
        $page = $request->input('page');
        if(empty($page)){
            $page = Session::get('dashboard_page');
            Session::remove('dashboard_page');
            if(empty($page)){
                $page = "1";
            }
        }
        session_write_close();
        $prev = ($page-1)>0?$page-1:1;
        $next = ($page+1)<$sums?$page+1:$sums;
        $offset = ($page-1)*$rev;


        $customers = $query->skip($offset)->limit($rev)->get();
//        $customers = $customers->slice($offset,$rev);


        $pp = array();
        for($i=1;$i<=$sums;$i++){
            $pp[$i]=$i;
        }





        //        its for welfare
        $query = WelfareStatus::query();
        $query->join('customers','welfare_status.customer_id','=','customers.id');
//        $query->join('business_concat_persons','business_concat_persons.customer_id','=','customers.id');

        if(Auth::user()->level==2){
            $query->Where('welfare_status.track_status','=','3');
        }
        else{
            $query->Where('customers.user_id','=',Auth::user()->id)->Where('welfare_status.track_status','=','3');
        }
        $query->orderBy('welfare_status.update_date','DESC')->orderBy('customer_id','DESC');

        $query->select('customers.id as customer_id','customers.name as customer_name','welfare_status.id',
            'welfare_status.track_status','budget','welfare_status.welfare_name');

        $welfare_stautses = $query->get();
//        dd($welfare_stautses);

//        $origin_welfare_statuses = WelfareStatus::findMany([]);

        $wcount= count($welfare_stautses);
        $wrev = '6';
        $wsums = ceil($wcount/$wrev);

        session_start();
        $wpage = $request->input('wpage');
        if(empty($wpage)){
            $wpage = Session::get('dashboard_wpage');
            Session::remove('dashboard_wpage');
            if(empty($wpage)){
                $wpage = "1";
            }
        }
        session_write_close();
        $wprev = ($wpage-1)>0?$wpage-1:1;
        $wnext = ($wpage+1)<$wsums?$wpage+1:$wsums;
        $woffset = ($wpage-1)*$wrev;
        $welfare_stautses = $query->skip($woffset)->limit($wrev)->get();



        $wpp = array();
        for($i=1;$i<=$wsums;$i++){
            $wpp[$i]=$i;
        }

//        its for maybe order
        $order_query = Order::query();
        $monthStart = now()->subMonth(1)->month;
        $monthEnd = now()->month;
        $order_query->whereYear('create_date','<',now()->startOfYear());
        $order_query->whereMonth('create_date','>=',$monthStart);
        $order_query->whereMonth('create_date','<=',$monthEnd);

        if(Auth::user()->level!=2){
            $order_query->where('user_id','=',Auth::user()->id);
        }

        $order_query->orderBy('create_date','DESC');
        $orders = $order_query->get();

        $ocount= count($orders);
        $orev = '6';
        $osums = ceil($ocount/$orev);

        session_start();
        $opage = $request->input('opage');
        if(empty($opage)){
            $opage = Session::get('dashboard_opage');
            Session::remove('dashboard_opage');
            if(empty($opage)){
                $opage = "1";
            }
        }
        session_write_close();
        $oprev = ($opage-1)>0?$opage-1:1;
        $onext = ($opage+1)<$osums?$opage+1:$osums;
        $ooffset = ($opage-1)*$orev;
        $orders = $order_query->skip($ooffset)->limit($orev)->get();
        $opp = array();
        for($i=1;$i<=$osums;$i++){
            $opp[$i]=$i;
        }
        $data = [
//            customers
            'customers' => $customers,
            'count' => $count,
            'rev' => $rev,
            'prev'=>$prev,
            'next'=>$next,
            'sums'=>$sums,
            'pp'=>$pp,
            'page'=>$page,
//            welfares
            'welfare_statuses'=>$welfare_stautses,
            'wcount' => $wcount,
            'wrev' => $wrev,
            'wprev'=>$wprev,
            'wnext'=>$wnext,
            'wsums'=>$wsums,
            'wpp'=>$wpp,
            'wpage'=>$wpage,
//            orders
            'orders'=>$orders,
            'ocount'=>$ocount,
            'orev' => $orev,
            'oprev'=>$oprev,
            'onext'=>$onext,
            'osums'=>$osums,
            'opp'=>$opp,
            'opage'=>$opage,
        ];
        return view('dashboard.index' , $data);
    }


    public function getoPage(Request $request)
    {
        //        its for maybe order
        $order_query = Order::query();


        $monthStart = now()->subMonth(1)->month;
        $monthEnd = now()->month;
        $order_query->whereYear('create_date','<',now()->startOfYear());
        $order_query->whereMonth('create_date','>=',$monthStart);
        $order_query->whereMonth('create_date','<=',$monthEnd);

        if(Auth::user()->level!=2){
            $order_query->where('user_id','=',Auth::user()->id);
        }
        $orderBy = 'create_date';
        if($request->has('orderBy')){
            $orderBy = $request->input('orderBy');
            if($orderBy == 'customer_id'){
                $order_query->orderBy($orderBy,'DESC')->orderBy('other_customer_name','DESC');
            }
            else{
                $order_query->orderBy($orderBy,'DESC');

            }
        }
        else{
            $order_query->orderBy($orderBy,'DESC');

        }
        $orders = $order_query->get();

        $ocount= count($orders);
        $orev = '6';
        $osums = ceil($ocount/$orev);

        $opage = Input::get('opage');
        $orderBy = Input::get('orderBy');
        $order_query->orderBy($orderBy,'DESC');

        if(empty($opage)){
            $opage = "1";
        }
        $oprev = ($opage-1)>0?$opage-1:1;
        $onext = ($opage+1)<$osums?$opage+1:$osums;
        $ooffset = ($opage-1)*$orev;
        $orders = $order_query->skip($ooffset)->limit($orev)->get();
        $opp = array();
        for($i=1;$i<=$osums;$i++){
            $opp[$i]=$i;
        }

        $res = '<table class="table table-bordered table-hover" style="width: 100%">
                                        <thead style="background-color: lightgray">
                                        <tr>
                                            <th class="text-center" style="width: 30%">客戶名稱</th>
                                            <th class="text-center" style="width: 15%">建單時間</th>
                                            <th class="text-center" style="width: 15%">目的</th>
                                            <th class="text-center" style="width: 15%">總金額</th>
                                            <th class="text-center" style="width: 30%">其他</th>
                                        </tr>
                                        </thead>';
        foreach($orders as $order){
            $res .= '<tr ondblclick="window.location.href = \'/orders/' . $order->id . '/detail\' " class="text-center">';
            if($order->customer){
                $res .= '<td>'.$order->customer->name.'</td>';
            }
            else{
                $res .= '<td>'.$order->other_customer_name.'</td>';
            }
            $res .= '<td>'.date('Y-m-d H:m',strtotime($order->create_date)).'</td>';
            $res .='<td>'. $order->welfare->welfare_name.'</td>';
            $res .= '<td>'.$order->amount.'</td>';
            $res .= '<td>其他</td>';
            $res .= '</tr>';
        }
        $res.= '</table>';

        $res .='<div class="page">';
        $res .='<!-------分页---------->' ;
        if($ocount > $orev){
            $res .= '<ul class="pagination">';
            if($opage != 1){
                $res .='<li >';
                $res .='<a href="javascript:void(0)" onclick="opage('.$oprev.')"><<</a>';
                $res .='</li>';
            }
            $flago = true;
            foreach($opp as $k=>$v){
                if($v == $opage){
                    $res .='<li class="active"><span>'.$v.'</span></li>';

                }
                elseif (abs($v-$opage)>=3 && $v<$opage){
                    if($v==1){
                        $res.='<li>
                                                            <a href="javascript:void(0)"
                                                               onclick="opage('.$v.')">'.$v.'</a>
                                                        </li>';
                    }
                    else{
                        if($flago){
                            $res.= '<li><span>...</span></li>';
                            $flago = false;
                        }

                    }
                }
                elseif(abs($v-$opage)>=3 && $v>$opage){
                    if($v==count($opp)){
                        $res.='<li>
                                                            <a href="javascript:void(0)"
                                                               onclick="opage('.$v.')">'.$v.'</a>
                                                        </li>';
                    }
                    else{
                        if($flago){
                            $res.= '<li><span>...</span></li>';
                            $flago = false;
                        }

                    }
                }
                else{
                    $flago = true;
                    $res .='<li >' ;
                    $res .='<a href="javascript:void(0)" onclick="opage('.$v.')">'.$v.'</a>' ;
                    $res .='</li>' ;
                }

            }
            if($opage != $osums){
                $res .= '<li>';
                $res .= "<a href='javascript:void(0)' onclick='opage(".$onext.")'>>></a>" ;
                $res .= '</li>';
            }
            $res .='</ul>';
        }
        return $res;

    }




    public function getPage(Request $request){

        $query = Customer::query();
        $query->join('concat_records','customers.id','=','concat_records.customer_id');
//        $query->join('business_concat_persons','business_concat_persons.customer_id','=','customers.id');
        if(Auth::user()->level==2){
            $query->Where('concat_records.status','=','1')->Where('concat_records.is_deleted','=',0);

        }
        else{
            $query->Where('customers.user_id','=',Auth::user()->id)->Where('concat_records.status','=','1')
                ->Where('concat_records.is_deleted','=',0);
        }
//        $query->orderBy('concat_records.update_date','DESC');
//        $query->orderBy('concat_records.track_date','ASC');

//        $query->select('customers.id as customer_id','customers.name as customer_name',
//            'concat_records.status as track_status','track_date','business_concat_persons.name as concat_person_name','concat_records.track_content',
//            'business_concat_persons.email as concat_person_email', 'business_concat_persons.phone_number as concat_person_phone_number',
//            'concat_records.id as concat_record_id'
//        );
        $query->select('customers.id as customer_id','customers.name as customer_name',
            'concat_records.status as track_status','track_date','concat_records.track_content',
            'concat_records.id as concat_record_id'
        );


//        $query_future = clone $query;
//        $query_past  = clone $query;
//        $future_track_customers = $query_future->WhereNotNull('concat_records.track_date')
//            ->whereDate('concat_records.track_date', '>=', Carbon::now())
//            ->orderBy('concat_records.track_date', 'asc')
//            ->get();
//
//        $past_track_customers = $query_past->WhereNotNull('concat_records.track_date')
//            ->whereDate('concat_records.track_date', '<', Carbon::now())
//            ->orderBy('concat_records.track_date', 'desc')
//            ->get();
//
//
////        dump($future_track_customers);
////        dump($past_track_customers);
//
//        $customers = $future_track_customers->concat($past_track_customers);
        $query->orderBy('concat_records.track_date', 'asc');
        $customers = $query->get();


//        $customers = $query->get();
        $count= count($customers);
        $rev = '6';
        $sums = ceil($count/$rev);
        $page = Input::get('page');
        if(empty($page)){
            $page = "1";
        }
        $prev = ($page-1)>0?$page-1:1;
        $next = ($page+1)<$sums?$page+1:$sums;
        $offset = ($page-1)*$rev;
//        $customers = $query->skip($offset)->limit($rev)->get();
        $customers = $customers->slice($offset,$rev);
        if(count($customers)==0 && $page>1){
            $page -= 1;
            $prev = ($page-1)>0?$page-1:1;
            $next = ($page+1)<$sums?$page+1:$sums;
            $offset = ($page-1)*$rev;
            $customers = $query->skip($offset)->limit($rev)->get();
//            $customers = $customers->slice($offset,$rev);
        }
        $pp = array();
        for($i=1;$i<=$sums;$i++){
            $pp[$i]=$i;
        }
        $res =  '<input hidden type="text" id="current_customer_page" value="'. $page . '">';

            $res .= ' <table class="table table-bordered table-hover">
                                    <thead style="background-color: lightgray" style="width: 100%">
                                    <tr>
                                        <th class="text-center" style="width: 5%">狀態</th>
                                            <th class="text-center" style="width: 30%">客戶名稱</th>
                                            <th class="text-center" style="width: 15%">Email</th>
                                            <th class="text-center" style="width: 15%">Concat Info</th>
                                            <th class="text-center" style="width: 15%">追蹤時間</th>

                                            <th class="text-center" style="width: 30%">Note</th>
                                    </tr>
                                    </thead>';
        foreach($customers as $customer){
            $res .= '<tr ondblclick="window.location.href = \'/customers/' . $customer->customer_id . '/record\' " class="text-center">';

            $res .= '<td><input type="checkbox" id="' . $customer->concat_record_id . '"
                                                           name="change_record_status" ></td>';
            $res .= '<td>'.$customer->customer_name.'</td>';
            $email = '-';
            if(\App\BusinessConcatPerson::where('customer_id','=',$customer->customer_id)->orderBy('update_date','DESC')->first()){
                if(\App\BusinessConcatPerson::where('customer_id','=',$customer->customer_id)->orderBy('update_date','DESC')->first()->email){
                    $phone_number = \App\BusinessConcatPerson::where('customer_id','=',$customer->customer_id)->orderBy('update_date','DESC')->first()->email;
                }
            }
            $res .= '<td>'.$email.'</td>';
            $phone_number = '-';
            if(\App\BusinessConcatPerson::where('customer_id','=',$customer->customer_id)->orderBy('update_date','DESC')->first()){
                if(\App\BusinessConcatPerson::where('customer_id','=',$customer->customer_id)->orderBy('update_date','DESC')->first()->phone_number){
                    $phone_number = \App\BusinessConcatPerson::where('customer_id','=',$customer->customer_id)->orderBy('update_date','DESC')->first()->phone_number;
                }
            }
            $res .= '<td>'.$phone_number.'</td>';
            $res .= '<td>' . $customer->track_date . '</td>';
            $res .= '<td>'.$customer->track_content.'</td>';
            $res .= '</tr>';
        }
        $res.= '</table>';

        $res .='<div class="page">';
        $res .='<!-------分页---------->' ;
        if($count > $rev){
            $res .= '<ul class="pagination">';
            if($page != 1){
                $res .='<li >';
                $res .='<a href="javascript:void(0)" onclick="page('.$prev.')"><<</a>';
                $res .='</li>';
            }
            $flag = true;
            foreach($pp as $k=>$v){
                if($v == $page){
                    $res .='<li class="active"><span>'.$v.'</span></li>';

                }
                elseif (abs($v-$page)>=3 && $v<$page){
                    if($v==1){
                        $res.='<li>
                                                            <a href="javascript:void(0)"
                                                               onclick="page('.$v.')">'.$v.'</a>
                                                        </li>';
                    }
                    else{
                        if($flag){
                            $res.= '<li><span>...</span></li>';
                            $flag = false;
                        }

                    }
                }
                elseif(abs($v-$page)>=3 && $v>$page){
                    if($v==count($pp)){
                        $res.='<li>
                                                            <a href="javascript:void(0)"
                                                               onclick="page('.$v.')">'.$v.'</a>
                                                        </li>';
                    }
                    else{
                        if($flag){
                            $res.= '<li><span>...</span></li>';
                            $flag = false;
                        }

                    }
                }
                else{
                    $flag = true;
                    $res .='<li >' ;
                    $res .='<a href="javascript:void(0)" onclick="page('.$v.')">'.$v.'</a>' ;
                    $res .='</li>' ;
                }

            }
            if($page != $sums){
                $res .= '<li>';
                $res .= "<a href='javascript:void(0)' onclick='page(".$next.")'>>></a>" ;
                $res .= '</li>';
            }
            $res .='</ul>';
        }
        return $res;
    }




    public function getwPage(Request $request){

        //        its for welfare
        $query = WelfareStatus::query();
        $query->join('customers','welfare_status.customer_id','=','customers.id');
//        $query->join('business_concat_persons','business_concat_persons.customer_id','=','customers.id');

        if(Auth::user()->level==2){
            $query->Where('welfare_status.track_status','=','3');
        }
        else{
            $query->Where('customers.user_id','=',Auth::user()->id)->Where('welfare_status.track_status','=','3');
        }
        $query->orderBy('welfare_status.update_date','DESC');

        $query->select('customers.id as customer_id','customers.name as customer_name',
            'welfare_status.track_status','budget','welfare_status.welfare_name','welfare_status.id as welfare_status_id');

        $welfare_statuses = $query->get();

        $wcount= count($welfare_statuses);
        $wrev = '6';
        $wsums = ceil($wcount/$wrev);

        $wpage = Input::get('wpage');
        if(empty($wpage)){
            $wpage = "1";
        }
        $wprev = ($wpage-1)>0?$wpage-1:1;
        $wnext = ($wpage+1)<$wsums?$wpage+1:$wsums;
        $woffset = ($wpage-1)*$wrev;
        $welfare_statuses = $query->skip($woffset)->limit($wrev)->get();
        $wpp = array();
        for($i=1;$i<=$wsums;$i++){
            $wpp[$i]=$i;
        }

        $res = ' <table class="table table-bordered table-hover">
                                    <thead style="background-color: lightgray">
                                    <tr>
                                        <th class="text-center" style="width: 150px">客戶名稱</th>
                                        <th class="text-center" style="width: 150px">福利目的</th>
                                        <th class="text-center" style="width: 150px">福利類別</th>
                                        <th class="text-center" style="width: 120px">預算</th>
                                    </tr>
                                    </thead>';
        foreach($welfare_statuses as $welfare_status){
            $res .= '<tr ondblclick="window.location.href = \'/welfare_status/' . $welfare_status->welfare_status_id . '/edit\' " class="text-center">';
            $res .= '<td>'.$welfare_status->customer_name.'</td>';
            $res .= '<td>'.$welfare_status->welfare_name.'</td>';
            $res .='<td>';

            foreach ($welfare_status->welfare_types as $welfare_type){
                $res.= $welfare_type->welfare_type_name->name;
            }
            $res.= '</td>';
            $res .= '<td>'.$welfare_status->budget.'</td>';
            $res .= '</tr>';
        }
        $res.= '</table>';

        $res .='<div class="page">';
        $res .='<!-------分页---------->' ;
        if($wcount > $wrev){
            $res .= '<ul class="pagination">';
            if($wpage != 1){
                $res .='<li >';
                $res .='<a href="javascript:void(0)" onclick="wpage('.$wprev.')"><<</a>';
                $res .='</li>';
            }
            $flag_w = true;
            foreach($wpp as $k=>$v){
                if($v == $wpage){
                    $res .='<li class="active"><span>'.$v.'</span></li>';

                }
                elseif (abs($v-$wpage)>=3 && $v<$wpage){
                    if($v==1){
                        $res.='<li>
                                                            <a href="javascript:void(0)"
                                                               onclick="wpage('.$v.')">'.$v.'</a>
                                                        </li>';
                    }
                    else{
                        if($flag_w){
                            $res.= '<li><span>...</span></li>';
                            $flag_w = false;
                        }

                    }
                }
                elseif(abs($v-$wpage)>=3 && $v>$wpage){
                    if($v==count($wpp)){
                        $res.='<li>
                                                            <a href="javascript:void(0)"
                                                               onclick="wpage('.$v.')">'.$v.'</a>
                                                        </li>';
                    }
                    else{
                        if($flag_w){
                            $res.= '<li><span>...</span></li>';
                            $flag_w = false;
                        }

                    }
                }
                else{
                    $flag_w = true;
                    $res .='<li >' ;
                    $res .='<a href="javascript:void(0)" onclick="wpage('.$v.')">'.$v.'</a>' ;
                    $res .='</li>' ;
                }

            }
            if($wpage != $wsums){
                $res .= '<li>';
                $res .= "<a href='javascript:void(0)' onclick='wpage(".$wnext.")'>>></a>" ;
                $res .= '</li>';
            }
            $res .='</ul>';
        }
        return $res;
    }


    public function set_concat_status(Request $request)
    {

        if($request['ids']){
            foreach($request['ids'] as $id){
                $concat_record = ConcatRecord::find($id);
                $concat_record->status = 0;
                $concat_record->update();

            }
        }

        return $request['page'];


    }

    public function setPageSession(Request $request)
    {
        if($request->has('page')){
            session_start();
            Session::remove('dashboard_page');
            Session::put('dashboard_page',$request->input('page'));
            session_write_close();  //<---------- Add this to close the session so that reading from the session will contain the new value.
            return 'success set page to session to' . $request->input('page');

        }
        if($request->has('wpage')){
            session_start();
            Session::remove('dashboard_wpage');
            Session::put('dashboard_wpage',$request->input('wpage'));
            session_write_close();  //<---------- Add this to close the session so that reading from the session will contain the new value.
            return 'success set page to session to' . $request->input('wpage');


        }
        if($request->has('opage')){
            session_start();
            Session::remove('dashboard_opage');
            Session::put('dashboard_opage',$request->input('opage'));
            session_write_close();  //<---------- Add this to close the session so that reading from the session will contain the new value.
            return 'success set page to session to' . $request->input('opage');
        }


    }











}

