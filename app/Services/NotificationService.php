<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create notification for user
     */
    public function create(User $user, string $type, string $title, string $message, ?string $actionUrl = null, ?array $data = null)
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Send email notification
     */
    public function sendEmail(User $user, string $subject, string $message)
    {
        // TODO: Implement email sending
        // Mail::to($user->email)->send(new NotificationMail($subject, $message));
    }

    /**
     * Notify when new order created
     */
    public function notifyNewOrder($order)
    {
        // Notify all available operators
        $operators = User::where('role', 'operator')
            ->whereHas('operatorProfile', function($query) {
                $query->where('is_available', true);
            })
            ->get();

        foreach ($operators as $operator) {
            $this->create(
                $operator,
                Notification::TYPE_ORDER_NEW,
                'Order Baru Tersedia!',
                "Order baru dengan budget Rp " . number_format($order->budget, 0, ',', '.') . " untuk kategori {$order->category}",
                route('operator.queue'),
                ['order_id' => $order->id]
            );
        }
    }

    /**
     * Notify client when order accepted
     */
    public function notifyOrderAccepted($order)
    {
        $this->create(
            $order->user,
            Notification::TYPE_ORDER_ACCEPTED,
            'Order Diterima!',
            "Order Anda telah diterima oleh {$order->operator->name}. Operator sedang mengerjakan pesanan Anda.",
            route('orders.show', $order),
            ['order_id' => $order->id]
        );

        // Send email
        $this->sendEmail(
            $order->user,
            'Order Diterima - Smart Copy SMK',
            "Order Anda telah diterima oleh {$order->operator->name}"
        );
    }

    /**
     * Notify client when order rejected
     */
    public function notifyOrderRejected($order)
    {
        $this->create(
            $order->user,
            Notification::TYPE_ORDER_REJECTED,
            'Order Ditolak',
            "Order Anda telah ditolak oleh operator. Order kembali ke queue untuk operator lain.",
            route('orders.show', $order),
            ['order_id' => $order->id]
        );
    }

    /**
     * Notify client when order completed
     */
    public function notifyOrderCompleted($order)
    {
        $this->create(
            $order->user,
            Notification::TYPE_ORDER_COMPLETED,
            'Order Selesai!',
            "Order Anda telah selesai dikerjakan oleh {$order->operator->name}. Silakan review hasilnya.",
            route('orders.show', $order),
            ['order_id' => $order->id]
        );

        // Send email
        $this->sendEmail(
            $order->user,
            'Order Selesai - Smart Copy SMK',
            "Order Anda telah selesai dikerjakan. Silakan review hasilnya."
        );
    }

    /**
     * Notify operator when client requests revision
     */
    public function notifyOrderRevision($order)
    {
        $this->create(
            $order->operator,
            Notification::TYPE_ORDER_REVISION,
            'Request Revisi',
            "Client meminta revisi untuk order #{$order->id}. Silakan cek catatan revisi.",
            route('operator.workspace', $order),
            ['order_id' => $order->id]
        );
    }

    /**
     * Notify operator when payment received
     */
    public function notifyPaymentReceived($payment)
    {
        $order = $payment->order;
        if ($order && $order->operator) {
            $this->create(
                $order->operator,
                Notification::TYPE_PAYMENT_RECEIVED,
                'Pembayaran Diterima!',
                "Pembayaran untuk order #{$order->id} sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " telah diterima.",
                route('operator.earnings'),
                ['payment_id' => $payment->id, 'order_id' => $order->id]
            );
        }
    }

    /**
     * Notify operator when withdrawal approved
     */
    public function notifyWithdrawalApproved($withdrawal)
    {
        $this->create(
            $withdrawal->user,
            Notification::TYPE_WITHDRAWAL_APPROVED,
            'Withdrawal Disetujui!',
            "Request withdrawal Anda sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah disetujui. Dana akan segera ditransfer.",
            route('operator.withdrawal.history'),
            ['withdrawal_id' => $withdrawal->id]
        );

        // Send email
        $this->sendEmail(
            $withdrawal->user,
            'Withdrawal Disetujui - Smart Copy SMK',
            "Request withdrawal Anda sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah disetujui."
        );
    }

    /**
     * Notify operator when withdrawal rejected
     */
    public function notifyWithdrawalRejected($withdrawal)
    {
        $this->create(
            $withdrawal->user,
            Notification::TYPE_WITHDRAWAL_REJECTED,
            'Withdrawal Ditolak',
            "Request withdrawal Anda sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " ditolak. Alasan: {$withdrawal->admin_notes}",
            route('operator.withdrawal.history'),
            ['withdrawal_id' => $withdrawal->id]
        );
    }

    /**
     * Notify operator when withdrawal completed
     */
    public function notifyWithdrawalCompleted($withdrawal)
    {
        $this->create(
            $withdrawal->user,
            Notification::TYPE_WITHDRAWAL_COMPLETED,
            'Withdrawal Selesai!',
            "Dana withdrawal sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah ditransfer ke rekening Anda.",
            route('operator.withdrawal.history'),
            ['withdrawal_id' => $withdrawal->id]
        );

        // Send email
        $this->sendEmail(
            $withdrawal->user,
            'Withdrawal Selesai - Smart Copy SMK',
            "Dana withdrawal sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah ditransfer."
        );
    }
}
