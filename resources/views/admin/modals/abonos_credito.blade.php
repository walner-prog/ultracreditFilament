<div  >
    <h3 style="color: #777676;">Crédito ID: {{ $credito->id }}</h3>
    <p><strong>Cliente:</strong>{{$credito->cliente->nombres }} {{$credito->cliente->apellidos }}</p>
    <p><strong>Monto Total:</strong> <span style="color: #008000;">C${{ number_format($credito->monto_total, 2) }}</span></p>
    <p><strong>Saldo Pendiente:</strong> <span style="color: #cc0000;">C${{ number_format($credito->saldo_pendiente, 2) }}</span></p>
    <p><strong>Tasa de Interés:</strong> <span style="color: #C19C07FF;">{{ $credito->tasa_interes }}%</span></p>
    <p><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($credito->fecha_inicio)->format('d/m/Y') }}</p>
    <p><strong>Fecha de Vencimiento:</strong> {{ \Carbon\Carbon::parse($credito->fecha_vencimiento)->format('d/m/Y') }}</p>

    <hr style="margin: 10px 0; border: 1px solid #ccc;">

    <h4 style="color: #837f7f;">Abonos Realizados</h4>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc; margin-top: 0.5rem;">
        <thead>
            <tr style="background-color: #9D9D9DFF;">
                <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Fecha</th>
                <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Monto</th>
                <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Registrado por</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($abonos as $abono)
                <tr style="border: 1px solid #ccc;">
                    <td style="padding: 8px;">{{ \Carbon\Carbon::parse($abono->fecha_abono)->format('d/m/Y') }}</td>
                    <td style="padding: 8px; color: #008000; font-weight: bold;">C${{ number_format($abono->monto_abono, 2) }}</td>
                    <td style="padding: 8px; color: #666;">{{ $abono->user->name ?? 'Desconocido' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


