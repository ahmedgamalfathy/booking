<?php

namespace App\Http\Resources\Exception;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AllExceptionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
     private $pagination;

     public function __construct($resource)
     {
         $this->pagination = [
             'total' => $resource->total(),
             'count' => $resource->count(),
             'perPage' => $resource->perPage(),
             'currentPage' => $resource->currentPage(),
             'totalPages' => $resource->lastPage()
         ];

         $resource = $resource->getCollection();

         parent::__construct($resource);
     }
    public function toArray(Request $request): array
    {

        return [
            'Services' => AllExceptionResource::collection(($this->collection)->values()->all()),
            'pagination' => $this->pagination
        ];

    }
}
