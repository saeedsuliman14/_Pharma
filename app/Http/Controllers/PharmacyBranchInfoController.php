<?php

namespace App\Http\Controllers;

use App\Models\Addresses;
use App\Models\AtmCards;
use App\Models\BankAccounts;
use App\Models\Pharmacies;
use Illuminate\Http\Request;
use App\Models\PharmaciesPhoneNumbers;
use App\Models\PharmacyBranches;
use Illuminate\Support\Facades\DB;

class PharmacyBranchInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $pharmacyBranches = PharmacyBranches::all();
        //dd($pharmacyBranches[0]->bankAccount->account_no);
        // $pharmacyBranches = PharmacyBranches::with('bankAccount')->with('atmCard')->get();

        $response = collect();
        foreach ($pharmacyBranches as $pharmacyBranch) {

            $phone = PharmaciesPhoneNumbers::getPhoneNumbers($pharmacyBranch->id);


            $data = [
                'id' => $pharmacyBranch->id,
                'name' => $pharmacyBranch->pharmacy->name,
                'branch' => $pharmacyBranch->name,
                'phone_numbers' => $phone,
                "email" => $pharmacyBranch->email,
                "website" => $pharmacyBranch->website,
                'state' => $pharmacyBranch->address->state,
                'city' => $pharmacyBranch->address->city,
                'address' => $pharmacyBranch->address->address,
                "support_delivery" => $pharmacyBranch->support_delivery,
                "delivery_cost" => $pharmacyBranch->delivery_cost,
                "created_at" => $pharmacyBranch->created_at,
                "status" => $pharmacyBranch->status,
                "payment_options" =>
                [
                    "mbok" =>
                    [
                        "account_no" => isset($pharmacyBranch->bankAccount) ? $pharmacyBranch->bankAccount->account_no : "",
                        "account_owner_name" => isset($pharmacyBranch->bankAccount) ? $pharmacyBranch->bankAccount->account_owner_name : "",
                        "bank_branch_name" => isset($pharmacyBranch->bankAccount) ? $pharmacyBranch->bankAccount->bank_branch_name : ""
                    ],
                    "atm_card" =>
                    [
                        "card_no" => isset($pharmacyBranch->atmCard) ? $pharmacyBranch->atmCard->card_no : "",
                        "card_owner_name" => isset($pharmacyBranch->atmCard) ? $pharmacyBranch->atmCard->card_owner_name : "",
                        "bank_name" => isset($pharmacyBranch->atmCard) ?  $pharmacyBranch->atmCard->bank_name : ""
                    ],

                ]
            ];
            $response->push($data);
        }
        return $response;
    }
