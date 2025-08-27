<?php

namespace App\Http\Controllers\API\V1\Dashboard\Client;


use App\Services\Client\ClientAddressService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Client\ClientService;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Services\Client\ClientEmailService;
use App\Services\Client\ClientPhoneService;
use App\Http\Resources\Client\ClientResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\Client\AllClientCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientController extends Controller implements HasMiddleware
{
    protected  $clientService;
    protected  $clientEmailService;
    protected  $clientPhoneService;
    protected  $clientAddressService;

    public function __construct(ClientService $clientService ,ClientEmailService $clientEmailService,ClientPhoneService $clientPhoneService ,ClientAddressService $clientAddressService )
    {

        $this->clientEmailService = $clientEmailService;
        $this->clientPhoneService = $clientPhoneService;
        $this->clientService = $clientService;
        $this->clientAddressService = $clientAddressService;
    }
        public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            new Middleware('permission:all_clients', only:['index']),
            new Middleware('permission:create_client', only:['create']),
            new Middleware('permission:edit_client', only:['edit']),
            new Middleware('permission:update_client', only:['update']),
            new Middleware('permission:destroy_client', only:['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $clients = $this->clientService->allClients();
         return ApiResponse::success(new AllClientCollection($clients));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateClientRequest $createClientRequest)
    {
         try {
            DB::beginTransaction();
            $data = $createClientRequest->validated();
           $client= $this->clientService->createClient($data);
            if (!empty($data['emails'])) {
                foreach ($data['emails'] as $email) {
                    $this->clientEmailService->createClientEmail($client->id,$email);
                }
            }
            if (!empty($data['phones'])) {
                foreach ($data['phones'] as $phone) {
                    $this->clientPhoneService->createClientPhone($client->id,$phone);
                }
            }
            if (!empty($data['addresses'])) {
                foreach ($data['addresses'] as $address) {
                   $this->clientAddressService->createClientAddress($client->id,$address);
                }
            }
            DB::commit();

            return ApiResponse::success([],__('crud.created'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return ApiResponse::error(__('crud.server_error'),$th->getMessage(),HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $client = $this->clientService->editClient($id);
        if (!$client) {
            return apiResponse::error(__('crud.not_found'),[], HttpStatusCode::NOT_FOUND);
        }
        return ApiResponse::success(new ClientResource($client));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id,UpdateClientRequest $updateClientRequest)
    {
        try {
            DB::beginTransaction();
            $this->clientService->updateClient($id,$updateClientRequest->validated());
            DB::commit();
            return ApiResponse::success([],__('crud.updated'));
        }catch( ModelNotFoundException $e ){
            return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error(__('crud.server_error'),$th->getMessage(),HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
        $this->clientService->deleteClient($id);
        return ApiResponse::success([],__('crud.deleted'));
        }catch(ModelNotFoundException $e){
        return apiResponse::error(__('crud.not_found'),[], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
        return ApiResponse::error(__('crud.server_error'),$th->getMessage(),HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function restore($id){
        try {
            $this->clientService->restoreClient($id);
            return ApiResponse::success([],__('crud.restore'));
        }catch(ModelNotFoundException $e){
            return apiResponse::error(__('crud.not_found'),[], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error(__('crud.server_error'),$th->getMessage(),HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
    public function forceDelete($id)
    {
        try {
            $this->clientService->forceDeleteClient($id);
            return ApiResponse::success([],__('crud.deleted'));
        } catch(ModelNotFoundException $e){
            return apiResponse::error(__('crud.not_found'),[], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error(__('crud.server_error'),$th->getMessage(),HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
