<?php

namespace App\Http\Controllers;

use App\Models\Categories;
<<<<<<< HEAD
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
=======
use App\Models\Company;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Storage;
>>>>>>> 26f4334637e0d1d2fa2ca67ce1c85cf1c82d1355

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
<<<<<<< HEAD
        $Drugs=Products::all();

        $response=collect();
        foreach($Drugs as $drug){

            $data=[
=======

        $Drugs = Products::all();



        $response = collect();
        foreach ($Drugs as $drug) {

            $data = [
>>>>>>> 26f4334637e0d1d2fa2ca67ce1c85cf1c82d1355
                "id" => $drug->id,
                "name" => $drug->name,
                'barcode' => $drug->barcode,
                "unit" => $drug->unit,
                "category" => $drug->categories->name,
                "company" =>  $drug->companies->name,
<<<<<<< HEAD
                "photo" => $drug->photo,
=======
                "photo" => ["url" => $drug->photo, "size" => 22222],
>>>>>>> 26f4334637e0d1d2fa2ca67ce1c85cf1c82d1355
                "ingredient" => $drug->ingredient,
                "need_perspection" => $drug->need_perspection,
                "description" => $drug->description,
                "usage" => $drug->usage,
                "warnings" => $drug->warnings,
                "side_effects" => $drug->side_effects
            ];
            $response->push($data);
        }
        return $response;
    }
    /*
        id: 1,
        name: "Diarrhoea. Relief - Loperamide Capsules",
        barcode: "1237980133840942",
        unit: "6 Capsules",
        category: "antibiotics",
        company: "Diarrhoea",
        photo: { url: "/assets/images/5.jpg", size: 123000 },
        ingredient: "loperamide hydrochloride",
        need_perspection: false,
        description: "",
        usage: "",
        warnings: "",
        side_effects: "",
    }
     */

<<<<<<< HEAD
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

=======
    public function uploadPhoto(Request $request)
    {

        $request->validate([
            'product_id' => 'required',
            'product_photo' => 'required|mimes:png,jpeg,jpg,gif,svg|max:2048',
        ]);

        $product_photo = $request->file("product_photo");

        if ($product_photo->isValid()) {

            //store file into images folder
            $file_name = $request->product_id . "." . $product_photo->extension();
            $url = "images/" . $file_name;

            $product_photo->storeAs('/public/images', $file_name);
            $drug = Products::find($request->product_id);
            $drug->photo = Storage::url($url); // + /storage/ + $url
            $drug->save();

            return response()->json([
                "url" => $drug->photo,
                "size" => Storage::size("public/images/" . $file_name),
            ]);
        }
    }


>>>>>>> 26f4334637e0d1d2fa2ca67ce1c85cf1c82d1355
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
<<<<<<< HEAD
=======
        $category = Categories::firstOrCreate(["name" => $request->input("category")]);
        $company = Company::firstOrCreate(["name" => $request->input("company")]);

        $data = [
            //"user_id" => $userid,
            "name" => $request->input("name"),
            'barcode' => $request->input("barcode"),
            "unit" => $request->input('unit'),
            "category" => $category->id,
            "company" =>  $company->id,
            "photo" => "",
            "ingredient" => $request->input('ingredient'),
            "need_prescreption" => $request->input('need_prescreption'),
            "description" => $request->input('description'),
            "usage_instructions" => $request->input('usage_instructions'),
            "warnings" => $request->input('warnings'),
            "side_effects" => $request->input('side_effects'),
        ];

        if ($product = Products::create($data)) {
            return response([
                "id" => $product->id,
                'name' => $product->name,
                "barcode" => $product->barcode,
                "unit" => $product->unit,
                "category" => $product->categories->name,
                "company" => $product->companies->name,
                "photo" => "",
                "ingredient" => $product->ingredient,
                "need_prescreption" => $product->need_prescreption,
                "description" => $product->description,
                "usage_instructions" => $product->usage_instructions,
                "warnings" => $product->warnings,
                "side_effects" => $product->side_effects
            ], 200);
        } else {
            abort(500, "Database Error.");
        }
>>>>>>> 26f4334637e0d1d2fa2ca67ce1c85cf1c82d1355
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

<<<<<<< HEAD
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
=======
>>>>>>> 26f4334637e0d1d2fa2ca67ce1c85cf1c82d1355

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
<<<<<<< HEAD
=======
        $category = Categories::firstOrCreate(["name" => $request->input("category")]);
        $company = Company::firstOrCreate(["name" => $request->input("company")]);

        $data = [
            "id" => $id,
            "name" => $request->input("name"),
            'barcode' => $request->input("barcode"),
            "unit" => $request->input('unit'),
            "category" => $category->id,
            "company" =>  $company->id,
            "photo" => "",
            "ingredient" => $request->input('ingredient'),
            "need_prescreption" => $request->input('need_prescreption'),
            "description" => $request->input('description'),
            "usage_instructions" => $request->input('usage_instructions'),
            "warnings" => $request->input('warnings'),
            "side_effects" => $request->input('side_effects'),
        ];
        $product = Products::where('id', $id)->first();
        if ($product->update($data)) {
            return response([
                'name' => $product->name,
                "barcode" => $product->barcode,
                "unit" => $product->unit,
                "category" => $product->categories->name,
                "company" => $product->companies->name,
                "photo" => "",
                "ingredient" => $product->ingredient,
                "need_prescreption" => $product->need_prescreption,
                "description" => $product->description,
                "usage_instructions" => $product->usage_instructions,
                "warnings" => $product->warnings,
                "side_effects" => $product->side_effects
            ], 200);
        } else {
            abort(500, "Database Error.");
        }
>>>>>>> 26f4334637e0d1d2fa2ca67ce1c85cf1c82d1355
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
<<<<<<< HEAD
=======
        if ($response = Products::destroy($id))
            return response(['id' => $id, 200]);

        else
            return response(['id' => $id, 400]);
>>>>>>> 26f4334637e0d1d2fa2ca67ce1c85cf1c82d1355
    }
}
