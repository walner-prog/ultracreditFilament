<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Personas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }

        /* Opcional: Ajustar los márgenes y tamaño de fuente si es necesario */
        @page {
            size: A4 landscape;  /* Cambia la orientación a horizontal */
            margin: 20px;
        }
    </style>
</head>
<body>
    <h2>Lista de Personas</h2>
    <table>
        <thead>
            <tr>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Género</th>
                <th>Fecha de Nacimiento</th>
                <th>Cédula/DNI</th>
                <th>Teléfono</th>
                <th>Correo Electrónico</th>
                <th>Dirección Completa</th>
                <th>Código de Hogar</th>
                <th>Estado Civil</th>
                <th>Nivel Educativo</th>
                <th>Creado el</th>
              
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->first_name }}</td>
                    <td>{{ $record->last_name }}</td>
                    <td>{{ $record->gender }}</td>
                    <td>{{ $record->birth_date }}</td>
                    <td>{{ $record->national_id }}</td>
                    <td>{{ $record->phone }}</td>
                    <td>{{ $record->email }}</td>
                    <td>{{ $record->address ? $record->address->full_address : 'Sin dirección' }}</td>
                    <td>{{ $record->household ? $record->household->household_code : 'N/A' }}</td>
                    <td>
                        @switch($record->marital_status)
                            @case('Single') Soltero/a @break
                            @case('Married') Casado/a @break
                            @case('Divorced') Divorciado/a @break
                            @case('Widowed') Viudo/a @break
                            @case('Separated') Separado/a @break
                            @case('Cohabiting') Unión libre @break
                            @default Desconocido
                        @endswitch
                    </td>
                    <td>{{ $record->educationLevel ? $record->educationLevel->education_level : 'N/A' }}</td>
                    <td>{{ $record->created_at->format('Y-m-d H:i:s') }}</td>
                   
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
