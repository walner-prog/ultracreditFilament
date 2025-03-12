<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditosTable extends Migration
{
    /**
     * Ejecuta la migración.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->id(); // id INT (PK)
          //  $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Usuario que otorga el credito');
            $table->unsignedBigInteger('cliente_id'); // cliente_id INT (FK)
            $table->foreignId('cartera_id')->constrained('carteras')->onDelete('cascade')->comment('credito asigano a x cartera ');
            $table->decimal('monto_total', 10, 2); // monto_total DECIMAL(10,2)
            $table->decimal('saldo_pendiente', 10, 2); // saldo_pendiente DECIMAL(10,2)
            $table->decimal('tasa_interes', 5, 2)->default(20.00); // tasa_interes DECIMAL(5,2) (valor predeterminado 20%)
            $table->integer('plazo'); // plazo INT (puede ser días o meses)
            $table->enum('unidad_plazo', ['dias', 'meses']); // unidad_plazo ENUM ('dias', 'meses')
            $table->enum('estado', ['activo', 'cancelado', 'moroso'])->default('activo'); // estado ENUM
            $table->date('fecha_inicio'); // fecha_inicio DATE
            $table->date('fecha_vencimiento'); // fecha_vencimiento DATE
            $table->timestamps(); // created_at y updated_at TIMESTAMP
            $table->softDeletes();

            // Definir la relación con la tabla clientes
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Revierte la migración.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creditos');
    }
}
