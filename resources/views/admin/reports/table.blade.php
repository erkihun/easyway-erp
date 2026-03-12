<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>{{ $title }}</title></head>
<body>
    <h2>{{ $title }}</h2>
    @if($rows->isNotEmpty())
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            @foreach(array_keys((array) $rows->first()) as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                @foreach((array) $row as $value)
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>



