<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Report <?= e($period) ?></title>
    <style>
        body {
            font-family: Inter, Arial, sans-serif;
            margin: 0;
            padding: 32px;
            color: #0f172a;
            background: #f8fafc;
        }
        .sheet {
            background: #fff;
            border: 1px solid #dbe4f0;
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #f8fafc;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <h1 style="margin:0 0 8px;">Public Bus Transport Ticketing System Report</h1>
        <p style="margin:0;color:#64748b;">Period: <?= e($period) ?></p>
        <table>
            <thead><tr><th>Date</th><th>Revenue</th><th>Payments</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['period_date']) ?></td>
                    <td><?= e($row['revenue']) ?></td>
                    <td><?= e($row['payments']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
