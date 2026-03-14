<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $invitation;
    public $invitedBy;
    public $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Project $project, ProjectMember $invitation, User $invitedBy, ?string $customMessage = null)
    {
        $this->project = $project;
        $this->invitation = $invitation;
        $this->invitedBy = $invitedBy;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Bergabung di Proyek: ' . $this->project->business_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.project-invitation',
            with: [
                'project' => $this->project,
                'invitation' => $this->invitation,
                'invitedBy' => $this->invitedBy,
                'customMessage' => $this->customMessage,
                'acceptUrl' => route('invitations.accept', $this->invitation),
                'declineUrl' => route('invitations.decline', $this->invitation),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
