<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MangAawitIdGenerator
{
    public function generate(): string
    {
        // Get next sequential number from PostgreSQL
        $sequence = DB::selectOne("SELECT nextval('mang_aawit_id_sequence') AS val");
        $number = str_pad($sequence->val, 4, '0', STR_PAD_LEFT);

        // Generate random 4-character segment (A-Z and 0-9)
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomSegment = '';
        for ($i = 0; $i < 4; $i++) {
            $randomSegment .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return "L-{$randomSegment}-{$number}";
    }
}