<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePORequest;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['user', 'items' => function($query) {
                $query->selectRaw('*, price * quantity as total_amount');
            }])
            ->when(auth()->user()->role !== 'Admin', function ($query) {
                return $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('PurchaseOrders/Index', [
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('PurchaseOrders/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePORequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('po_image')) {
            $data['po_image'] = $request->file('po_image')->store('po_images', 'public');
        }

        $purchaseOrder = PurchaseOrder::create(Arr::except($data, 'items'));

        foreach ($data['items'] as $item) {
            $purchaseOrder->items()->create([
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'batch_no' => $item['batch_no'] ?? null,
                'mfg_date' => $item['mfg_date'] ?? null,
                'exp_date' => $item['exp_date'] ?? null,
                'total_amount' => $item['price'] * $item['quantity'],
            ]);
        }

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('view', $purchaseOrder);

        $purchaseOrder->load([
            'user',
            'items' => function($query) {
                $query->selectRaw('*, price * quantity as total_amount');
            },
            'supplies.items.poItem',
            'supplies.payments'
        ]);

        // Recalculate total amount for each item
        $purchaseOrder->items->each(function ($item) {
            $item->total_amount = $item->price * $item->quantity;
        });

        return Inertia::render('PurchaseOrders/Show', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('update', $purchaseOrder);

        $purchaseOrder->load('items');

        return Inertia::render('PurchaseOrders/Edit', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePORequest $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorize('update', $purchaseOrder);

        $data = $request->validated();

        if ($request->hasFile('po_image')) {
            $data['po_image'] = $request->file('po_image')->store('po_images', 'public');
        }

        $purchaseOrder->update(Arr::except($data, 'items'));

        // Get existing item IDs
        $existingItemIds = $purchaseOrder->items->pluck('id')->toArray();
        $updatedItemIds = [];

        // Update or create items
        foreach ($data['items'] as $itemData) {
            $itemId = $itemData['id'] ?? null;
            $itemAttributes = [
                'name' => $itemData['name'],
                'price' => $itemData['price'],
                'quantity' => $itemData['quantity'],
                'batch_no' => $itemData['batch_no'] ?? null,
                'mfg_date' => $itemData['mfg_date'] ?? null,
                'exp_date' => $itemData['exp_date'] ?? null,
                'total_amount' => $itemData['price'] * $itemData['quantity'],
            ];

            if ($itemId && in_array($itemId, $existingItemIds)) {
                // Update existing item
                $purchaseOrder->items()->where('id', $itemId)->update($itemAttributes);
                $updatedItemIds[] = $itemId;
            } else {
                // Create new item
                $newItem = $purchaseOrder->items()->create($itemAttributes);
                $updatedItemIds[] = $newItem->id;
            }
        }

        // Delete items that were not updated or created
        $purchaseOrder->items()
            ->whereNotIn('id', $updatedItemIds)
            ->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('delete', $purchaseOrder);

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully.');
    }
}