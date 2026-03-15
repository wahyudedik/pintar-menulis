<?php

namespace App\Services;

use App\Mail\NotificationMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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
    public function sendEmail(User $user, string $subject, string $message, ?string $actionUrl = null, ?string $actionLabel = null)
    {
        try {
            Mail::to($user->email)->send(new NotificationMail($subject, $message, $actionUrl, $actionLabel));
        } catch (\Exception $e) {
            Log::error('Failed to send notification email to ' . $user->email . ': ' . $e->getMessage());
        }
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

            // Send email to operator
            $this->sendEmail(
                $operator,
                'Order Baru Tersedia - Pintar Menulis',
                "Ada order baru dengan budget Rp " . number_format($order->budget, 0, ',', '.') . " untuk kategori {$order->category}. Segera cek queue untuk mengambil order ini.",
                route('operator.queue'),
                'Lihat Order Queue'
            );
        }
    }

    /**
     * Notify client when order accepted
     */
    public function notifyOrderAccepted($order)
    {
        $operatorName = $order->operator?->name ?? 'Operator';

        $this->create(
            $order->user,
            Notification::TYPE_ORDER_ACCEPTED,
            'Order Diterima!',
            "Order Anda telah diterima oleh {$operatorName}. Operator sedang mengerjakan pesanan Anda.",
            route('orders.show', $order),
            ['order_id' => $order->id]
        );

        $this->sendEmail(
            $order->user,
            'Order Diterima - Pintar Menulis',
            "Order Anda telah diterima oleh {$operatorName}. Operator sedang mengerjakan pesanan Anda.",
            route('orders.show', $order),
            'Lihat Order'
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

        // Send email
        $this->sendEmail(
            $order->user,
            'Order Ditolak - Pintar Menulis',
            "Order Anda telah ditolak oleh operator. Order kembali ke queue dan akan diproses oleh operator lain.",
            route('orders.show', $order),
            'Lihat Order'
        );
    }

    /**
     * Notify client when order completed
     */
    public function notifyOrderCompleted($order)
    {
        $operatorName = $order->operator?->name ?? 'Operator';

        $this->create(
            $order->user,
            Notification::TYPE_ORDER_COMPLETED,
            'Order Selesai!',
            "Order Anda telah selesai dikerjakan oleh {$operatorName}. Silakan review hasilnya.",
            route('orders.show', $order),
            ['order_id' => $order->id]
        );

        $this->sendEmail(
            $order->user,
            'Order Selesai - Pintar Menulis',
            "Order Anda telah selesai dikerjakan oleh {$operatorName}. Silakan review hasilnya.",
            route('orders.show', $order),
            'Review Hasil'
        );
    }

    /**
     * Notify operator when client requests revision
     */
    public function notifyOrderRevision($order)
    {
        if (!$order->operator) {
            return;
        }

        $this->create(
            $order->operator,
            Notification::TYPE_ORDER_REVISION,
            'Request Revisi',
            "Client meminta revisi untuk order #{$order->id}. Silakan cek catatan revisi.",
            route('operator.workspace', $order),
            ['order_id' => $order->id]
        );

        $this->sendEmail(
            $order->operator,
            'Request Revisi - Pintar Menulis',
            "Client meminta revisi untuk order #{$order->id}. Silakan cek catatan revisi dan segera kerjakan.",
            route('operator.workspace', $order),
            'Buka Workspace'
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

            // Send email to operator
            $this->sendEmail(
                $order->operator,
                'Pembayaran Diterima - Pintar Menulis',
                "Pembayaran untuk order #{$order->id} sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " telah diterima dan di-hold platform. Dana akan diteruskan setelah client approve.",
                route('operator.earnings'),
                'Lihat Penghasilan'
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
            'Withdrawal Disetujui - Pintar Menulis',
            "Request withdrawal Anda sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah disetujui. Dana akan segera ditransfer.",
            route('operator.withdrawal.history'),
            'Lihat Riwayat'
        );
    }

    /**
     * Notify operator when withdrawal rejected
     */
    public function notifyWithdrawalRejected($withdrawal)
    {
        $reason  = $withdrawal->admin_notes ? " Alasan: {$withdrawal->admin_notes}" : '';
        $message = "Request withdrawal Anda sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " ditolak.{$reason}";

        $this->create(
            $withdrawal->user,
            Notification::TYPE_WITHDRAWAL_REJECTED,
            'Withdrawal Ditolak',
            $message,
            route('operator.withdrawal.history'),
            ['withdrawal_id' => $withdrawal->id]
        );

        $this->sendEmail(
            $withdrawal->user,
            'Withdrawal Ditolak - Pintar Menulis',
            $message,
            route('operator.withdrawal.history'),
            'Lihat Riwayat'
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
            'Withdrawal Selesai - Pintar Menulis',
            "Dana withdrawal sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah ditransfer ke rekening Anda.",
            route('operator.withdrawal.history'),
            'Lihat Riwayat'
        );
    }

    /**
     * Notify user when subscription is activated (or trial started)
     */
    public function notifySubscriptionActivated($subscription)
    {
        $package = $subscription->package;
        $isTrial = $subscription->status === 'trial';
        $expiryDate = $isTrial
            ? ($subscription->trial_ends_at?->format('d M Y') ?? '-')
            : ($subscription->ends_at?->format('d M Y') ?? '-');

        $title   = $isTrial ? "Trial {$package->name} Aktif! 🎉" : "Langganan {$package->name} Aktif! 🎉";
        $message = $isTrial
            ? "Trial {$package->name} Anda aktif hingga {$expiryDate}. Selamat menikmati fitur premium!"
            : "Langganan {$package->name} Anda telah aktif hingga {$expiryDate}. Selamat menikmati fitur premium!";

        $this->create(
            $subscription->user,
            Notification::TYPE_SUBSCRIPTION_ACTIVATED,
            $title,
            $message,
            route('subscription.index'),
            ['subscription_id' => $subscription->id, 'package_id' => $package->id]
        );

        $this->sendEmail(
            $subscription->user,
            "{$title} - Pintar Menulis",
            $message,
            route('subscription.index'),
            'Lihat Langganan'
        );
    }

    /**
     * Notify user when subscription payment is pending manual verification
     */
    public function notifySubscriptionPendingVerification($subscription)
    {
        $package = $subscription->package;

        $this->create(
            $subscription->user,
            Notification::TYPE_SUBSCRIPTION_PENDING,
            'Pembayaran Sedang Diverifikasi',
            "Bukti pembayaran langganan {$package->name} Anda sedang diverifikasi admin. Langganan akan aktif setelah verifikasi selesai.",
            route('subscription.index'),
            ['subscription_id' => $subscription->id]
        );

        // Send email confirmation
        $this->sendEmail(
            $subscription->user,
            'Pembayaran Diterima, Sedang Diverifikasi - Pintar Menulis',
            "Bukti pembayaran langganan {$package->name} Anda telah kami terima dan sedang diverifikasi admin. Proses verifikasi maksimal 1×24 jam.",
            route('subscription.index'),
            'Lihat Status Langganan'
        );
    }

    /**
     * Notify user when subscription payment is rejected
     */
    public function notifySubscriptionRejected($subscription)
    {
        $package = $subscription->package;
        $this->create(
            $subscription->user,
            Notification::TYPE_SUBSCRIPTION_REJECTED,
            'Pembayaran Langganan Ditolak',
            "Pembayaran langganan {$package->name} Anda ditolak. Silakan lakukan pembayaran ulang atau hubungi admin.",
            route('subscription.checkout', $package),
            ['subscription_id' => $subscription->id]
        );

        $this->sendEmail(
            $subscription->user,
            "Pembayaran Langganan Ditolak - Pintar Menulis",
            "Pembayaran langganan {$package->name} Anda ditolak. Silakan lakukan pembayaran ulang atau hubungi admin.",
            route('subscription.checkout', $package),
            'Bayar Ulang'
        );
    }

    // ── Project Collaboration Notifications ───────────────────────────────────

    /**
     * Notify project owner when invitation is accepted
     */
    public function notifyInvitationAccepted($invitation)
    {
        $project = $invitation->project;
        $owner   = $project->user;
        $member  = $invitation->user;

        $this->create(
            $owner,
            Notification::TYPE_PROJECT_INVITATION_ACCEPTED,
            'Undangan Diterima',
            "{$member->name} telah menerima undangan untuk bergabung di proyek \"{$project->business_name}\" sebagai {$invitation->role}.",
            route('projects.collaboration.index', $project),
            ['project_id' => $project->id, 'user_id' => $member->id]
        );

        $this->sendEmail(
            $owner,
            'Undangan Diterima - Pintar Menulis',
            "{$member->name} telah menerima undangan untuk bergabung di proyek \"{$project->business_name}\" sebagai {$invitation->role}.",
            route('projects.collaboration.index', $project),
            'Lihat Tim Proyek'
        );
    }

    /**
     * Notify project owner when invitation is declined
     */
    public function notifyInvitationDeclined($invitation)
    {
        $project = $invitation->project;
        $owner   = $project->user;
        $member  = $invitation->user;

        $this->create(
            $owner,
            Notification::TYPE_PROJECT_INVITATION_DECLINED,
            'Undangan Ditolak',
            "{$member->name} menolak undangan untuk bergabung di proyek \"{$project->business_name}\".",
            route('projects.collaboration.index', $project),
            ['project_id' => $project->id, 'user_id' => $member->id]
        );

        $this->sendEmail(
            $owner,
            'Undangan Ditolak - Pintar Menulis',
            "{$member->name} menolak undangan untuk bergabung di proyek \"{$project->business_name}\".",
            route('projects.collaboration.index', $project),
            'Lihat Tim Proyek'
        );
    }

    /**
     * Notify project approvers when content is submitted for review
     */
    public function notifyContentSubmitted($content)
    {
        $project   = $content->project;
        $submitter = $content->creator;

        // Notify all admin/owner members who can approve
        $approvers = $project->members()
            ->whereIn('role', ['admin'])
            ->where('status', 'accepted')
            ->with('user')
            ->get()
            ->pluck('user');

        // Also notify project owner
        $approvers->push($project->user);

        foreach ($approvers->unique('id') as $approver) {
            if ($approver->id === $submitter->id) continue;

            $this->create(
                $approver,
                Notification::TYPE_CONTENT_SUBMITTED,
                'Konten Menunggu Review',
                "{$submitter->name} mengirim konten \"{$content->title}\" untuk direview di proyek \"{$project->business_name}\".",
                route('projects.content.show', [$project, $content]),
                ['project_id' => $project->id, 'content_id' => $content->id]
            );

            $this->sendEmail(
                $approver,
                'Konten Menunggu Review - Pintar Menulis',
                "{$submitter->name} mengirim konten \"{$content->title}\" untuk direview di proyek \"{$project->business_name}\". Silakan review dan berikan keputusan.",
                route('projects.content.show', [$project, $content]),
                'Review Konten'
            );
        }
    }

    /**
     * Notify content creator when content is approved
     */
    public function notifyContentApproved($content)
    {
        $project  = $content->project;
        $reviewer = $content->reviewer;

        $this->create(
            $content->creator,
            Notification::TYPE_CONTENT_APPROVED,
            'Konten Disetujui ✅',
            "Konten \"{$content->title}\" Anda di proyek \"{$project->business_name}\" telah disetujui" . ($reviewer ? " oleh {$reviewer->name}" : '') . ".",
            route('projects.content.show', [$project, $content]),
            ['project_id' => $project->id, 'content_id' => $content->id]
        );

        $this->sendEmail(
            $content->creator,
            'Konten Disetujui - Pintar Menulis',
            "Konten \"{$content->title}\" Anda di proyek \"{$project->business_name}\" telah disetujui" . ($reviewer ? " oleh {$reviewer->name}" : '') . ". Selamat!",
            route('projects.content.show', [$project, $content]),
            'Lihat Konten'
        );
    }

    /**
     * Notify content creator when content is rejected
     */
    public function notifyContentRejected($content)
    {
        $project  = $content->project;
        $reviewer = $content->reviewer;
        $notes    = $content->review_notes ? " Catatan: {$content->review_notes}" : '';

        $this->create(
            $content->creator,
            Notification::TYPE_CONTENT_REJECTED,
            'Konten Perlu Revisi',
            "Konten \"{$content->title}\" Anda di proyek \"{$project->business_name}\" perlu direvisi" . ($reviewer ? " oleh {$reviewer->name}" : '') . ".{$notes}",
            route('projects.content.show', [$project, $content]),
            ['project_id' => $project->id, 'content_id' => $content->id]
        );

        $this->sendEmail(
            $content->creator,
            'Konten Perlu Revisi - Pintar Menulis',
            "Konten \"{$content->title}\" Anda di proyek \"{$project->business_name}\" perlu direvisi" . ($reviewer ? " oleh {$reviewer->name}" : '') . ".{$notes}",
            route('projects.content.show', [$project, $content]),
            'Lihat Konten'
        );
    }

    // ── Operator Notifications ────────────────────────────────────────────────

    /**
     * Notify operator when verified by admin
     */
    public function notifyOperatorVerified(User $operator)
    {
        $this->create(
            $operator,
            Notification::TYPE_OPERATOR_VERIFIED,
            'Akun Operator Terverifikasi ✅',
            'Selamat! Akun operator Anda telah diverifikasi oleh admin. Anda sekarang dapat menerima order dari client.',
            route('operator.queue'),
        );

        $this->sendEmail(
            $operator,
            'Akun Operator Terverifikasi - Pintar Menulis',
            'Selamat! Akun operator Anda telah diverifikasi oleh admin. Anda sekarang dapat menerima order dari client.',
            route('operator.queue'),
            'Lihat Order Queue'
        );
    }

    /**
     * Notify operator when verification is revoked
     */
    public function notifyOperatorUnverified(User $operator)
    {
        $this->create(
            $operator,
            Notification::TYPE_OPERATOR_UNVERIFIED,
            'Verifikasi Operator Dicabut',
            'Verifikasi akun operator Anda telah dicabut oleh admin. Hubungi admin untuk informasi lebih lanjut.',
            null,
        );

        $this->sendEmail(
            $operator,
            'Verifikasi Operator Dicabut - Pintar Menulis',
            'Verifikasi akun operator Anda telah dicabut oleh admin. Hubungi admin untuk informasi lebih lanjut.',
        );
    }

    /**
     * Notify operator when withdrawal request is submitted (confirmation)
     */
    public function notifyWithdrawalSubmitted(User $operator, $withdrawal)
    {
        $this->create(
            $operator,
            Notification::TYPE_WITHDRAWAL_SUBMITTED,
            'Request Withdrawal Diterima',
            "Request withdrawal sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " berhasil disubmit dan menunggu approval admin.",
            route('operator.withdrawal.history'),
            ['withdrawal_id' => $withdrawal->id]
        );

        $this->sendEmail(
            $operator,
            'Request Withdrawal Diterima - Pintar Menulis',
            "Request withdrawal sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " berhasil disubmit dan sedang menunggu approval admin. Proses maksimal 1×24 jam.",
            route('operator.withdrawal.history'),
            'Lihat Riwayat Withdrawal'
        );
    }

    /**
     * Notify client when payment proof is submitted (confirmation)
     */
    public function notifyPaymentProofSubmitted(User $client, $order)
    {
        $this->create(
            $client,
            Notification::TYPE_PAYMENT_PROOF_SUBMITTED,
            'Bukti Pembayaran Diterima',
            "Bukti pembayaran untuk order #{$order->id} berhasil diupload dan menunggu verifikasi admin (maks. 1×24 jam).",
            route('orders.show', $order),
            ['order_id' => $order->id]
        );

        $this->sendEmail(
            $client,
            'Bukti Pembayaran Diterima - Pintar Menulis',
            "Bukti pembayaran untuk order #{$order->id} berhasil kami terima dan sedang menunggu verifikasi admin. Proses verifikasi maksimal 1×24 jam.",
            route('orders.show', $order),
            'Lihat Status Order'
        );
    }
}
