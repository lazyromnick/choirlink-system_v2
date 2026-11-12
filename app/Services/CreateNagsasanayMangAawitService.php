<?php

namespace App\Services;

use App\Models\MangAawitAccount;
use App\Models\MangAawitAccountHistory;
use App\Models\MangAawitQrToken;
use App\Models\Nagsasanay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreateNagsasanayMangAawitService
{
    public function __construct(
        protected MangAawitIdGenerator $idGenerator
    ) {}

    public function create(array $data, int $adminId): array
    {
        $this->validate($data);

        return DB::transaction(function () use ($data, $adminId) {

            // 1. Generate Member ID
            $mangAawitId = $this->idGenerator->generate();

            // 2. Create nagsasanay record
            $nagsasanay = Nagsasanay::create([
                'mang_aawit_id'       => $mangAawitId,
                'training_track'      => 'Mang-aawit',
                'first_name'          => $data['first_name'],
                'middle_name'         => $data['middle_name'] ?? null,
                'last_name'           => $data['last_name'],
                'suffix'              => $data['suffix'] ?? null,
                'birthday'            => $data['birthday'],
                'sex'                 => $data['sex'],
                'address'             => $data['address'],
                'date_registered'     => $data['date_registered'] ?? now()->toDateString(),
                'training_category'   => 'Mang-aawit',
                'training_start_date' => $data['training_start_date'],
                'trainer_name'        => $data['trainer_name'],
                'progress_stage'      => 'Newly Registered',
                'training_status'     => 'Active',
                'remarks'             => $data['remarks'] ?? null,
                'purok_grupo_id'      => $data['purok_grupo_id'] ?? null,
            ]);

            // 3. Generate birthday-based initial password
            $initialPassword = $nagsasanay->birthday->format('Ymd');

            // 4. Create login account
            // We reuse mang_aawit_accounts but need a placeholder in mang_aawits first
            // So we create a temporary mang_aawit record marked as nagsasanay
            $mangAawit = \App\Models\MangAawit::create([
                'mang_aawit_id'   => $mangAawitId,
                'first_name'      => $data['first_name'],
                'middle_name'     => $data['middle_name'] ?? null,
                'last_name'       => $data['last_name'],
                'suffix'          => $data['suffix'] ?? null,
                'birthday'        => $data['birthday'],
                'sex'             => $data['sex'],
                'address'         => $data['address'],
                'date_registered' => $data['date_registered'] ?? now()->toDateString(),
                'purok_grupo_id'  => $data['purok_grupo_id'] ?? null,  // ADD THIS
            ]);

            // 5. Create membership period as Nagsasanay
            $mangAawit->memberships()->create([
                'mang_aawit_record_id'      => $mangAawit->id,
                'start_date'                => $data['training_start_date'],
                'is_current'                => true,
                'membership_classification' => 'Taga-lokal',
                'designation'               => 'Nagsasanay',
                'tungkulin'                 => 'Mang-aawit',
                'tungkulin_status'          => 'Active',
                'membership_status'         => 'In-locale',
                'mang_aawit_in'             => $data['mang_aawit_in'] ?? 'AWS',
            ]);

            // 6. Create account
            $account = MangAawitAccount::create([
                'mang_aawit_record_id' => $mangAawit->id,
                'password'             => Hash::make($initialPassword),
                'account_status'       => 'Active',
                'must_change_password' => true,
            ]);

            // 7. Account history
            MangAawitAccountHistory::create([
                'mang_aawit_account_id' => $account->id,
                'event_type'            => 'Account Created',
                'previous_status'       => null,
                'new_status'            => 'Active',
                'reason'                => null,
                'changed_by'            => $adminId,
                'actor_type'            => 'Admin',
            ]);

            // 8. QR token
            $rawToken  = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);

            MangAawitQrToken::create([
                'mang_aawit_account_id' => $account->id,
                'token_hash'            => $tokenHash,
                'created_by'            => $adminId,
            ]);

            // 9. Link nagsasanay record to mang_aawit record
            $nagsasanay->update(['mang_aawit_record_id' => null]); // stays separate until graduation

            return [
                'nagsasanay'      => $nagsasanay->fresh(),
                'mangAawit'       => $mangAawit->fresh(),
                'initialPassword' => $initialPassword,
                'qrToken'         => $rawToken,
            ];
        });
    }

    protected function validate(array $data): void
    {
        $errors = [];

        if (empty($data['first_name']))          $errors['first_name']          = ['First name is required.'];
        if (empty($data['last_name']))           $errors['last_name']           = ['Last name is required.'];
        if (empty($data['birthday']))            $errors['birthday']            = ['Birthday is required.'];
        if (empty($data['address']))             $errors['address']             = ['Address is required.'];
        if (empty($data['training_start_date'])) $errors['training_start_date'] = ['Training start date is required.'];
        if (empty($data['trainer_name']))        $errors['trainer_name']        = ['Trainer name is required.'];

        if (!in_array($data['sex'] ?? '', ['Male', 'Female'])) {
            $errors['sex'] = ['Sex must be Male or Female.'];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}