<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testCommitTransaction()
    {
        // Create a new transaction
        $transaction = Transaction::create();

        // Simulate network failure by mocking DB facade to throw an exception
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->never(); // Expecting commit to not be called
        DB::shouldReceive('rollback')->once(); // Expecting rollback to be called

        // Send request to commit the transaction
        $response = $this->post("/transactions/commit/{$transaction->id}");

        // Assert response status code
        $response->assertStatus(500);
        // Assert response content
        $response->assertJson(['error' => 'Failed to commit transaction']);
    }

    public function testRollbackTransaction()
    {
        // Create a new transaction
        $transaction = Transaction::create();

        // Simulate network failure by mocking DB facade to throw an exception
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('rollback')->never(); // Expecting rollback to not be called

        // Send request to rollback the transaction
        $response = $this->post("/transactions/rollback/{$transaction->id}");

        // Assert response status code
        $response->assertStatus(500);
        // Assert response content
        $response->assertJson(['error' => 'Failed to rollback transaction']);
    }

    public function testConcurrentTransactions()
    {
        // Create two new transactions
        $transaction1 = Transaction::create();
        $transaction2 = Transaction::create();

        // Simulate concurrent transactions by starting two transactions
        // and attempting to commit both at the same time
        $response1 = $this->post("/transactions/commit/{$transaction1->id}");
        $response2 = $this->post("/transactions/commit/{$transaction2->id}");

        // Assert that both transactions failed to commit
        $response1->assertStatus(500);
        $response2->assertStatus(500);
        // Assert response content
        $response1->assertJson(['error' => 'Failed to commit transaction']);
        $response2->assertJson(['error' => 'Failed to commit transaction']);
    }
}
