<?php

namespace App\Http\Controllers\API\V1\Dashboard\Appointment;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use App\Models\Service\Service;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\CustomSelect\ClientResource;

class CustomAppointmentFilterController extends Controller
{
    public function getServices(Request $request)
    {
        $data = $request->validate([
            'search'=>'nullable|string'
        ]);
        if (!empty($data['search'])) {
            $services = Service::select('id','name','color')
                ->whereLike('name', $data['search'])
                ->get(['id as value', 'name as label']);
        } else {
            $services = Service::select('id','name','color')
                ->get(['id as value', 'name as label']);
        }

        return ApiResponse::success($services);
    }

    public function getClients(Request $request)
    {
        $data = $request->validate([
            'search'=>'nullable|string'
        ]);

        if (!empty($data['search'])) {
            $clients= Client::with('phones')->whereLike('name', $data['search'])->get();
        } else {
        $clients = Client::with('phones')->get();
        }
        return ApiResponse::success(ClientResource::collection($clients));
    }
}
