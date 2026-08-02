<?php

namespace App\Models;

use CodeIgniter\Model;

class StationModel extends Model
{
    protected $table            = 'stations';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['code', 'name', 'city', 'state', 'zone'];

    /**
     * Autocomplete search used by TrainController::stationSuggest().
     * Matches on station code OR name, code matches ranked first.
     */
    public function search(string $term, int $limit = 8): array
    {
        $term = trim($term);

        if ($term === '') {
            return [];
        }

        return $this->select("id, code, name, city, state,
                IF(code LIKE CONCAT(?, '%'), 1, 2) AS rank_order", [$term], false)
            ->groupStart()
                ->like('code', $term)
                ->orLike('name', $term)
                ->orLike('city', $term)
            ->groupEnd()
            ->orderBy('rank_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->limit($limit)
            ->find();
    }

    public function findByCode(string $code): ?array
    {
        return $this->where('code', strtoupper($code))->first();
    }

    /**
     * A handful of popular routes to show on the home page.
     */
    public function getPopularRoutes(): array
    {
        return [
            ['from' => 'NDLS', 'fromName' => 'New Delhi', 'to' => 'BCT', 'toName' => 'Mumbai Central'],
            ['from' => 'MAS',  'fromName' => 'Chennai Central', 'to' => 'SBC', 'toName' => 'Bengaluru'],
            ['from' => 'HWH',  'fromName' => 'Howrah Jn', 'to' => 'NDLS', 'toName' => 'New Delhi'],
            ['from' => 'ADI',  'fromName' => 'Ahmedabad Jn', 'to' => 'BCT', 'toName' => 'Mumbai Central'],
        ];
    }
}
