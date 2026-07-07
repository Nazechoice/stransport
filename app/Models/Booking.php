<?php

declare(strict_types=1);

namespace Transport\Models;

final class Booking extends BaseModel
{
    protected string $table = 'bookings';

    public function recent(int $limit = 10): array
    {
        return $this->fetchAll("SELECT b.*, u.full_name AS passenger_name, s.departure_date, s.departure_time, r.origin, r.destination
            FROM {$this->table} b
            INNER JOIN users u ON u.id = b.passenger_id
            INNER JOIN schedules s ON s.id = b.schedule_id
            INNER JOIN routes r ON r.id = s.route_id
            WHERE b.deleted_at IS NULL
            ORDER BY b.id DESC
            LIMIT {$limit}");
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (booking_number, passenger_id, schedule_id, bus_id, route_id, seat_number, booking_type, booking_status, total_amount, payment_status, notes, created_by, created_at, updated_at)
                VALUES (:booking_number, :passenger_id, :schedule_id, :bus_id, :route_id, :seat_number, :booking_type, :booking_status, :total_amount, :payment_status, :notes, :created_by, NOW(), NOW())";
        $this->execute($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }

    public function seatTaken(int $scheduleId, string $seatNumber): bool
    {
        $row = $this->fetchOne("SELECT id FROM {$this->table} WHERE schedule_id = :schedule_id AND seat_number = :seat_number AND booking_status IN ('pending','confirmed','checked_in') AND deleted_at IS NULL LIMIT 1", [
            'schedule_id' => $scheduleId,
            'seat_number' => $seatNumber,
        ]);

        return $row !== null;
    }
}

