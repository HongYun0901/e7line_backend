<?php

namespace App\Http\Controllers;

use App\ConcatRecord;
use App\Order;
use App\OrderItem;
use App\Product;
use App\ProductRelation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderItemController extends Controller
{

    public static $order_item_status_names = ['尚未受理','已叫貨','已出貨','已交貨','已收單'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
//        dd($request);
        $status = 0;
        $sortBy_text = ['建單日期', '收件日期'];
        $user_filter = -1;
        $status_filter = -1;
        $sortBy = 'create_date';
        $query = OrderItem::query();
        $search_type = 0;
        $search_info = '';
        $products = Product::all();
        $users = User::all();
        $product_id = -1;
        $product_detail_id = -1;
        $product_relation = null;
        $date_from = null;
        $date_to = null;

        $query->join('orders','order_items.order_id','=','orders.id');
        $query->select('order_items.*','orders.user_id as user_id','order_items.status as status');
//        dd($query->first());

        //   get     sort
        if($request->has('sortBy')){
            $sortBy = $request->input('sortBy');
        }


//        user
        if($request->has('user_filter')){
            $user_filter = $request->input('user_filter');
        }
        if($user_filter>0) {
            $query->where('user_id','=',$user_filter);
        }

// status
        if ($request->has('status_filter')) {
            $status_filter = $request->input('status_filter');
        }
        if((int)$status_filter>=0){
            $query->where('order_items.status', '=', (int)$status_filter);
        }
//        product
        if ($request->has('product_id')) {
            $product_id = $request->query('product_id');
        }
        if($request->has('product_detail_id')){
            $product_detail_id = $request->query('product_detail_id');
        }
//      check if user select product ornot
//        dump($product_detail_id);
//        dd($product_id);
        if($product_detail_id != -1 || $product_id != -1){
            $query->join('product_relations','order_items.product_relation_id','=','product_relations.id');
            if($product_id!=-1 && $product_detail_id != -1){
                $query->where('product_id', '=', $product_id)
                    ->where('product_detail_id', '=', $product_detail_id);
            }
            else if($product_id!=-1 && $product_detail_id == -1){
                $query->where('product_id','=',$product_id);
            }
//            else{
//                $query->where('product_detail_id', '=', $product_detail_id);
//            }

        }
////        get the selected product
//        if($product_relation != null){
//            $query->where('product_relation_id','=',$product_relation->id);
//        }

//        date filter
        if($request->has('date_from')){
            $date_from = $request->input('date_from');
        }
        if($request->has('date_to')){
            $date_to = $request->input('date_to');
        }
        if($date_from != null && $date_to != null){
            $date_from_addtime = $date_from." 00:00:00";
            $date_to_addtime = $date_to. " 23:59:59";
            $query->whereBetween('orders.'.$sortBy,[$date_from_addtime,$date_to_addtime]);
        }

        if ($request->has('search_type')) {
            $search_type = $request->query('search_type');
        }
        if ($search_type > 0) {
            $search_info = $request->query('search_info');
            switch ($search_type) {
                case 1:
                    $query->where('orders.no', 'like', "%{$search_info}%");
                    break;
                case 2:
                    $query->join('customers','customers.id','=','orders.customer_id');
                    $query->where(function ($query) use ($search_info) {
                        $query->where('customers.name', 'like', "%{$search_info}%")
                            ->orWhere('orders.other_customer_name', 'like', "%{$search_info}%");
                        return $query;

                    });
                    break;
                case 3:
                    $query->join('business_concat_persons','business_concat_persons.id','=','orders.business_concat_person_id');
                    $query->where(function ($query) use ($search_info) {
                        $query->where('business_concat_persons.name', 'like', "%{$search_info}%")
                            ->orWhere('orders.other_concat_person_name', 'like', "%{$search_info}%");
                        return $query;
                    });
                    break;
                default:
                    break;
            }
        }





        $query->where('is_deleted','=',0);
        $query->orderBy($sortBy,'DESC');


        $order_items = $query->paginate(15);
//        dd($order_items);
//        $orders = Order::paginate(15);
        $data=[
            'products' => $products,
            'order_items' => $order_items,
            'order_item_status_names'=>self::$order_item_status_names,
            'status_filter' => $status_filter,
            'product_id'=>$product_id,
            'product_detail_id'=>$product_detail_id,
            'user_filter'=>$user_filter,
            'users'=>$users,
            'sortBy'=>$sortBy,
            'sortBy_text'=>$sortBy_text,
            'date_from'=>$date_from,
            'date_to'=>$date_to,
        ];

        return view('order_items.index',$data);
    }

//    public function compute_quantity(Request $request)
//    {
//        return $request;
//    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function change_item_status(Request $request)
    {
        if($request['ids']){
            $status = $request['status'];
            foreach($request['ids'] as $id){
                $order_item = OrderItem::find($id);
                $order_item->status = $status;
                $order_item->update_date = now();
                $order_item->update();

//                把訂單從為處理=>處理中

//              如果此筆大訂單之所有商品都已完成，訂單狀態自動變成完成
                $order = $order_item->order;
                $order_items = $order->order_items;
                $all_success_flag = true;
                foreach ($order_items as $o_i){
                    if($o_i->status !=4){
                        $all_success_flag = false;
                        break;
                    }
                }
                if($order->status == 0){
                    $order->status = 1;
                    $order->update();
                }
                if($all_success_flag){
                    $order->status = 2;
                    $order->update();
                }


            }
        }
        return "success";

    }
}