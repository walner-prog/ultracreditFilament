<div>
   

    <ul style="margin-top: 1rem; list-style: none; padding: 0;">
        @foreach ($activeCredits as $credito)
            <li style="border: 1px solid #ccc; padding: 1rem; border-radius: 8px; background-color: #ffffff; 
                       box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                       transition: background 0.3s; color: #333; margin-bottom: 1.5rem;" 
                onmouseover="this.style.backgroundColor='#f5f5f5'"
                onmouseout="this.style.backgroundColor='#ffffff'"
                data-theme="dark">
                
                <strong style="color: #444;">Crédito ID:</strong> <span style="color: #666;">{{ $credito->id }}</span><br>
                <strong style="color: #444;">Monto Total:</strong> <span style="color: #008000;">C${{ number_format($credito->monto_total, 2) }}</span><br>
                <strong style="color: #444;">Saldo Pendiente:</strong> <span style="color: #cc0000;">C${{ number_format($credito->saldo_pendiente, 2) }}</span><br>
                <strong style="color: #444;">Tasa de Interés:</strong> <span style="color: #ffcc00;">{{ $credito->tasa_interes }}%</span><br>
                <strong style="color: #444;">Plazo:</strong> <span style="color: #666;">{{ $credito->plazo }} {{ ucfirst($credito->unidad_plazo) }}</span><br>
                <strong style="color: #444;">Fecha de Inicio:</strong> <span style="color: #666;">{{ \Carbon\Carbon::parse($credito->fecha_inicio)->format('d/m/Y') }}</span><br>
                <strong style="color: #444;">Fecha de Vencimiento:</strong> <span style="color: #666;">{{ \Carbon\Carbon::parse($credito->fecha_vencimiento)->format('d/m/Y') }}</span><br>
                
                <hr style="margin: 10px 0; border: 1px solid #ccc;">

                <!-- Mostrar los abonos asociados a este crédito -->
                <h4 style="font-size: 1rem; font-weight: bold; color: #444; margin-top: 1rem;">Abonos Realizados</h4>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc; margin-top: 0.5rem;">
                    <thead>
                        <tr style="background-color: #e0e0e0;">
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Fecha</th>
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Monto</th>
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Registrado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($credito->abonos as $abono)
                            <tr style="border: 1px solid #ccc;">
                                <td style="padding: 8px;">{{ \Carbon\Carbon::parse($abono->fecha_abono)->format('d/m/Y') }}</td>
                                <td style="padding: 8px; color: #008000; font-weight: bold;">C${{ number_format($abono->monto_abono, 2) }}</td>
                                <td style="padding: 8px; color: #666;">{{ $abono->user->name ?? 'Desconocido' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </li>
        @endforeach
    </ul>
</div>
