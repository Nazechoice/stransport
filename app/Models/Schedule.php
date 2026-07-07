<?php

declare(strict_types=1);

namespace Transport\Models;

final class Schedule extends BaseModel
{
    protected string $table = 'schedules';

    public function searchByRoute(string $origin = '', string $destination = '', ?string $date = null, ?int $busId = null, ?int $routeId = null): array
    {
        $sql = "SELECT s.*, r.origin, r.destination, r.stops, r.fare, r.estimated_minutes, b.bus_number, b.registration_number, b.bus_type, b.capacity, b.image, b.status AS bus_status
                FROM schedules s
                INNER JOIN routes r ON r.id = s.route_id
                INNER JOIN buses b ON b.id = s.bus_id
                WHERE s.deleted_at IS NULL
                AND r.deleted_at IS NULL
                AND b.deleted_at IS NULL
                AND s.status IN ('scheduled','boarding')";
        $params = [];

        $origin = trim($origin);
        $destination = trim($destination);

        if ($origin !== '') {
            $sql .= " AND (r.origin LIKE :origin OR r.stops LIKE :origin_stops)";
            $params['origin'] = '%' . $origin . '%';
            $params['origin_stops'] = '%' . $origin . '%';
        }

        if ($destination !== '') {
            $sql .= " AND (r.destination LIKE :destination OR r.stops LIKE :destination_stops)";
            $params['destination'] = '%' . $destination . '%';
            $params['destination_stops'] = '%' . $destination . '%';
        }

        if ($date) {
            $sql .= " AND s.departure_date = :departure_date";
            $params['departure_date'] = $date;
        }

        if ($busId) {
            $sql .= " AND s.bus_id = :bus_id";
            $params['bus_id'] = $busId;
        }

        if ($routeId) {
            $sql .= " AND s.route_id = :route_id";
            $params['route_id'] = $routeId;
        }

        $sql .= " ORDER BY s.departure_date, s.departure_time";
        return $this->fetchAll($sql, $params);
    }

    public function upcomingTrips(int $limit = 6): array
    {
        $limit = max(1, $limit);

        return $this->fetchAll(
            "SELECT s.*, r.origin, r.destination, r.stops, r.fare, r.estimated_minutes, b.bus_number, b.registration_number, b.bus_type, b.capacity, b.image, b.status AS bus_status
             FROM schedules s
             INNER JOIN routes r ON r.id = s.route_id
             INNER JOIN buses b ON b.id = s.bus_id
             WHERE s.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
               AND s.status IN ('scheduled','boarding')
             ORDER BY s.departure_date, s.departure_time
             LIMIT {$limit}"
        );
    }

    public function forDashboard(): array
    {
        return $this->fetchAll("SELECT s.*, r.origin, r.destination, b.bus_number, d.full_name AS driver_name
            FROM schedules s
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses b ON b.id = s.bus_id
            LEFT JOIN users d ON d.id = s.driver_id
            WHERE s.deleted_at IS NULL
            ORDER BY s.departure_date DESC, s.departure_time DESC");
    }
}
