<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct(private StatisticsService $statisticsService) {}

    public function show(Request $request): JsonResponse
    {
        return $this->success([
            'user' => new UserResource($request->user()),
            'stats' => $this->statisticsService->profileStats($request->user()),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return $this->success(new UserResource($user), '资料更新成功');
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update([
            'password' => $request->input('password'),
        ]);

        return $this->success(null, '密码修改成功');
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $user = $request->user();

        // 删除旧头像
        if ($user->avatar) {
            $oldPath = public_path($user->avatar);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // 保存新头像
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $uploadDir = public_path('uploads/avatars');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $file->move($uploadDir, $filename);

        // 更新数据库
        $avatarPath = '/uploads/avatars/' . $filename;
        $user->update(['avatar' => $avatarPath]);

        return $this->success(['avatar' => $avatarPath], '头像修改成功');
    }
}
