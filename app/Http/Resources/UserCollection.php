<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\PostResource;

class UserCollection extends ResourceCollection
{
    private $statusText;
    public function __construct($resource, $statusText= 'success'){
        parent::__construct($resource);
        $this -> statusText = $statusText;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'data' => $this->collection,
            'status' => $this-> statusText,
            'count' => $this -> collection->count(),

        ];
    }
}
