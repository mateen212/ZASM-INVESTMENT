<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;
use App\Models\Investment;
use App\Models\Deal;
use App\Models\Admin;

class InvestmentCreatedNotification extends Notification
{
    use Queueable;

    protected $investment;

    public function __construct(Investment $investment)
    {
        $this->investment = $investment;
    }

    public function via($notifiable)
    {
        $template = $this->getNotificationTemplate();
        $channels = [];
        if ($template && $template->email_status) {
            $channels[] = 'mail';
        }
        if ($template && $template->push_status) {
            $channels[] = 'database';
        }
        return $channels ?: ['database']; // Default to database if no template found
    }

    public function toMail($notifiable)
    {
        $template = $this->getNotificationTemplate();
        $deal = $this->investment->deal;

        if (!$template || !$template->email_status) {
            return null; // Skip email if no template or email is disabled
        }

        $shortcodes = $this->getShortcodes($notifiable);
        $subject = $this->replaceShortcodes($template->subject, $shortcodes);
        $body = $this->replaceShortcodes($template->email_body, $shortcodes);

        $senderEmail = $template->email_sent_from_address ?? 'sign@isign.click';
        $senderName = $template->email_sent_from_name ?? config('app.name');

        $mail = (new MailMessage)
            ->from($senderEmail, $senderName) // Explicitly set sender
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($body)
            ->action('View Deal', url('/deals/' . $deal->id));

        // Log the notification
        $this->logNotification($notifiable, 'email', $subject, $body, $template);

        // Debug sender and receiver
        // dd([
        //     'sender' => [
        //         'email' => $senderEmail,
        //         'name' => $senderName,
        //     ],
        //     'receiver' => '',
        // ]);

        return $mail;
    }

    public function toDatabase($notifiable)
    {
        $template = $this->getNotificationTemplate();
        $deal = $this->investment->deal;

        $shortcodes = $this->getShortcodes($notifiable);
        $subject = $template ? $this->replaceShortcodes($template->push_title, $shortcodes) : 'New Investment Created';
        $message = $template ? $this->replaceShortcodes($template->push_body, $shortcodes) : 'A new investment was created in your deal: ' . $deal->name;

        // Log the notification
        $this->logNotification($notifiable, 'push', $subject, $message, $template);

        return [
            'deal_id' => $deal->id,
            'deal_name' => $deal->name,
            'investment_id' => $this->investment->id,
            'investment_amount' => $this->investment->investment_amount,
            'message' => $message,
        ];
    }

    protected function getNotificationTemplate()
    {
        return DB::table('notification_templates')
            ->where('act', 'investment_created')
            ->first();
    }

    protected function getShortcodes($notifiable)
    {
        $deal = $this->investment->deal;
        $partner = $this->investment->investor;

        return [
            '{deal_name}' => $deal->name,
            '{investment_amount}' => $this->investment->investment_amount,
            '{investor_name}' => $partner->name ?? 'N/A',
            '{role}' => $deal->partners()->where('admin_id', $partner->id)->first()->pivot->role ?? 'N/A',
            '{deal_owner_name}' => $notifiable->name,
            '{deal_id}' => $deal->id,
        ];
    }

    protected function replaceShortcodes($content, $shortcodes)
    {
        return str_replace(
            array_keys($shortcodes),
            array_values($shortcodes),
            $content
        );
    }

    protected function logNotification($notifiable, $type, $subject, $message, $template)
    {
        DB::table('notification_logs')->insert([
            'user_id' => $notifiable->id,
            'sender' => $template->email_sent_from_name ?? config('app.name'),
            'sent_from' => $template->email_sent_from_address ?? 'sign@isign.click',
            'sent_to' => $type === 'email' ? $notifiable->email : null,
            'subject' => $subject,
            'message' => $message,
            'notification_type' => $type,
            'user_read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }
    
}