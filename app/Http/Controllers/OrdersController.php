<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Orders;
use App\Models\OrdersProducts;
use App\Models\Pharmacies;
use App\Models\PharmacyBranches;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * Display a listing of all orders except rejected.
     * @author @OxSama
     * @return \Illuminate\Http\Response
     */
    public function allOrdersExceptRejected()
    {
        $orders = Orders::where(
            [
            ['status',
            '=',
            'payment_confirmed'],
            ]
            )->orWhere([
            ['status',
            '=',
            'waiting']
            ]
            )->get();
        return response(
            $this->buildResponse($orders),
            200,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * Display a listing of orders with rejected.
     * @author @OxSama
     * @return \Illuminate\Http\Response
     */
    public function allOrders()
    {
        $orders = Orders::all();
        return response(
            $this->buildResponse($orders),
            201,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * Build a collection of objects in the form of
     * {
     *     "orderId": 1223234,
     *     "Date" : "11 November 2021",
     *     "status" : "waiting for review",
     *     "total" : 6700
     * }
     * @author @OxSama
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function buildResponse($orders)
    {

        $response = collect();
        foreach ($orders as $key => $order) {
            $response[$key] = collect([
                'orderId' => $order->id,
                'Date' => $this->formatDate($order->created_at),
                'status' => $order->status,
                'delivery' => $order->delivery,
                'vat' => $order->vat,
                'discount' => $order->discount,
                'total' => $this->calculateTotal($order->id)
            ]);
        }
        return $response;
    }

    /**
     * Format the created_at date into d F Y
     * @author @OxSama
     * @param String $date
     * @return String
     */
    private function formatDate($Date)
    {
        return (new Carbon($Date))->format('d F Y');
    }
    /**
     * Calculate the total amount for the order
     * @author @OxSama
     * @param int $orderId
     * @return int $total Amount
     */
    private function calculateTotal($orderId)
    {
        $ordersProductsTable = OrdersProducts::where('order_id', '=', $orderId)->get(['price', 'quantity']);
        $total = 0;
        foreach ($ordersProductsTable as $order) {
            $total += ($order->price * $order->quantity);
        }
        return $total;
    }

    /**
     * Display the order details for every order showing the whole products of the order.
     * @author @OxSama
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *     "productname": "Imodium-Instant-Melts",
     *     "pharmacy" : "Castro pharmacies",
     *     "QTY" : 2,
     *     "Unit Price":1200,
     *     "Total Amount" : 2400
     * }
     */
    public function show($id)
    {
        $orderProducts = OrdersProducts::where(
            'order_id',
            '=',
            $id
        )->get();
        $response = collect();
        // return $this->pharmacyBranchName($order[0]->pharmacy_branch_id);
        // return $orderProducts;
        foreach ($orderProducts as $key => $orderProduct) {
            $order = Orders::where(
                'id',
                '=',
                $id
            )->first();
            $response[$key] = collect([
                "productname" => Products::where('id', '=', $orderProduct->id)->first('name')->name,
                "pharmacy" => $this->pharmacyBranchName($order->pharmacy_branch_id),
                "QTY" => $orderProduct->quantity,
                "Unit Price" => $orderProduct->price,
                "Total Amount" => $orderProduct->price * $orderProduct->quantity
            ]);
        }
        return response(
            $response,
            201,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }
    /**
     * return the whole name of the pharmacy
     * @author @OxSama
     * @param  int  $id
     * @return string
     */
    private function pharmacyBranchName($branchId)
    {
        $pharmacyBranch = PharmacyBranches::where('id', '=', $branchId)->first(['name', 'pharmacy_id']);
        $pharmacy = Pharmacies::where('id', '=', $pharmacyBranch->pharmacy_id)->first('name');
        return $pharmacy->name . ' - ' . $pharmacyBranch->name;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $Orders = Orders::all();

        $response = collect();
        foreach ($Orders as $order) {

            $products = OrdersProducts::get('price')->first();
            $products = $products->price;

            /* $company=Company::where('id',$product->product->company)->get('name')->first();
            $company=$company->name;

            $order=Orders::get('type')->first();
            $order=$order->type;*/
            $data = [
                "id" => $order->id,
                "type" => $order->type,
                "price" => $products,
                "status" => $order->status,
                "handled_by" =>
                [
                    "id" => isset($order->employee) ? $order->employee->id : "",
                    "name" => isset($order->employee) ? $order->employee->fullname : ""
                ],
                "date" =>  $order->created_at,
                "payment" =>
                [
                    "method" => $order->payment_method,
                    "proof" => $order->payment_proof_screenshot
                ],
                "products" =>
                $order->products->map(function ($product) {
                    return [
                        "name" => $product->name,
                        "qty" => $product->pivot->quantity,
                        "price" => $product->pivot->price
                    ];
                }),
                "products_amount" => $order->products_amount,
                "discount" => $order->discount,
                "vat" => $order->vat,
                "delivery" => $order->delivery
            ];
            $response->push($data);
        }
        return $response;
    }
    /*{
        id: 23820,
        type: "local",
        price: 5400,
        status: "finished",
        handled_by: {
            id: 1,
            name: "Mustafa Salah",
        },
        date: "24-09-2021 12:34:03 PM",
        payment: {
            method: "cash",
            proof: "/assets/images/pay.jpg",
        },
        products: [
            {
                name: "Diarrhoea. Relief - Loperamide Capsules",
                qty: 1,
                price: 1200,
            },
            {
                name: "Ovex Family Pack Tablets",
                qty: 2,
                price: 1400,
            },
        ],
        products_amount: 5400,
        discount: 0,
        vat: 0,
        delivery: 0,
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // $handled_by_id = Employees::firstOrCreate([
        //     "id" => 
        // ]);

        // $handled_by_name = Employees::firstOrCreate([
        //     "fullname" => $request->input("name")
        // ]);
        //$company = Company::firstOrCreate(["name" => $request->input("company")]);    

        $data = [
            //"user_id" => $userid,
            "type" => $request->input("type"),
            'status' => $request->input("status"),
            "handled_by" => $request->input("handled_by")["id"],
            "date" => $request->input('date'),
            "products_amount" => $request->input('products_amount'),
            "discount" => $request->input('discount'),
            "vat" => $request->input('vat'),
            "payment_method" => $request->input('payment->method'),
            "delivery" => $request->input('delivery')
        ];

        $products = OrdersProducts::get('price')->first();
        $products = $products->price;

        if ($order = Orders::create($data)) {

            foreach ($request->input("products") as $product) {
                OrdersProducts::create([
                    "order_id" => $order->id,
                    "product_id" => $product["id"],
                    "quantity" => $product["qty"],
                    "price" => $product["price"],
                    "cost" => $product["cost"],
                ]);
            }

            return response([
                "id" => $order->id,
                "type" => $order->type,
                "status" => $order->status,
                "handled_by" =>
                [
                    "id" => $order->employee->id,
                    //"name" => $order->employee->user->fullname()
                ],
                "date" =>  $order->created_at,
                "payment" =>
                [
                    "method" => $order->payment_method,
                    "proof" => $order->payment_proof_screenshot
                ],
                "products" =>
                $order->products->map(function ($product) {
                    return [
                        "qty" => $product->pivot->pro,
                        "name" => $product->name,
                        "qty" => $product->pivot->quantity,
                        "price" => $product->pivot->price,
                        "cost" => $product->pivot->costs
                    ];
                }),
                "products_amount" => $order->products_amount,
                "discount" => $order->discount,
                "vat" => $order->vat,
                "delivery" => $order->delivery
            ], 200);
        } else {
            abort(500, "Database Error.");
        }
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
    public function updateStatus(Request $request, $id)
    {
        //

        $data = [
            "status" => $request->input("status"),
            "handled_by" => $request->input("employee_id")
        ];
        $status = Orders::where('id', $id)->first();
        if ($status->update($data)) {
            return response([
                "status" => $status->status
            ], 200);
        } else {
            abort(500, "Database Error.");
        }
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
}
