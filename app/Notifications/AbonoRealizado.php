<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Abono;

class AbonoRealizado extends Notification
{
    use Queueable;

    public $abono;

    /**
     * Create a new notification instance.
     */
    public function __construct(Abono $abono)
    {
        $this->abono = $abono;
    }

    /**
     * Determine the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database']; // Guarda la notificación en la BD
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'mensaje' => 'Se ha registrado un nuevo abono de NIO ' . number_format($this->abono->monto_abono, 2),
            'cliente' => $this->abono->cliente->full_name,
            'credito' => 'Crédito ID: ' . $this->abono->credito->id,
            'fecha' => $this->abono->fecha_abono->format('d-m-Y'),
            'abono_id' => $this->abono->id,
        ];
    }
}
