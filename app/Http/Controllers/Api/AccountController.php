<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccountController extends BaseApiController
{
    /**
     * Permanently delete the authenticated user from `users` (cannot log in again).
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $uid = $this->getUserId($request);

        if (empty($uid)) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'Unauthorized! Please login first.',
            ], 401);
        }

        $user = User::find($uid);

        if (! $user) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'User not found!',
            ], 401);
        }

        try {
            DB::transaction(function () use ($user) {
                $userId = $user->id;

                $user->tokens()->delete();

                $this->deleteUserRowsIfTableExists('user_push_devices', $userId);
                $this->deleteUserRowsIfTableExists('user_notifications', $userId);
                $this->deleteUserRowsIfTableExists('user_registration_responses', $userId);
                $this->deleteUserRowsIfTableExists('campaign_collaborators', $userId);
                $this->deleteUserRowsIfTableExists('comments', $userId);
                $this->deleteUserRowsIfTableExists('campaigns', $userId);
                $this->deleteUserRowsIfTableExists('deposits', $userId);
                $this->deleteUserRowsIfTableExists('withdrawals', $userId);
                $this->deleteUserRowsIfTableExists('transactions', $userId);
                $this->deleteUserRowsIfTableExists('sessions', $userId);

                $user->delete();
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'Unable to delete account. Please contact support.',
            ], 401);
        }

        return response()->json([
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Account Delete Successfully!!',
        ]);
    }

    private function deleteUserRowsIfTableExists(string $table, int $userId): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'user_id')) {
            return;
        }

        DB::table($table)->where('user_id', $userId)->delete();
    }
}
