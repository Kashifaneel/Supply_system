<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplyRequest;
use App\Models\PurchaseOrder;
use App\Models\Supply;
use App\Models\POItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplies = Supply::with(['purchaseOrder', 'user', 'items.poItem'])
            ->when(auth()->user()->role !== 'Admin', function ($query) {
                return $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Supplies/Index', [
            'supplies' => $supplies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $purchaseOrders = PurchaseOrder::with(['items' => function ($query) {
            $query->whereColumn('supplied', '<', 'quantity');
        }])
            ->where('status', '!=', 'Fully Supplied')
            ->when(auth()->user()->role !== 'Admin', function ($query) {
                return $query->where('user_id', auth()->id());
            })
            ->get();

        // If a specific PO is requested, filter to show only that PO
        if ($request->has('po')) {
            $purchaseOrders = $purchaseOrders->where('id', $request->get('po'));
        }

        return Inertia::render('Supplies/Create', [
            'purchaseOrders' => $purchaseOrders->values(),
            'selectedPO' => $request->get('po'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplyRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $supply = Supply::create([
            'purchase_order_id' => $data['purchase_order_id'],
            'user_id' => $data['user_id'],
            'supply_date' => $data['supply_date']
        ]);

        foreach ($data['items'] as $item) {
            $poItem = POItem::findOrFail($item['po_item_id']);
            $supplyQuantity = (int) $item['quantity'];

            if ($supplyQuantity <= 0 || $poItem->supplied + $supplyQuantity > $poItem->quantity) {
                throw ValidationException::withMessages([
                    'items' => "Invalid supply quantity for item: {$poItem->name}. Available: " . ($poItem->quantity - $poItem->supplied)
                ]);
            }

            $supply->items()->create([
                'po_item_id' => $poItem->id,
                'quantity' => $supplyQuantity,
            ]);

            // Update supplied quantity
            $poItem->increment('supplied', $supplyQuantity);
        }

        // Update PO status
        $purchaseOrder = $supply->purchaseOrder;
        $allItems = $purchaseOrder->items;
        
        if ($allItems->every(fn($item) => $item->supplied >= $item->quantity)) {
            $purchaseOrder->update(['status' => 'Fully Supplied']);
        } else {
            $purchaseOrder->update(['status' => 'Partially Supplied']);
        }

        // Generate PDFs
        $this->generatePDFs($supply);

        return redirect()->route('supplies.show', $supply)
            ->with('success', 'Supply recorded successfully and PDFs generated.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supply $supply)
    {
        $this->authorize('view', $supply);

        $supply->load(['purchaseOrder.items', 'user', 'items.poItem', 'payments']);

        return Inertia::render('Supplies/Show', [
            'supply' => $supply,
        ]);
    }

    /**
     * Upload stamped documents
     */
    public function uploadStamped(Request $request, Supply $supply)
    {
        $this->authorize('update', $supply);

        $request->validate([
            'dc_stamped' => 'nullable|file|mimes:pdf|max:5120',
            'invoice_stamped' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = [];

        if ($request->hasFile('dc_stamped')) {
            // Delete old file if exists
            if ($supply->dc_stamped) {
                Storage::disk('public')->delete($supply->dc_stamped);
            }
            $dc_stamped = $request->file('dc_stamped')->store('supplies/stamped', 'public');
            $supply->dc_stamped = $dc_stamped;
        }

        if ($request->hasFile('invoice_stamped')) {
            // Delete old file if exists
            if ($supply->invoice_stamped) {
                Storage::disk('public')->delete($supply->invoice_stamped);
            }
            $invoice_stamped = $request->file('invoice_stamped')->store('supplies/stamped', 'public');
            $supply->invoice_stamped = $invoice_stamped;
        }

        $supply->save();

        return back()->with('success', 'Documents uploaded successfully.');
    }

    /**
     * Generate DC and Invoice PDFs
     */
    private function generatePDFs(Supply $supply)
    {
        $supply->load(['purchaseOrder.items', 'items.poItem', 'user']);

        // Generate DC PDF
        $dcPdf = Pdf::loadView('pdf.dc', ['supply' => $supply]);
        $dcPath = "dc/DC-{$supply->id}-" . now()->format('Ymd') . ".pdf";
        Storage::disk('public')->put($dcPath, $dcPdf->output());

        // Generate Invoice PDF
        $invoicePdf = Pdf::loadView('pdf.invoice', ['supply' => $supply]);
        $invoicePath = "invoices/INV-{$supply->id}-" . now()->format('Ymd') . ".pdf";
        Storage::disk('public')->put($invoicePath, $invoicePdf->output());

        $supply->update([
            'dc_pdf' => $dcPath,
            'invoice_pdf' => $invoicePath
        ]);
    }
}
