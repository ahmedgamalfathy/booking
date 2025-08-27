<?php
namespace App\Services\Client;
use App\Helpers\ApiResponse;
use App\Models\Client\Client;

class BulkActionService{
   public $clientService;
    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }
 public function bulkAction(array $validated){
        $query = Client::whereIn('id', $validated['ids']);
        switch ($validated['action']) {
            case 'changeType':
                $query->update(['param_id' => $validated['type']]);
                break;
            case 'delete':
                foreach ($query->get() as $client) {
                    $this->clientService->deleteClient($client->id);
                }
            break;
        }
        return ApiResponse::success([],"Users {$validated['action']}d successfully");
    }
}
