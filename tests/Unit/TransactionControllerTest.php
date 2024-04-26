<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionControllerTest extends TestCase
{
    public function testStartTransaction()
    {
        $controller = new TransactionController();
        $request = new Request();

        $response = $controller->startTransaction($request);
        $responseData = $response->getData(true);

        $this->assertTrue($response->isOk());
        $this->assertArrayHasKey('transaction_id', $responseData);
        $this->assertNotNull($responseData['transaction_id']);
    }

    public function testCommitTransaction()
    {
        $controller = new TransactionController();
        $transactionId = 1; // Mock transaction ID
        $request = new Request();

        // Mock DB transaction methods
        DB::shouldReceive('beginTransaction');
        DB::shouldReceive('commit');

        $response = $controller->commitTransaction($request, $transactionId);
        $responseData = $response->getData(true);

        $this->assertTrue($response->isOk());
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Transaction committed successfully', $responseData['message']);
    }

    public function testRollbackTransaction()
    {
        $controller = new TransactionController();
        $transactionId = 1; // Mock transaction ID
        $request = new Request();

        // Mock DB transaction methods
        DB::shouldReceive('beginTransaction');
        DB::shouldReceive('rollback');

        // Mock logging
        Log::shouldReceive('error')->once();

        $response = $controller->rollbackTransaction($request, $transactionId);
        $responseData = $response->getData(true);

        $this->assertTrue($response->isOk());
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Transaction rolled back successfully', $responseData['message']);
    }
}
