<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Credito;

class MontoAbonoValido implements Rule
{
    protected $creditoId;
    protected $abonoId;

    public function __construct($creditoId, $abonoId = null)
    {
        $this->creditoId = $creditoId;
        $this->abonoId = $abonoId; // El id del abono, si es que estamos editando
    }

    public function passes($attribute, $value)
    {
        $credito = Credito::find($this->creditoId);

        // Verificar si el abono está siendo editado (esto es opcional según el contexto)
        if ($this->abonoId) {
            $abono = $credito->abonos()->find($this->abonoId);

            // Si el abono está siendo editado y el saldo es 0, podemos permitir la edición
            if ($credito->saldo_pendiente <= 0) {
                return true;  // Permitimos la edición del abono si el crédito está cancelado
            }
        }

        // Si no estamos editando el abono, verificamos que el monto no exceda el saldo pendiente
        return $value <= $credito->saldo_pendiente;
    }

    public function message()
    {
        return 'El monto del abono no puede ser mayor al saldo pendiente del crédito.';
    }
}
