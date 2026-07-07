<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket <?= e($ticket['ticket_number']) ?></title>
    <style>
        body {
            margin: 0;
            padding: 32px;
            font-family: Inter, Arial, sans-serif;
            color: #0f172a;
            background: #f8fafc;
        }
        .ticket {
            border: 1px solid #dbe4f0;
            border-radius: 24px;
            padding: 28px;
            background: #fff;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        }
        .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 20px;
        }
        .meta div {
            background: #f8fbff;
            padding: 14px;
            border-radius: 16px;
        }
        .meta span {
            display: block;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 4px;
            letter-spacing: 0.12em;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h1 style="margin:0 0 8px;font-size:24px;">Public Bus Transport Ticketing System</h1>
        <h2 style="margin:0 0 8px;font-size:34px;"><?= e($ticket['ticket_number']) ?></h2>
        <p style="margin:0;color:#64748b;"><?= e($ticket['origin']) ?> to <?= e($ticket['destination']) ?></p>
        <div class="meta">
            <div><span>Passenger</span><strong><?= e($ticket['passenger_name']) ?></strong></div>
            <div><span>Seat</span><strong><?= e($ticket['seat_number']) ?></strong></div>
            <div><span>Bus</span><strong><?= e($ticket['bus_number']) ?></strong></div>
            <div><span>Departure</span><strong><?= e($ticket['departure_date']) ?> <?= e($ticket['departure_time']) ?></strong></div>
        </div>
        <div style="margin-top:24px;"><?= $svg ?></div>
    </div>
</body>
</html>
