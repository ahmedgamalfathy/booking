<?php

namespace App\Http\Controllers\API\V1\Dashboard\Client;



use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Utils\PaginateCollection;
use App\Http\Controllers\Controller;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Services\Client\ClientAddressService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Client\ClientAddress\ClientAddressResource;
use App\Http\Requests\Client\ClientAddress\CreateClientAddressRequest;
use App\Http\Requests\Client\ClientAddress\UpdateClientAddressRequest;
use App\Http\Resources\Client\ClientAddress\AllClientAddressCollection;

class ClientAddressController extends Controller
{
       protected $clientAddressService;
    public function __construct( ClientAddressService $clientAddressService)
    {
        $this->clientAddressService = $clientAddressService;
    }
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            new Middleware('permission:all_client_addresses', only:['index']),
            new Middleware('permission:create_client_address', only:['create']),
            new Middleware('permission:edit_client_address', only:['edit']),
            new Middleware('permission:update_client_address', only:['update']),
            new Middleware('permission:destroy_client_address', only:['destroy']),
        ];
    }
    public function index(int $clientId,Request $request)
    {
        try {
            $clientAddresses = $this->clientAddressService->allClientAddress( $clientId);
            return ApiResponse::success(new AllClientAddressCollection(PaginateCollection::paginate( $clientAddresses, $request->pageSize?$request->pageSize:10)));
        }catch(ModelNotFoundException $e){
           return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
          return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }

    public function show(int $clientId,int $addressId)
    {
        try{
            $clientAddress = $this->clientAddressService->editClientAddress($clientId, $addressId);
            return ApiResponse::success(new ClientAddressResource($clientAddress));
        }catch(ModelNotFoundException $e){
           return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
          return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
    public function store(int $clientId,CreateClientAddressRequest $createClientAddressRequest)
    {
        try {
            $this->clientAddressService->createClientAddress($clientId,$createClientAddressRequest->validated());
            return ApiResponse::success([], __('crud.created'), HttpStatusCode::CREATED);
        } catch (ModelNotFoundException$th) {
           return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
          return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
    public function update(int $clientId,int $addressId,UpdateClientAddressRequest $updateClientAddressRequest)
    {
        try{
             $this->clientAddressService->updateClientAddress($clientId,$addressId, $updateClientAddressRequest->validated());
        return ApiResponse::success([], __('crud.updated'));
        }catch(ModelNotFoundException $e){
           return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
          return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
    public function destroy(int $clientId,int $addressId)
    {
        try {
            $this->clientAddressService->deleteClientAddress($clientId,$addressId);
            return ApiResponse::success([], __('crud.deleted'));
        } catch(ModelNotFoundException $e){
            return ApiResponse::error(__('crud.not_found'), HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error(__('crud.server_error'),[], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
    public function restore(int $clientId,int $addressId)
    {
        try {
            $this->clientAddressService->restoreClientAddress($clientId,$addressId);
            return ApiResponse::success([], __('crud.restored'));
        } catch(ModelNotFoundException $e){
            return ApiResponse::error(__('crud.not_found'), HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error(__('crud.server_error'),[], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
    public function forceDelete(int $clientId,int $addressId)
    {
        try {
            $this->clientAddressService->forceDeleteClientAddress($clientId,$addressId);
            return ApiResponse::success([], __('crud.permanently_deleted'));
        } catch(ModelNotFoundException $e){
            return ApiResponse::error(__('crud.not_found'), HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error(__('crud.server_error'),[], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
