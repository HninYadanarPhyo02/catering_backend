<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceDetail;

class InvoiceDetailController extends Controller
{
    public function destroy($invoice_id)
    {
        // Find the InvoiceDetail by id
        $detail = InvoiceDetail::findOrFail($invoice_id);

        // Get the related invoice_id before deleting
        $invoice_id = $detail->invoice_id;

        try {
            // Delete the detail
            $detail->delete();

            // Recalculate the total amount of the invoice after deletion
            Invoice::recalculateInvoiceTotal($invoice_id);

            return redirect()->back()->with('success', 'Invoice detail deleted and invoice total updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete invoice detail: ' . $e->getMessage());
        }
    }
}
