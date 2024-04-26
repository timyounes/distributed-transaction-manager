<?php
// TransactionController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function startTransaction(Request $request)
    {
        // Create a new transaction record
        $transaction = Transaction::create();
        // Return the transaction ID to the client
        return response()->json(['transaction_id' => $transaction->id]);
    }

    public function commitTransaction(Request $request, $transactionId)
    {
        // Retrieve the transaction record
        $transaction = Transaction::findOrFail($transactionId);
        try {
            // Start transaction
            DB::beginTransaction();

            // Commit the transaction
            DB::commit();
            return response()->json(['message' => 'Transaction committed successfully']);
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollback();

            // Handle commit failure
            return response()->json(['error' => 'Failed to commit transaction', 'details' => $e->getMessage()], 500);
        }
    }

    public function rollbackTransaction(Request $request, $transactionId)
    {
        // Retrieve the transaction record
        $transaction = Transaction::findOrFail($transactionId);
        try {
            // Start transaction
            DB::beginTransaction();

            // Rollback the transaction
            DB::rollback();
            return response()->json(['message' => 'Transaction rolled back successfully']);
        } catch (\Exception $e) {
            // Log rollback failure
            Log::error('Failed to rollback transaction: ' . $e->getMessage());

            // Handle rollback failure
            return response()->json(['error' => 'Failed to rollback transaction', 'details' => $e->getMessage()], 500);
        }
    }
}
