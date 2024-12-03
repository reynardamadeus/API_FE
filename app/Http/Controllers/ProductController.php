<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function getProducts(){
        try{
            $products = ProductResource::collection(Product::all());

            return response()->json([
                'status' => 200,
                'message' => $products,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createProduct(Request $request){
        try{
            $validation = Validator::make( $request->all(), [
                'name' => 'required',
                'description' => 'required',
                'stock' => 'required|integer',
                'price' => 'required|integer',
                'image' => 'required|image|max:2048',
                'production_date' => 'required',
            ]);

            if($validation->fails()){
                $errors = array();

                foreach($validation->errors()->getMessages() as $key => $values)
                {
                    array_push($errors, $values[0]);
                }

                return response()->json([
                    'status' => 400,
                    'data' => [
                        'message' => $errors
                    ]
                    ], 400);
            }


            $imageName = $request->image->getClientOriginalName();
            $imagePath = $request->file('image')->storeAs('/', $imageName, 'public');
            $imagePath = '/storage/'. $imagePath;

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'stock' => $request->stock,
                'price' => $request->price,
                'image' => $imagePath,
                'production_date' => $request->production_date,
            ]);

            return response()->json([
                'status' => 200,
                'data' => [
                    'message' => 'Product Successfully Created',
                    'product' => new ProductResource($product)
                ]
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProduct(Request $request, $id){
        try{
            $product = Product::find($id);

            if(empty($product)){
                return response()->json([
                    'status' => 400,
                    'data' => [
                        'message' => "product not found"
                    ]
                ], 400);
            }

            $validation = Validator::make( $request->all(), [
            'name' => 'required',
            'description' => 'required',
            'stock' => 'required|integer',
            'price' => 'required|integer',
            'image' => 'required|image|max:2048',
            'production_date' => 'required',
            ]);

            if($validation->fails()){
                $errors = array();

                foreach($validation->errors()->getMessages() as $key => $values)
                {
                    array_push($errors, $values[0]);
                }

                return response()->json([
                    'status' => 400,
                    'data' => [
                        'message' => $errors
                    ]
                    ], 400);
            }

            $filePath = basename($product->image);
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            } else {
                return response()->json([
                    'status' => 400,
                    'data' => ['message' => 'Image not found']
                ], 400);
            }

            $imageName = $request->image->getClientOriginalName();
            $imagePath = $request->file('image')->storeAs('/', $imageName, 'public');
            $imagePath = '/storage/'. $imagePath;
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'stock' => $request->stock,
                'price' => $request->price,
                'image' => $imagePath,
                'production_date' => $request->production_date,
            ]);

            return response()->json([
                'status' => 200,
                'data' => [
                    'message' => 'Product Successfully Updated',
                    'product' => new ProductResource($product)
                ]
                ], 200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 500,
                'data' => [
                    'message' => $e->getMessage()
                    ]
            ], 500);
        }
    }

    public function deleteProduct($id){
        try{
            $product = Product::find($id);
            if(empty($product)){
                return response()->json([
                    'status' => 400,
                    'data' => [
                        'message' => "product not found"
                    ]
                ], 400);
            }

            $filePath = basename($product->image);
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            } else {
                return response()->json([
                    'status' => 400,
                    'data' => ['message' => 'Image not found']
                ], 400);
            }

            $product->delete();

            return response()->json([
                'status' => 200,
                'data' => [
                    'message' => 'Product Successfully Deleted'
                    ]
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'status' => 500,
                'data' => [
                    'message' => $e->getMessage()
                    ]
            ], 500);
        }
    }
}
