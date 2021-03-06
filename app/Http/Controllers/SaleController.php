<?php

namespace App\Http\Controllers;

use App\Sale;
use App\SalesItem;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $sales = Sale::orderBy('created_at', 'desc')->paginate(15);

        $data = [
            'sales' => $sales,
        ];
        return view('sales.index', $data);
    }

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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

    }
    public function showup()
    {
        //
        $sales = Sale::where('shipment',0)->orderBy('created_at', 'desc')->paginate(15);

        $data = [
            'sales' => $sales,
        ];
        return view('sales.up', $data);
    }
    public function showremove()
    {
        //
        $sales = Sale::where('shipment',1)->orderBy('created_at', 'desc')->paginate(15);

        $data = [
            'sales' => $sales,
        ];
        return view('sales.remove', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Sale $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        //

        $salesitems = $sale->salesitems;
        $data = [
            'sale' => $sale,
            'salesitems' => $salesitems,
//            'salesitems'=> $sale->salesitem(),

        ];

        return view('sales.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Sale $sale
     * @param SalesItem $salesitems
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
//        dd($request->all());
        //


        $this->validate($request, [
            'order_name' => 'required|max:60',
            'order_phone' => 'required|max:20',
            'order_address' => 'required|max:70',
            'order_note' => 'required|max:70',
            'quantity' => 'integer'
        ]);
        $sale->shipment = $request->shipment;
        $sale->update($request->all());
        foreach (array_keys($request->id) as $id){
            foreach ($sale->salesitems as $item){
                if($id == $item->id){
                   $item->quantity = $request->id[$id];
                   $item->update();
                }
            }
        }
        return redirect()->route('sales.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        //
        $sale->delete();

        return redirect()->route('sales.index');
    }
}
