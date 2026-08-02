<?php

namespace App\Models;

use CodeIgniter\Model;

class TrainModel extends Model
{
    protected $table         = 'trains';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'train_number', 'train_name', 'train_type',
        'source_code', 'destination_code',
        'departure_time', 'arrival_time', 'duration', 'distance_km',
        'runs_mon', 'runs_tue', 'runs_wed', 'runs_thu', 'runs_fri', 'runs_sat', 'runs_sun',
    ];

    public function findByNumber(string $number): ?array
    {
        return $this->where('train_number', $number)->first();
    }

    public function runningDaysArray(array $train): array
    {
        $map = [
            'runs_mon' => 'Mon', 'runs_tue' => 'Tue', 'runs_wed' => 'Wed',
            'runs_thu' => 'Thu', 'runs_fri' => 'Fri', 'runs_sat' => 'Sat', 'runs_sun' => 'Sun',
        ];

        $days = [];
        foreach ($map as $field => $label) {
            $days[$label] = ! empty($train[$field]);
        }

        return $days;
    }
}
