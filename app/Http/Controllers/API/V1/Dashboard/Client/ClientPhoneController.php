<?php

namespace App\Http\Controllers\API\V1\Dashboard\Client;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Utils\PaginateCollection;
use App\Http\Controllers\Controller;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Services\Client\ClientPhoneService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Client\ClientContact\ClientContactResource;
use App\Http\Requests\Client\ClientContact\CreateClientContactRequest;
use App\Http\Requests\Client\ClientContact\UpdateClientContactRequest;
use App\Http\Resources\Client\ClientContact\AllClientContactCollection;

class ClientPhoneController extends Controller implements HasMiddleware
{
        public $clientPhoneService;
     public function __construct(ClientPhoneService $clientPhoneService)
     {
         $this->clientPhoneService = $clientPhoneService;
     }
     public static function middleware(): array
     {
         return [
             new Middleware('auth:api'),
             new Middleware('permission:all_client_phones', only:['index']),
             new Middleware('permission:create_client_phone', only:['update']),
             new Middleware('permission:edit_client_phone', only:['edit']),
             new Middleware('permission:update_client_phone', only:['update']),
             new Middleware('permission:destroy_client_phone', only:['destroy']),
         ];
     }

    public function index(int $clientId,Request $request)
    {
        $clientPhones = $this->clientPhoneService->allClientPhones($clientId);
        return ApiResponse::success(new AllClientContactCollection(PaginateCollection::paginate($clientPhones, $request->pageSize?$request->pageSize:10)));
    }

    public function show(int $clientId,int $phoneId)
    {
        try{
        $clientPhone = $this->clientPhoneService->editClientPhone($clientId, $phoneId);
           return ApiResponse::success(new ClientContactResource($clientPhone));
        }catch(ModelNotFoundException $e){
            return apiResponse::error(__('crud.not_found'), HttpStatusCode::NOT_FOUND);
        }catch(\Throwable $th){
            return apiResponse::error(__('crud.server_error'), HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }

    public function store(int $clientId,CreateClientContactRequest $createClientContactRequest)
    {
        try{
            $this->clientPhoneService->createClientPhone($clientId,$createClientContactRequest->validated());
            return ApiResponse::success([], __('crud.created'),  HttpStatusCode::CREATED);
        }catch(\Throwable $th){
            return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::UNPROCESSABLE_ENTITY);
        }
    }

    public function update(int $clientId,int $phoneId,UpdateClientContactRequest $updateClientContactRequest)
    {
        try{
            $this->clientPhoneService->updateClientPhone($clientId,$phoneId, $updateClientContactRequest->validated());
            return ApiResponse::success([], __('crud.updated'));
        }catch(ModelNotFoundException $e){
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }
        catch(\Throwable $th){
            return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }

    public function destroy(int $clientId,int $phoneId)
    {
        try{
            $this->clientPhoneService->deleteClientPhone($clientId,$phoneId);
            return  ApiResponse::success([], __('crud.deleted'),  HttpStatusCode::OK);
        }catch(ModelNotFoundException $e){

            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch(\Throwable $th){
            return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
    public function restore(int $clientId,int $phoneId)
    {
        try{
            $this->clientPhoneService->restoreClientPhone($clientId,$phoneId);
            return  ApiResponse::success([], __('crud.restored'),  HttpStatusCode::OK);
        }catch(ModelNotFoundException $e){

            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch(\Throwable $th){
            return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
    public function forceDelete(int $clientId,int $phoneId)
    {
        try{
            $this->clientPhoneService->forceDeleteClientPhone($clientId,$phoneId);
            return  ApiResponse::success([], __('crud.deleted'),  HttpStatusCode::OK);
        }catch(ModelNotFoundException $e){

            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch(\Throwable $th){
            return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
