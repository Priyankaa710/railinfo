<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table         = 'schedules';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'train_id', 'source_code', 'destination_code', 'travel_date',
        'departure_time', 'arrival_time', 'duration',
        'sl_seats', 'sl_fare',
        'ac3_seats', 'ac3_fare',
        'ac2_seats', 'ac2_fare',
        'ac1_seats', 'ac1_fare',
        'status', 'fetched_at',
    ];

    /**
     * Look up cached schedule rows for a source/destination/date combo,
     * joined with the trains table for display data. This is the
     * "offline first" path — the controller falls back to the live
     * API only when nothing (fresh) is found here.
     */
    public function findCached(string $source, string $destination, string $date): array
    {
        return $this->select('schedules.*, trains.train_number, trains.train_name, trains.train_type')
            ->join('trains', 'trains.id = schedules.train_id')
            ->where('schedules.source_code', $source)
            ->where('schedules.destination_code', $destination)
            ->where('schedules.travel_date', $date)
            ->orderBy('schedules.departure_time', 'ASC')
            ->findAll();
    }

    /**
     * Whether the cache for this route/date is still considered fresh.
     */
    public function isFresh(string $source, string $destination, string $date, int $ttlSeconds): bool
    {
        $row = $this->select('MAX(fetched_at) as latest')
            ->where('source_code', $source)
            ->where('destination_code', $destination)
            ->where('travel_date', $date)
            ->first();

        if (empty($row['latest'])) {
            return false;
        }

        return (time() - strtotime($row['latest'])) < $ttlSeconds;
    }

    /**
     * Upsert a batch of schedule rows fetched from the live API.
     */
    public function storeFromApi(array $rows): void
    {
        foreach ($rows as $row) {
            $row['fetched_at'] = date('Y-m-d H:i:s');
            $this->insert($row);
        }
    }
}
