<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Incoming;
use Illuminate\Http\Request;

class IncomingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Incoming::with(['supplier', 'product'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'date_in' => 'required|date',
        ]);

        $incoming = Incoming::create($data);

        $product = Product::findOrFail($data['product_id']);
        $product->stock += $data['quantity']; 
        $product->save();

        return response()->json($incoming->load(['supplier', 'product']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $incoming = Incoming::findOrFail($id);
        return response()->json($incoming);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $incoming = Incoming::findOrFail($id);
        $data = $request->validate([
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'purchase_price' => 'sometimes|required|numeric|min:0',
            'date_in' => 'sometimes|required|date',
        ]);
        $oldQuantity = $incoming->quantity;

        $incoming->update($data);

        $product = Product::findOrFail($data['product_id']);

        if ($incoming->product_id != $incoming->getOriginal('product_id')) {
            $oldProduct = Product::find($incoming->getOriginal('product_id'));
            if ($oldProduct) {
                $oldProduct->stock -= $oldQuantity;
                $oldProduct->save();
            }
            $product->stock += $data['quantity'];
        } else {
            $product->stock += ($data['quantity'] - $oldQuantity);
        }

        $product->save();

        return response()->json($incoming->load(['supplier', 'product']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $incoming = Incoming::findOrFail($id);

        $product = Product::findOrFail($incoming->product_id);
        $product->stock -= $incoming->quantity;
        $product->save();
        
        $incoming->delete();
        return response()->json([
            'message' => 'Incoming record deleted successfully'
        ], 204);
    }
}
