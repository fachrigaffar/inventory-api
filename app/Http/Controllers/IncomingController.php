<?php

namespace App\Http\Controllers;

use App\Models\Incoming;
use Illuminate\Http\Request;

class IncomingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Incoming::all();
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
        return Incoming::create($data);
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
        $incoming->update($data);
        return response()->json($incoming);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $incoming = Incoming::findOrFail($id);
        $incoming->delete();
        return response()->json(['message' => 'Incoming record deleted successfully']);
    }
}
