<?php

declare(strict_types=1);

namespace Transport\Services;

use Transport\Core\Database;

final class SeatLayoutService
{
    public function ensureScheduleSeats(int $scheduleId, int $capacity, int $columns = 4): void
    {
        $capacity = max(0, $capacity);
        $columns = max(1, $columns);
        if ($capacity <= 0) {
            return;
        }

        $pdo = Database::pdo();
        $existingStmt = $pdo->prepare("SELECT seat_number FROM schedule_seats WHERE schedule_id = :schedule_id AND deleted_at IS NULL");
        $existingStmt->execute(['schedule_id' => $scheduleId]);
        $existing = array_flip(array_column($existingStmt->fetchAll(), 'seat_number'));

        $stmt = $pdo->prepare("INSERT INTO schedule_seats (schedule_id, seat_row, seat_column, seat_number, seat_status, created_at, updated_at) VALUES (:schedule_id, :seat_row, :seat_column, :seat_number, 'available', NOW(), NOW())");

        $seatIndex = 0;
        $rowCount = (int) ceil($capacity / $columns);
        for ($row = 0; $row < $rowCount; $row++) {
            $seatRow = $this->rowLabel($row);
            for ($column = 1; $column <= $columns; $column++) {
                $seatIndex++;
                if ($seatIndex > $capacity) {
                    break;
                }

                $seatNumber = $seatRow . $column;
                if (isset($existing[$seatNumber])) {
                    continue;
                }

                $stmt->execute([
                    'schedule_id' => $scheduleId,
                    'seat_row' => $seatRow,
                    'seat_column' => (string) $column,
                    'seat_number' => $seatNumber,
                ]);
            }
        }
    }

    public function seatsForSchedule(int $scheduleId, ?int $capacity = null): array
    {
        if ($capacity !== null) {
            $this->ensureScheduleSeats($scheduleId, $capacity);
        }

        $pdo = Database::pdo();
        $rows = $pdo->prepare("SELECT seat_number, seat_status FROM schedule_seats WHERE schedule_id = :schedule_id AND deleted_at IS NULL ORDER BY seat_row, seat_column");
        $rows->execute(['schedule_id' => $scheduleId]);
        $seats = $rows->fetchAll();

        if ($seats) {
            return $seats;
        }

        if ($capacity === null || $capacity <= 0) {
            return [];
        }

        $generated = [];
        $columns = 4;
        $seatIndex = 0;
        $rowCount = (int) ceil($capacity / $columns);
        for ($row = 0; $row < $rowCount; $row++) {
            $seatRow = $this->rowLabel($row);
            for ($column = 1; $column <= $columns; $column++) {
                $seatIndex++;
                if ($seatIndex > $capacity) {
                    break;
                }

                $generated[] = [
                    'seat_number' => $seatRow . $column,
                    'seat_status' => 'available',
                ];
            }
        }

        return $generated;
    }

    public function matrixForSchedule(int $scheduleId, ?int $capacity = null): array
    {
        $pdo = Database::pdo();
        $seatRows = $this->seatsForSchedule($scheduleId, $capacity);
        if (!$seatRows && $capacity !== null && $capacity > 0) {
            $seatRows = $this->seatsForSchedule($scheduleId, $capacity);
        }

        $bookedStmt = $pdo->prepare("SELECT seat_number FROM bookings WHERE schedule_id = :schedule_id AND booking_status IN ('pending','confirmed','checked_in') AND deleted_at IS NULL");
        $bookedStmt->execute(['schedule_id' => $scheduleId]);
        $booked = array_flip(array_column($bookedStmt->fetchAll(), 'seat_number'));

        $blockedStmt = $pdo->prepare("SELECT seat_number FROM seat_blocks WHERE schedule_id = :schedule_id AND deleted_at IS NULL");
        $blockedStmt->execute(['schedule_id' => $scheduleId]);
        $blocked = array_flip(array_column($blockedStmt->fetchAll(), 'seat_number'));

        $seats = [];
        $update = $pdo->prepare("UPDATE schedule_seats SET seat_status = :seat_status, updated_at = NOW() WHERE schedule_id = :schedule_id AND seat_number = :seat_number AND deleted_at IS NULL");
        foreach ($seatRows as $seatRow) {
            $seatNumber = (string) ($seatRow['seat_number'] ?? '');
            $status = (string) ($seatRow['seat_status'] ?? 'available');
            if (isset($blocked[$seatNumber])) {
                $status = 'blocked';
            } elseif (isset($booked[$seatNumber])) {
                $status = 'booked';
            } elseif (!in_array($status, ['available', 'booked', 'blocked'], true)) {
                $status = 'available';
            }

            if ($seatNumber !== '' && $status !== ($seatRow['seat_status'] ?? null)) {
                $update->execute([
                    'schedule_id' => $scheduleId,
                    'seat_number' => $seatNumber,
                    'seat_status' => $status,
                ]);
            }

            $seats[] = [
                'seat' => $seatNumber,
                'status' => $status,
            ];
        }

        return $seats;
    }

    public function markBooked(int $scheduleId, string $seatNumber): void
    {
        $this->updateSeatStatus($scheduleId, $seatNumber, 'booked');
    }

    public function markAvailable(int $scheduleId, string $seatNumber): void
    {
        $this->updateSeatStatus($scheduleId, $seatNumber, 'available');
    }

    public function markBlocked(int $scheduleId, string $seatNumber): void
    {
        $this->updateSeatStatus($scheduleId, $seatNumber, 'blocked');
    }

    private function updateSeatStatus(int $scheduleId, string $seatNumber, string $status): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("UPDATE schedule_seats SET seat_status = :seat_status, updated_at = NOW() WHERE schedule_id = :schedule_id AND seat_number = :seat_number AND deleted_at IS NULL");
        $stmt->execute([
            'schedule_id' => $scheduleId,
            'seat_number' => $seatNumber,
            'seat_status' => $status,
        ]);
    }

    private function rowLabel(int $index): string
    {
        $label = '';
        $index++;

        while ($index > 0) {
            $index--;
            $label = chr(65 + ($index % 26)) . $label;
            $index = intdiv($index, 26);
        }

        return $label;
    }
}
