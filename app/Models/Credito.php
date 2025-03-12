<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credito extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'cliente_id',
        'cartera_id',
        'monto_total',
        'saldo_pendiente',
        'tasa_interes',
        'plazo',
        'unidad_plazo',
        'estado',
        'fecha_inicio',
        'fecha_vencimiento',
    ];

    // Relación con Cliente (un crédito pertenece a un cliente)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function abonos()
   {
    return $this->hasMany(Abono::class);
   }

   public function cartera()
   {
       return $this->belongsTo(Cartera::class);
   }

   public function getSaldoPendienteAttribute()
{
    // Sumar todos los abonos realizados y restarlo del monto total
    return $this->monto_total - $this->abonos->sum('monto_abono');
}
}
