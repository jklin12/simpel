<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'summary' => [
                'total_users' => $this->total(),
                'roles_distribution' => $this->getRolesDistribution(),
            ],
        ];
    }

    /**
     * Get roles distribution
     */
    private function getRolesDistribution()
    {
        $distribution = [];

        foreach ($this->collection as $user) {
            $role = $user->roles->first()?->name ?? 'no_role';

            if (!isset($distribution[$role])) {
                $distribution[$role] = 0;
            }

            $distribution[$role]++;
        }

        return $distribution;
    }
}