/*
        data: {
            id: 1,
            name: "CVS Pharmacy",
            branch: "Omdurman Branch",
            phone_numbers: ["+249965484820", "+249148392930"],
            email: "cvs-pharma@cvs-pharma.com",
            website: "http://www.cvs-pharma.com",
            state: "khartoum",
            city: "omdurman",
            address: "Alwadi Street, near Alrwdah hospital",
            support_delivery: false,
            delivery_cost: 0,
            payment_options: {
                mbok: {
                    account_no: "5678293",
                    account_owner_name: "Mustafa Salah Mustafa",
                    bank_branch_name: "sabren",
                },
                atm_card: {
                    card_no: "",
                    card_owner_name: "",
                    bank_name: "",
                },
            },
        }
*/
    private static function phoneNumsCutter($phone)
    {
        $phoneNums = '';
        foreach ($phone as $phoneNum) {
            if (!$phoneNums == '') {
                $phoneNums .= '   -   ';
            }
            $phoneNums .= $phoneNum->phone_number;
        }
        return $phoneNums;
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
        $pharmacy = PharmacyBranches::all();

        $address = Addresses::firstOrCreate([
            "state" => $request->input("state"),
            "city" => $request->input("city"),
            "address" => $request->input("address"),
            "latitude" => $request->input("lat"),
            "longitude" => $request->input("long")
        ]);

        $pharmacy_name = Pharmacies::firstOrCreate([
            "name" => $request->input("name")
        ]);

        $data = [
            'pharmacy_id' => $pharmacy_name->id,
            'name' => $request->input('branch'),
            "email" => $request->input('email'),
            "website" => $request->input('website'),
            "address_id" =>  $address->id
        ];

        if ($pharmacyBranch = PharmacyBranches::create($data)) {

            PharmaciesPhoneNumbers::setPharmacyPhoneNumbers($pharmacyBranch->id, $request->input("phone_numbers"));

            return response([
                'name' => $pharmacyBranch->pharmacy->name,
                'branch' => $pharmacyBranch->name,
                'phone_numbers' => PharmaciesPhoneNumbers::getPhoneNumbers($pharmacyBranch->id),
                "email" => $pharmacyBranch->email,
                "website" => $pharmacyBranch->website,
                'state' => $pharmacyBranch->address->state,
                'city' => $pharmacyBranch->address->city,
                'address' => $pharmacyBranch->address->address,
                "lat" => $pharmacyBranch->address->latitude,
                "long" => $pharmacyBranch->address->longitude
            ], 200);
        } else {
            abort(500, "Database Error.");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($type, $id)
    {
        //
        if ($type === "onwer") {
            return $this->getOwnerPharmacies($id);
        } else {
            return $this->getEmployeePharmacy($id);
        }
    }

    private function getEmployeePharmacy($id) {
        // employee implemention...
        $pharmacy_branch_id = DB::table('Employees')->where("user_id", $id)->value("pharmacy_branch_id");
        $pharmacyBranch = PharmacyBranches::findOrFail($pharmacy_branch_id);

        $response = collect();


        $phone = PharmaciesPhoneNumbers::getPhoneNumbers($pharmacyBranch->id);

        $data = [
            'id' => $pharmacyBranch->id,
            'name' => $pharmacyBranch->pharmacy->name,
            'branch' => $pharmacyBranch->name,
            'phone_numbers' => $phone,
            "email" => $pharmacyBranch->email,
            "website" => $pharmacyBranch->website,
            'state' => $pharmacyBranch->address->state,
            'city' => $pharmacyBranch->address->city,
            'address' => $pharmacyBranch->address->address,
            "lat" => $pharmacyBranch->address->latitude,
            "long" => $pharmacyBranch->address->longitude,
            "created_at" => $pharmacyBranch->created_at,
            "status" => $pharmacyBranch->status,
            "owned_by" => ["id" => $pharmacyBranch->pharmacy->ownedBy->id, "name" => $pharmacyBranch->pharmacy->ownedBy->fullName()]
        ];
        $response->push($data);

        return $response;
    }

    private function getOwnerPharmacies($id)
    {
        $pharmacyBranch = PharmacyBranches::all();

        $pharmacy_id = DB::table('pharmacies')->where("owner", $id)->value("id");

        $pharmacy_id = DB::table('pharmacies')->where("owner", $id)->value("id");   
        
        $pharmacyBranch= PharmacyBranches::where("pharmacy_id", $pharmacy_id)->first();

        $response=collect();

        $phone = PharmaciesPhoneNumbers::getPhoneNumbers($pharmacyBranch->id);

        $data = [
            'id' => $pharmacyBranch->id,
            'name' => $pharmacyBranch->pharmacy->name,
            'branch' => $pharmacyBranch->name,
            'phone_numbers' => $phone,
            "email" => $pharmacyBranch->email,
            "website" => $pharmacyBranch->website,
            'state' => $pharmacyBranch->address->state,
            'city' => $pharmacyBranch->address->city,
            'address' => $pharmacyBranch->address->address,
            "lat" => $pharmacyBranch->address->latitude,
            "long" => $pharmacyBranch->address->longitude,
            "created_at" => $pharmacyBranch->created_at,
            "status" => $pharmacyBranch->status,
            "owned_by" => ["id" => $pharmacyBranch->pharmacy->ownedBy->id, "name" => $pharmacyBranch->pharmacy->ownedBy->fullName()]
        ];
        $response->push($data);

        return $response;
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
    public function updatePaymentOption(Request $request, $type, $id)
    {
        //
        if ($type == "MBOK") {
            $bankInfo = BankAccounts::firstOrCreate([
                "account_no" => $request->input("account_no"),
                "account_owner_name" => $request->input("account_owner_name"),
                "bank_branch_name" => $request->input("bank_branch_name")

            ]);


            $data = [
                "bank_account_id" => $bankInfo->id
            ];
            $mbok = PharmacyBranches::where('id', $id)->first();
            if ($mbok->update($data)) {
                return response([
                    'account_no' => $mbok->bankAccount->account_no,
                    "account_owner_name" => $mbok->bankAccount->account_owner_name,
                    "bank_branch_name" => $mbok->bankAccount->bank_branch_name
                ], 200);
            } else {
                abort(500, "Database Error.");
            }
        } else {
            $atmInfo = AtmCards::firstOrCreate([
                "card_no" => $request->input("card_no"),
                "card_owner_name" => $request->input("card_owner_name"),
                "bank_name" => $request->input("bank_name")

            ]);


            $data = [
                "atm_card_id" => $atmInfo->id
            ];
            $atm = PharmacyBranches::where('id', $id)->first();
            if ($atm->update($data)) {
                return response([
                    'card_no' => $atm->atmCard->card_no,
                    "card_owner_name" => $atm->atmCard->card_owner_name,
                    "bank_name" => $atm->atmCard->bank_name
                ], 200);
            } else {
                abort(500, "Database Error.");
            }
        }
    }
    //"online_order" => $request->boolean("online_order")
    public function updateDeliveryOption(Request $request, $id)
    {
        $data = [
            "support_delivery" => $request->input("support_delivery"),
            "delivery_cost" => $request->input("delivery_cost")
        ];
        $delivery = PharmacyBranches::where('id', $id)->first();
        if ($delivery->update($data)) {
            return response([
                'support_delivery' => $delivery->support_delivery,
                "delivery_cost" => $delivery->delivery_cost
            ], 200);
        } else {
            abort(500, "Database Error.");
        }
    }

    public function updateVat(Request $request, $id)
    {
        $data = [
            "vat" => $request->input("vat")
        ];
        $vat = PharmacyBranches::where('id', $id)->first();
        if ($vat->update($data)) {
            return response([
                "vat" => $vat->vat
            ], 200);
        } else {
            abort(500, "Database Error.");
        }
    }

    public function updatePharmacyBranchInfo(Request $request, $id)
    {
        $pharmacy = PharmacyBranches::find($id);

        $address = Addresses::find($pharmacy->address_id)->update([
            "state" => $request->input("state"),
            "city" => $request->input("city"),
            "address" => $request->input("address"),
            "latitude" => $request->input("lat"),
            "longitude" => $request->input("long")
        ]);

        PharmaciesPhoneNumbers::setPharmacyPhoneNumbers($id, $request->input("phone_numbers"));

        $data = [
            'name' => $request->input('branch'),
            "email" => $request->input('email'),
            "website" => $request->input('website'),
        ];

        if ($pharmacy->update($data)) {

            return response([
                'name' => $pharmacy->pharmacy->name,
                'branch' => $pharmacy->name,
                'phone_numbers' => PharmaciesPhoneNumbers::getPhoneNumbers($id),
                "email" => $pharmacy->email,
                "website" => $pharmacy->website,
                'state' => $pharmacy->address->state,
                'city' => $pharmacy->address->city,
                'address' => $pharmacy->address->address,
                "lat" => $pharmacy->address->latitude,
                "long" => $pharmacy->address->longitude
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
