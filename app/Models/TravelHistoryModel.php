<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Persists a durable copy of travel history to MySQL, in addition to
 * the fast-access copy kept in the CI4 session for the current visitor.
 * (Anonymous visitors are tracked by session ID.)
 */
class TravelHistoryModel extends Model
{
    protected $table         = 'travel_history';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'session_id', 'search_type', 'source_code', 'destination_code',
        'pnr_number', 'travel_date', 'searched_at',
    ];

    public function logSearch(string $sessionId, array $entry): void
    {
        $this->insert(array_merge($entry, [
            'session_id'  => $sessionId,
            'searched_at' => date('Y-m-d H:i:s'),
        ]));
    }

    public function recentForSession(string $sessionId, int $limit = 10): array
    {
        return $this->where('session_id', $sessionId)
            ->orderBy('searched_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function clearForSession(string $sessionId): void
    {
        $this->where('session_id', $sessionId)->delete();
    }
}
