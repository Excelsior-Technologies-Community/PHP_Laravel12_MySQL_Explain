<!DOCTYPE html>
<html>
<head>
    <title>MySQL Query Analyzer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        h2 {
            margin-top: 0;
            font-size: 20px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            color: #fff;
            margin-left: 10px;
        }

        .badge-red {
            background: #e74c3c;
        }

        .badge-green {
            background: #2ecc71;
        }

        pre {
            background: #1e1e1e;
            color: #00ff9c;
            padding: 12px;
            border-radius: 6px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #e0e0e0;
            text-align: center;
            font-size: 14px;
        }

        th {
            background: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .key-empty {
            color: #e74c3c;
            font-weight: bold;
        }

        .key-used {
            color: #2ecc71;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- BEFORE -->
    <div class="card">
        <h2>
            🔴 Before Optimization
            <span class="badge badge-red">BAD QUERY</span>
        </h2>

        <pre>{{ $queryBefore }}</pre>

        <table>
            <tr>
                <th>ID</th>
                <th>Select Type</th>
                <th>Table</th>
                <th>Type</th>
                <th>Possible Keys</th>
                <th>Key</th>
                <th>Rows</th>
                <th>Extra</th>
            </tr>

            @foreach($explainBefore as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->select_type }}</td>
                    <td>{{ $row->table }}</td>
                    <td>{{ $row->type }}</td>
                    <td>{{ $row->possible_keys }}</td>
                    <td class="{{ $row->key ? 'key-used' : 'key-empty' }}">
                        {{ $row->key ?: 'NULL' }}
                    </td>
                    <td>{{ $row->rows }}</td>
                    <td>{{ $row->Extra }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <!-- AFTER -->
    <div class="card">
        <h2>
            🟢 After Optimization
            <span class="badge badge-green">OPTIMIZED</span>
        </h2>

        <pre>{{ $queryAfter }}</pre>

        <table>
            <tr>
                <th>ID</th>
                <th>Select Type</th>
                <th>Table</th>
                <th>Type</th>
                <th>Possible Keys</th>
                <th>Key</th>
                <th>Rows</th>
                <th>Extra</th>
            </tr>

            @foreach($explainAfter as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->select_type }}</td>
                    <td>{{ $row->table }}</td>
                    <td>{{ $row->type }}</td>
                    <td>{{ $row->possible_keys }}</td>
                    <td class="{{ $row->key ? 'key-used' : 'key-empty' }}">
                        {{ $row->key ?: 'NULL' }}
                    </td>
                    <td>{{ $row->rows }}</td>
                    <td>{{ $row->Extra }}</td>
                </tr>
            @endforeach
        </table>
    </div>

</div>

</body>
</html>