<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Expense::with(['customer', 'product'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'selling_price' => 'required|numeric|min:0',
            'date_out' => 'required|date',
        ]);
        $expense = Expense::create($data);
        $product = Product::findOrFail($data['product_id']);
        $product->stock -= $data['quantity'];
        $product->save();
        
        return response()->json($expense, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);
        $data = $request->validate([
            'customer_id' => 'sometimes|required|exists:suppliers,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'selling_price' => 'sometimes|required|numeric|min:0',
            'date_out' => 'sometimes|required|date',
        ]);
        $oldQuantity = $expense->quantity;
        $expense->product_id = $data['product_id'] ?? $expense->product_id;
        $expense->quantity = $data['quantity'] ?? $expense->quantity;
        $expense->selling_price = $data['selling_price'] ?? $expense->selling_price;
        $expense->customer_id = $data['customer_id'] ?? $expense->customer_id;
        $expense->date_out = $data['date_out'] ?? $expense->date_out;
        $product = Product::findOrFail($expense->product_id);

        if ($expense->product_id != $expense->getOriginal('product_id')) {
            // Product changed: remove from old, add to new
            $oldProduct = Product::find($expense->getOriginal('product_id'));
            if ($oldProduct) {
                $oldProduct->stock += $oldQuantity;
                $oldProduct->save();
            }
            $product->stock -= $data['quantity'];
        } else {
            // Same product: adjust by difference
            $product->stock -= ($data['quantity'] - $oldQuantity);
        }
        $product->save();
        $expense->update($data);
        return response()->json($expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        
        $product = Product::findOrFail($expense->product_id);
        $product->stock += $expense->quantity;
        $product->save();

        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully'
        ], 204);
    }
}
