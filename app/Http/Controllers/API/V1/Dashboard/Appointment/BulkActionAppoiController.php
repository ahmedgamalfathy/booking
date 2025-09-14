<?php

namespace App\Http\Controllers\API\V1\Dashboard\Appointment;

use App\Http\Controllers\Controller;
use App\Services\Appointment\BulkActionAppService;
use Illuminate\Http\Request;

class BulkActionAppoiController extends Controller
{
    protected $bulkActionAppService;

    public function __construct(BulkActionAppService $bulkActionAppService)
    {
          $this->bulkActionAppService = $bulkActionAppService;
    }
    public function getMonthlyAvailability($serviceId ,Request $request)
    {
        $monthYear=$request->query('monthYear')??null;
        $availability = $this->bulkActionAppService->getMonthlyAvailability($serviceId,$monthYear);

        return response()->json($availability);
    }
    public function getAvailableSlots($serviceId , Request $request)
    {
       $data= $request->validate(['date' => 'required|date|date_format:Y-m-d']);
        $availability = $this->bulkActionAppService->getAvailableSlots($serviceId ,$data['date']);

        return response()->json($availability);
    }


}
