<?php

namespace App\Enums;

enum DealStatus: string
{
    case Lead = 'lead';
    case AppointmentSet = 'appointment_set';
    case QuoteSent = 'quote_sent';
    case ClosedWon = 'closed_won';
    case ClosedLost = 'closed_lost';

    public function label(): string
    {
        return match ($this) {
            self::Lead => 'Lead',
            self::AppointmentSet => 'Appointment Set',
            self::QuoteSent => 'Quote Sent',
            self::ClosedWon => 'Closed Won',
            self::ClosedLost => 'Closed Lost',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Lead => 'gray',
            self::AppointmentSet => 'blue',
            self::QuoteSent => 'yellow',
            self::ClosedWon => 'green',
            self::ClosedLost => 'red',
        };
    }

    public function isClosed(): bool
    {
        return $this === self::ClosedWon;
    }
}
