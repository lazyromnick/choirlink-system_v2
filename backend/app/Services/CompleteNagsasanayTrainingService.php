<?php

namespace App\Services;

use App\Models\MangAawit;
use App\Models\Nagsasanay;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompleteNagsasanayTrainingService
{
    public function complete(Nagsasanay $nagsasanay, array $membershipData): MangAawit
    {
        return DB::transaction(function () use ($nagsasanay, $membershipData) {

            if ($nagsasanay->training_track === 'Mang-aawit') {
                return $this->completeMangAawitTrack($nagsasanay, $membershipData);
            }

            return $this->completeOrganistaTrack($nagsasanay);
        });
    }

    protected function completeMangAawitTrack(Nagsasanay $nagsasanay, array $membershipData): MangAawit
    {
        $this->validateMembershipData($membershipData);

        // Find the mang_aawit record created during nagsasanay registration
        $mangAawit = MangAawit::where('mang_aawit_id', $nagsasanay->mang_aawit_id)->firstOrFail();

        // Update personal info in case it was edited
        $mangAawit->update([
            'first_name'  => $nagsasanay->first_name,
            'middle_name' => $nagsasanay->middle_name,
            'last_name'   => $nagsasanay->last_name,
            'suffix'      => $nagsasanay->suffix,
            'birthday'    => $nagsasanay->birthday,
            'sex'         => $nagsasanay->sex,
            'address'     => $nagsasanay->address,
        ]);

        // Close nagsasanay membership period
        $mangAawit->currentMembership?->update([
            'is_current' => false,
            'end_date'   => now()->toDateString(),
        ]);

        // Create new Regular membership period
        $mangAawit->memberships()->create([
            'mang_aawit_record_id'      => $mangAawit->id,
            'start_date'                => now()->toDateString(),
            'is_current'                => true,
            'membership_classification' => $membershipData['membership_classification'],
            'designation'               => 'Regular',
            'tungkulin'                 => 'Mang-aawit',
            'tungkulin_status'          => 'Active',
            'membership_status'         => 'In-locale',
            'mang_aawit_in'             => $membershipData['mang_aawit_in'],
            'previous_locale'           => $membershipData['previous_locale'] ?? null,
            'destination_locale'        => $membershipData['destination_locale'] ?? null,
            'transfer_date'             => $membershipData['transfer_date'] ?? null,
        ]);

        // Mark nagsasanay as completed and soft delete
        $nagsasanay->update(['progress_stage' => 'Completed']);
        $nagsasanay->delete();

        return $mangAawit->fresh(['currentMembership', 'account']);
    }

    protected function completeOrganistaTrack(Nagsasanay $nagsasanay): MangAawit
    {
        $mangAawit = MangAawit::findOrFail($nagsasanay->mang_aawit_record_id);

        // Update tungkulin to Organista on current membership
        $mangAawit->currentMembership?->update([
            'tungkulin' => 'Organista',
        ]);

        // Mark nagsasanay as completed and soft delete
        $nagsasanay->update(['progress_stage' => 'Completed']);
        $nagsasanay->delete();

        return $mangAawit->fresh(['currentMembership', 'account']);
    }

    protected function validateMembershipData(array $data): void
    {
        $errors         = [];
        $classification = $data['membership_classification'] ?? '';

        if (!in_array($classification, ['Taga-lokal', 'Transfer-In', 'Transfer-Out'])) {
            $errors['membership_classification'] = ['Invalid membership classification.'];
        }

        if (empty($data['mang_aawit_in'])) {
            $errors['mang_aawit_in'] = ['Worship service is required.'];
        }

        if ($classification === 'Transfer-In') {
            if (empty($data['previous_locale'])) $errors['previous_locale'] = ['Previous locale is required.'];
            if (empty($data['transfer_date']))   $errors['transfer_date']   = ['Transfer date is required.'];
        }

        if ($classification === 'Transfer-Out') {
            if (empty($data['destination_locale'])) $errors['destination_locale'] = ['Destination locale is required.'];
            if (empty($data['transfer_date']))       $errors['transfer_date']      = ['Transfer date is required.'];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}