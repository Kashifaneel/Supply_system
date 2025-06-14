<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Supply;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['supply.purchaseOrder', 'supply.user'])
            ->when(auth()->user()->role !== 'Admin', function ($query) {
                return $query->whereHas('supply', function ($q) {
                    $q->where('user_id', auth()->id());
                });
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $supplies = Supply::with(['purchaseOrder', 'payments'])
            ->when(auth()->user()->role !== 'Admin', function ($query) {
                return $query->where('user_id', auth()->id());
            })
            ->get()
            ->filter(function ($supply) {
                // Only show supplies that don't have confirmed payments
                return !$supply->payments->where('status', 'Confirmed')->count();
            });

        // If a specific supply is requested, filter to show only that supply
        if ($request->has('supply')) {
            $supplies = $supplies->where('id', $request->get('supply'));
        }

        return Inertia::render('Payments/Create', [
            'supplies' => $supplies->values(),
            'selectedSupply' => $request->get('supply'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cheque_image')) {
            $data['cheque_image'] = $request->file('cheque_image')->store('cheque_images', 'public');
        }

        $payment = Payment::create($data);

        return redirect()->route('payments.index')
            ->with('success', 'Payment submitted successfully. Awaiting admin confirmation.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['supply.purchaseOrder', 'supply.user', 'supply.items.poItem']);

        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    /**
     * Confirm payment (Admin only)
     */
    public function confirm(Payment $payment)
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Only admins can confirm payments.');
        }

        $payment->update(['status' => 'Confirmed']);

        return back()->with('success', 'Payment confirmed successfully.');
    }

    /**
     * Reject payment (Admin only)
     */
    public function reject(Payment $payment)
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Only admins can reject payments.');
        }

        $payment->delete();

        return back()->with('success', 'Payment rejected and removed.');
    }
}
