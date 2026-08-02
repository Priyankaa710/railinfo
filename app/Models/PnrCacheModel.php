<?php

namespace App\Models;

use CodeIgniter\Model;

class PnrCacheModel extends Model
{
    protected $table         = 'pnr_cache';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'pnr_number', 'train_number', 'train_name', 'journey_date',
        'source_code', 'destination_code', 'boarding_point', 'class',
        'chart_prepared', 'passenger_count', 'passengers_json',
        'current_status', 'fetched_at',
    ];

    public function findByPnr(string $pnr): ?array
    {
        return $this->where('pnr_number', $pnr)
            ->orderBy('fetched_at', 'DESC')
            ->first();
    }

    public function isFresh(array $row, int $ttlSeconds): bool
    {
        if (empty($row['fetched_at'])) {
            return false;
        }

        return (time() - strtotime($row['fetched_at'])) < $ttlSeconds;
    }

    public function upsert(string $pnr, array $data): void
    {
        $existing = $this->where('pnr_number', $pnr)->first();
        $data['pnr_number'] = $pnr;
        $data['fetched_at'] = date('Y-m-d H:i:s');

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }
}
