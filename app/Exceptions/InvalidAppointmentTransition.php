<?php

namespace App\Exceptions;

use DomainException;

class InvalidAppointmentTransition extends DomainException
{
    public static function make(string $from, string $to): self
    {
        return new self("Cannot change appointment from '{$from}' to '{$to}'.");
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->getMessage()], 422);
        }
        return back()->withErrors(['status' => $this->getMessage()]);
    }
}