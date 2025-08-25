<?php
namespace App\Services\User;
use App\Models\User;
use App\Enums\StatusEnum;
use App\Helpers\ApiResponse;

class BulkActionService{
    public function bulkAction(array $validated){
        $query = User::whereIn('id', $validated['ids']);
        switch ($validated['action']) {
            case 'active':
                $query->update(['is_active' => StatusEnum::ACTIVE]);
                break;
            case 'inActive':
                $query->update(['is_active' => StatusEnum::INACTIVE]);
                $query->each(function ($user) {
                    $user->tokens()->delete();
                });
                break;
            case 'delete':
                $query->each(function ($user) {
                    $user->tokens()->delete();
                });
                $query->delete();
                break;
        }
        return ApiResponse::success([],"Users {$validated['action']}d successfully");
    }
}
