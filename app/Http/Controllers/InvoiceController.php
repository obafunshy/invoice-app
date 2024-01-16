<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Counter;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function get_all_invoice()
    {
        $invoices = Invoice::with('customer')->orderBy('id', 'DESC')->get();

        return response()->json([
            'invoices' => $invoices,
        ], 200);
    }

    public function search_invoice(Request $request)
    {
        $search = $request->get('s');

        $invoices = Invoice::with('customer')
            ->when($search, function ($query) use ($search) {
                return $query->where('id', 'LIKE', "%$search%");
            })
            ->get();

        return response()->json(['invoices' => $invoices], 200);
    }

    public function create_invoice()
    {
        $counter = Counter::where('key', 'invoice')->first();
        if (! $counter) {
            throw ValidationException::withMessages(['error' => 'Counter data not found']);
        }

        $latestInvoice = Invoice::orderBy('id', 'DESC')->first();
        $counters = $latestInvoice ? $latestInvoice->id + 1 : $counter->value;

        $formData = [
            'number' => $counter->prefix.$counters,
            'customer_id' => null,
            'customer' => null,
            'date' => now()->toDateString(),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'terms_and_conditions' => 'Default Terms and Conditions',
            'items' => [
                'product_id' => null,
                'product' => null,
                'unit_price' => 0,
                'quantity' => 1,
            ],
        ];

        return response()->json($formData);
    }

    public function add_invoice(InvoiceRequest $request)
    {
        $invoiceData = $request->only([
            'sub_total', 'total', 'customer_id', 'number', 'date', 'due_date',
            'discount', 'reference', 'terms_and_conditions',
        ]);

        $invoice = Invoice::create($invoiceData);

        $invoiceItems = json_decode($request->input('invoice_item'));

        if ($invoiceItems) {
            foreach ($invoiceItems as $item) {
                $itemdata['product_id'] = $item->id;
                $itemdata['invoice_id'] = $invoice->id;
                $itemdata['quantity'] = $item->quantity;
                $itemdata['unit_price'] = $item->unit_price;

                InvoiceItem::create($itemdata);
            }
        }
    }

    public function show_invoice($id)
    {
        $invoice = Invoice::with('customer', 'invoice_items.product')->findOrFail($id);

        return response()->json([
            'invoice' => $invoice,
        ], 200);
    }

    public function edit_invoice($id)
    {
        $invoice = Invoice::with('customer', 'invoice_items.product')->findOrFail($id);

        return response()->json([
            'invoice' => $invoice,
        ], 200);
    }

    public function delete_invoice_items($id)
    {
        $invoiceitem = InvoiceItem::findOrFail($id);
        $invoiceitem->delete();
    }

    public function update_invoice(InvoiceRequest $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->update($request->only([
            'sub_total', 'total', 'customer_id', 'number', 'date', 'due_date',
            'discount', 'reference', 'terms_and_conditions',
        ]));

        $invoiceItems = json_decode($request->input('invoice_item'));

        $invoice->invoice_items()->delete();

        foreach ($invoiceItems as $item) {
            $itemData = [
                'product_id' => $item->product_id,
                'invoice_id' => $invoice->id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ];

            InvoiceItem::create($itemData);
        }
    }

    public function delete_invoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->invoice_items()->delete();
        $invoice->delete();
    }
}
