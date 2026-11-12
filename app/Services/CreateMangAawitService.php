<?php

namespace App\Services;

use App\Models\MangAawit;
use App\Models\MangAawitAccount;
use App\Models\MangAawitAccountHistory;
use App\Models\MangAawitQrToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreateMangAawitService
{
    public function __construct(
        protected MangAawitIdGenerator $idGenerator
    ) {}

    public function create(array $personalData, array $membershipData, int $adminId): array
    {
        $this->validatePersonal($personalData);
        $this->validateMembership($membershipData);

        return DB::transaction(function () use ($personalData, $membershipData, $adminId) {

            // 1. Generate Member ID
            $mangAawitId = $this->idGenerator->generate();

            // 2. Set date_registered default if not provided
            $personalData['date_registered'] ??= now()->toDateString();

            // 3. Create personal record
            $personalData['mang_aawit_id'] = $mangAawitId;
            $mangAawit = MangAawit::create($personalData);

            // 4. Generate birthday-based initial password
            $initialPassword = $mangAawit->birthday->format('Ymd');

            // 5. Create membership period
            $membershipData['mang_aawit_record_id'] = $mangAawit->id;
            $membershipData['is_current']            = true;
            $membershipData['start_date']          ??= now()->toDateString();
            $membershipData['membership_status']     = 'In-locale';
            $mangAawit->memberships()->create($membershipData);

            // 6. Create member account
            $account = MangAawitAccount::create([
                'mang_aawit_record_id' => $mangAawit->id,
                'password'             => Hash::make($initialPassword),
                'account_status'       => 'Active',
                'must_change_password' => true,
            ]);

            // 7. Record account created history
            MangAawitAccountHistory::create([
                'mang_aawit_account_id' => $account->id,
                'event_type'            => 'Account Created',
                'previous_status'       => null,
                'new_status'            => 'Active',
                'reason'                => null,
                'changed_by'            => $adminId,
                'actor_type'            => 'Admin',
            ]);

            // 8. Generate secure QR token
            $rawToken  = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);

            MangAawitQrToken::create([
                'mang_aawit_account_id' => $account->id,
                'token_hash'            => $tokenHash,
                'created_by'            => $adminId,
            ]);

            // Return raw credentials — only shown once to the admin
            return [
                'mangAawit'       => $mangAawit->fresh(['currentMembership', 'account']),
                'initialPassword' => $initialPassword,
                'qrToken'         => $rawToken,
            ];
        });
    }

    protected function validatePersonal(array $data): void
    {
        $errors = [];

        if (empty($data['first_name']))  $errors['first_name']  = ['First name is required.'];
        if (empty($data['last_name']))   $errors['last_name']   = ['Last name is required.'];
        if (empty($data['birthday']))    $errors['birthday']    = ['Birthday is required.'];
        if (empty($data['address']))     $errors['address']     = ['Address is required.'];

        if (!in_array($data['sex'] ?? '', ['Male', 'Female'])) {
            $errors['sex'] = ['Sex must be Male or Female.'];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected function validateMembership(array $data): void
    {
        $errors        = [];
        $classification = $data['membership_classification'] ?? '';
        $designation   = $data['designation'] ?? '';
        $tungkulin     = $data['tungkulin'] ?? '';
        $tungkulinStatus = $data['tungkulin_status'] ?? '';
        $mangAawitIn   = $data['mang_aawit_in'] ?? '';

        if (!in_array($classification, ['Taga-lokal', 'Transfer-In', 'Transfer-Out'])) {
            $errors['membership_classification'] = ['Invalid membership classification.'];
        }

        if (!in_array($designation, ['Regular', 'Nagsasanay'])) {
            $errors['designation'] = ['Invalid designation.'];
        }

        if (!in_array($tungkulin, ['Mang-aawit', 'Organista'])) {
            $errors['tungkulin'] = ['Invalid tungkulin.'];
        }

        if (!in_array($tungkulinStatus, ['Active', 'Inactive'])) {
            $errors['tungkulin_status'] = ['Invalid tungkulin status.'];
        }

        if (!in_array($mangAawitIn, ['AWS', 'CWS'])) {
            $errors['mang_aawit_in'] = ['Invalid worship service. Must be AWS or CWS.'];
        }

        // Transfer field rules
        if ($classification === 'Taga-lokal') {
            if (!empty($data['previous_locale']) || !empty($data['destination_locale']) || !empty($data['transfer_date'])) {
                $errors['membership_classification'] = ['Taga-lokal must not have transfer information.'];
            }
        }

        if ($classification === 'Transfer-In') {
            if (empty($data['previous_locale']))  $errors['previous_locale']  = ['Previous locale is required for Transfer-In.'];
            if (empty($data['transfer_date']))    $errors['transfer_date']    = ['Transfer date is required for Transfer-In.'];
            if (!empty($data['destination_locale'])) $errors['destination_locale'] = ['Transfer-In must not have a destination locale.'];
        }

        if ($classification === 'Transfer-Out') {
            if (empty($data['destination_locale'])) $errors['destination_locale'] = ['Destination locale is required for Transfer-Out.'];
            if (empty($data['transfer_date']))      $errors['transfer_date']      = ['Transfer date is required for Transfer-Out.'];
            if (!empty($data['previous_locale']))   $errors['previous_locale']    = ['Transfer-Out must not have a previous locale.'];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}