<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MangAawit;
use App\Models\MangAawitQrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'mang_aawit_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $mangAawit = MangAawit::with(['account', 'currentMembership', 'purokGrupo'])
            ->where('mang_aawit_id', $request->mang_aawit_id)
            ->first();

        $account = $mangAawit?->account;

        if (
            !$mangAawit ||
            !$account ||
            $account->account_status !== 'Active' ||
            !Hash::check($request->password, $account->password)
        ) {
            return response()->json([
                'message' => 'Invalid Member ID or password.',
            ], 401);
        }

        $token = $account->createToken('mobile-app')->plainTextToken;

        $membership = $mangAawit->currentMembership;

        return response()->json([
            'token' => $token,
            'must_change_password' => $account->must_change_password,
            'member' => [
                'mang_aawit_id' => $mangAawit->mang_aawit_id,
                'full_name' => $mangAawit->full_name,
                'birthday' => $mangAawit->birthday?->toDateString(),
                'age' => $mangAawit->age,
                'sex' => $mangAawit->sex,
                'address' => $mangAawit->address,
                'purok_grupo' => $mangAawit->purokGrupo?->name,
                'membership' => [
                    'tungkulin' => $membership?->tungkulin,
                    'tungkulin_status' => $membership?->tungkulin_status,
                    'designation' => $membership?->designation,
                    'membership_classification' => $membership?->membership_classification,
                    'membership_status' => $membership?->membership_status,
                    'mang_aawit_in' => $membership?->mang_aawit_in,
                ],
            ],
        ]);
    }
    
    public function qrResolve(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $tokenHash = hash('sha256', $request->token);

        $qrToken = MangAawitQrToken::with('mangAawitAccount.mangAawit')
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->first();

        $mangAawit = $qrToken?->mangAawitAccount?->mangAawit;

        if (!$mangAawit || $mangAawit->account->account_status !== 'Active') {
            return response()->json([
                'message' => 'QR code is invalid or expired. Please scan your QR code again.',
            ], 404);
        }

        return response()->json([
            'mang_aawit_id' => $mangAawit->mang_aawit_id,
            'full_name' => $mangAawit->full_name,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request)
    {
        $account = $request->user();
        $mangAawit = $account->mangAawit;
        $membership = $mangAawit->currentMembership;

        return response()->json([
            'member' => [
                'mang_aawit_id' => $mangAawit->mang_aawit_id,
                'full_name' => $mangAawit->full_name,
                'birthday' => $mangAawit->birthday?->toDateString(),
                'age' => $mangAawit->age,
                'sex' => $mangAawit->sex,
                'address' => $mangAawit->address,
                'purok_grupo' => $mangAawit->purokGrupo?->name,
                'membership' => [
                    'tungkulin' => $membership?->tungkulin,
                    'tungkulin_status' => $membership?->tungkulin_status,
                    'designation' => $membership?->designation,
                    'membership_classification' => $membership?->membership_classification,
                    'membership_status' => $membership?->membership_status,
                    'mang_aawit_in' => $membership?->mang_aawit_in,
                ],
            ],
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $account = $request->user();

        if (!Hash::check($request->current_password, $account->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 401);
        }

        $account->password = $request->new_password;
        $account->must_change_password = false;
        $account->save();

        $account->histories()->create([
            'mang_aawit_account_id' => $account->id,
            'event_type' => 'Password Reset',
            'previous_status' => null,
            'new_status' => null,
            'reason' => null,
            'changed_by' => null,
            'actor_type' => 'System',
        ]);

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }
}