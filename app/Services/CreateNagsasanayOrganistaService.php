<?php

namespace App\Services;

use App\Models\MangAawit;
use App\Models\Nagsasanay;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateNagsasanayOrganistaService
{
    public function create(array $data, int $adminId): Nagsasanay
    {
        $this->validate($data);

        return DB::transaction(function () use ($data) {

            $mangAawit = MangAawit::findOrFail($data['mang_aawit_record_id']);

            // Use mang-aawit's existing ID
            $nagsasanay = Nagsasanay::create([
                'mang_aawit_id'       => $mangAawit->mang_aawit_id,
                'training_track'      => 'Organista',
                'mang_aawit_record_id'=> $mangAawit->id,
                'training_category'   => 'Organista',
                'training_start_date' => $data['training_start_date'],
                'trainer_name'        => $data['trainer_name'],
                'progress_stage'      => 'Newly Registered',
                'training_status'     => 'Active',
                'remarks'             => $data['remarks'] ?? null,
            ]);

            return $nagsasanay->fresh();
        });
    }

    protected function validate(array $data): void
    {
        $errors = [];

        if (empty($data['mang_aawit_record_id'])) $errors['mang_aawit_record_id'] = ['Please select a Mang-aawit.'];
        if (empty($data['training_start_date']))   $errors['training_start_date']  = ['Training start date is required.'];
        if (empty($data['trainer_name']))          $errors['trainer_name']          = ['Trainer name is required.'];

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}