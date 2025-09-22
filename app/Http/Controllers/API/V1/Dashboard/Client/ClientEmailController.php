<?php

namespace App\Http\Controllers\API\V1\Dashboard\Client;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Utils\PaginateCollection;
use App\Http\Controllers\Controller;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Services\Client\ClientEmailService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Client\ClientEmails\ClientEmailResource;
use App\Http\Requests\Client\ClientEmail\CreateClientEmailRequest;
use App\Http\Requests\Client\ClientEmail\UpdateClientEmailRequest;
use App\Http\Resources\Client\ClientEmails\AllClientEmailResource;
use App\Http\Resources\Client\ClientEmails\AllClientEmailCollection;

class ClientEmailController extends Controller implements HasMiddleware
{
    protected $clientEmailService;
 public function __construct(ClientEmailService $clientEmailService)
 {
     $this->clientEmailService = $clientEmailService;
 }
 public static function middleware(): array
 {
     return [
         new Middleware('auth:api'),
         new Middleware('permission:all_client_emails', only:['index']),
         new Middleware('permission:create_client_email', only:['create']),
         new Middleware('permission:edit_client_email', only:['edit']),
         new Middleware('permission:update_client_email', only:['update']),
         new Middleware('permission:destroy_client_email', only:['destroy']),
     ];
 }
    public function index(int $clientId,Request $request)
    {
        try{
                $ClientEmail = $this->clientEmailService->allClientEmails($clientId);
                return ApiResponse::success(AllClientEmailResource::collection($ClientEmail));
        }
        catch (ModelNotFoundException $th) {
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error(__('crud.server_error'), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
    public function store(int $clientId,CreateClientEmailRequest $createClientEmailRequest)
    {
        try{
            $this->clientEmailService->createClientEmail($clientId,$createClientEmailRequest->validated());
            return ApiResponse::success([], __('crud.created'), HttpStatusCode::CREATED);
        }catch(ModelNotFoundException){
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Exception $e) {
            return ApiResponse::error(__('crud.server_error'), $e->getMessage(), HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
    public function show(int $clientId,int $emailId,)
    {
        try {
            $clientEmail = $this->clientEmailService->editClientEmail($clientId, $emailId);
            return ApiResponse::success(new ClientEmailResource($clientEmail));
        }catch (ModelNotFoundException $th) {
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error(__('crud.server_error'), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }



    }
    public function update(int $clientId,int $emailId,UpdateClientEmailRequest $updateClientEmailRequest)
    {
        try{
        $ClientEmail = $this->clientEmailService->updateClientEmail($clientId, $emailId,$updateClientEmailRequest->validated());
        return ApiResponse::success([], __('crud.updated'));
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error( __('crud.not_found'),[], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error(__( 'crud.server_error'), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
    public function destroy(int $clientId,int $emailId,)
    {
        try{
          $this->clientEmailService->deleteClientEmail($clientId,$emailId);
          return ApiResponse::success([], __('crud.deleted'), HttpStatusCode::OK);
        }catch (ModelNotFoundException $e) {
        return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Exception $e) {
        return ApiResponse::error(__('crud.server_error'), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
    public function restore(int $clientId,int $emailId,)
    {
        try{
          $this->clientEmailService->restoreClientEmail($clientId,$emailId);
          return ApiResponse::success([], __('crud.restored'), HttpStatusCode::OK);
        }catch (ModelNotFoundException $e) {
        return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Exception $e) {
        return ApiResponse::error(__('crud.server_error'), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
    public function forceDelete(int $clientId,int $emailId,)
    {
        try{
          $this->clientEmailService->forceDeleteClientEmail($clientId,$emailId);
          return ApiResponse::success([], __('crud.deleted'), HttpStatusCode::OK);
        }catch (ModelNotFoundException $e) {
        return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Exception $e) {
        return ApiResponse::error(__('crud.server_error'), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
