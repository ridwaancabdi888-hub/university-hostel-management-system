<?php

namespace App\Notifications\Messages;

class SmsMessage
{
    public string $content = '';

    public function content(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
