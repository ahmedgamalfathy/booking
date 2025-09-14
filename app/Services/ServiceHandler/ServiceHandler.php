<?php
namespace App\Services\ServiceHandler;

use App\Enums\TypeEnum;
use App\Enums\StatusEnum;
use App\Enums\ActionStatusEnum;
use App\Models\Service\Service;
use Illuminate\Http\UploadedFile;
use App\Services\Time\TimeService;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filters\Service\FilterService;
use App\Services\Upload\UploadService;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceHandler
{
    protected $uploadService;
    protected $timeService;
    public function __construct(UploadService $uploadService ,TimeService $timeService)
    {
        $this->uploadService = $uploadService;
        $this->timeService = $timeService;
    }
    public function allServices()
    {
        $services= QueryBuilder::for(Service::class)
           ->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::custom('search', new FilterService()), // Add a custom search filter
                AllowedFilter::exact('status'),
                AllowedFilter::exact('type')
            ])
            ->orderBy('created_at', 'desc')
            ->get();
            return $services;

    }
    public function editServices(int $id)
    {
       $service= Service::with('times','exceptions')->find($id);
        if(!$service){
            throw new ModelNotFoundException();
        }
       return $service;
    }
    public function createService(array $data)
    {
        $Path = null;
        if(isset($data['path']) && $data['path'] instanceof UploadedFile){
            $Path =  $this->uploadService->uploadFile($data['path'], 'services');
        }
        return Service::create([
            'name' => $data['name'],
            'color' => $data['color'] ?? '#0055CC',
            'price' => $data['price'] ?? 0,
            'status' => $data['status'] ?? StatusEnum::ACTIVE,
            'type' => $data['type'] ?? TypeEnum::OFFLINE,
            'path' => $Path,
        ]);
    }
    public function updateService(int $id, array $data)
    {

        $service = Service::find($id);
        if (!$service) {
            throw new ModelNotFoundException();
        }

        $service->name = $data['name'];
        $service->color = $data['color'];
        $service->price = $data['price']??0;
        $service->status = StatusEnum::from($data['status']);
        $service->type = TypeEnum::from($data['type']);
        if (isset($data['path']) && $data['path'] instanceof UploadedFile) {
            $rawPath = $service->getRawOriginal('path');
            if ($rawPath && Storage::disk('public')->exists($rawPath)) {
                Storage::disk('public')->delete($rawPath);
            }
            $service->path = $this->uploadService->uploadFile($data['path'], 'services');
        }
        $service->save();

         if (!empty($data['days'])) {
            foreach ($data['days'] as $day) {
                foreach ($day['times'] as $time) {
                    $time['serviceId'] = $service->id;
                    $time['dayOfWeek'] = $day['dayOfWeek'];
                    $status = isset($time['actionStatus']) ? (int)$time['actionStatus'] : ActionStatusEnum::DEFAULT->value;

                    switch ($status) {
                        case ActionStatusEnum::CREATE->value:
                            $this->timeService->createTime($time);
                            break;

                        case ActionStatusEnum::UPDATE->value:
                            $this->timeService->updateTime($time['timeId'], $time);
                            break;

                        case ActionStatusEnum::DELETE->value:
                            $this->timeService->deleteTime($time['timeId']);
                            break;

                        case ActionStatusEnum::DEFAULT->value:
                        default:
                            // تجاهل العنصر
                            break;
                    }
                }
            }
        }
        return $service;
    }
    public function deleteService(int $id)
    {
        $service = Service::find($id);
        if (!$service) {
            throw new ModelNotFoundException();
        }
        $rawPath = $service->getRawOriginal('path');
        if ($rawPath) {
            Storage::disk('public')->delete($rawPath);
        }
        return $service->delete();
    }
    public function serviceView(int $id)
    {
        $service= Service::with('times','exceptions')->find($id);
        if(!$service){
            throw new ModelNotFoundException();
        }
       return $service;
    }


}
