<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Twój Raport HabitFlow</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; } /* Obsługa polskich znaków w PDF */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #444; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; color: #2d3748; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tygodniowy Raport Postępów</h1>
        <p>Przygotowany dla: <strong>{{ $user->name }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nawyk</th>
                <th>Zrealizowano</th>
                <th>Aktualna seria (dni)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($habitsData as $data)
            <tr>
                <td>{{ $data['name'] }}</td>
                <td>{{ $data['completions_count'] }} / 7</td>
                <td>{{ $data['streak'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>