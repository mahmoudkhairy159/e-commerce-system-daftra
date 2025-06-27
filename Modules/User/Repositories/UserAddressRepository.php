<?php

namespace Modules\User\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\User\Models\User;
use Modules\User\Models\UserAddress;
use Prettus\Repository\Eloquent\BaseRepository;

class UserAddressRepository extends BaseRepository
{
    public function model()
    {
        return UserAddress::class;
    }
    public function getAll()
    {
        return $this->model
            ->with('user')
            ->filter(request()->all())
            ->orderBy('created_at', 'desc');
    }
    public function getByUserId($userId)
    {
        return $this->model
            ->where('user_id', $userId);
    }




    public function createOne(array $data)
    {
        try {
            DB::beginTransaction();
            $created = $this->model->create($data);
            if (!empty($data['is_default']) && $data['is_default'] == true) {
                $user = User::findOrFail($data['user_id']);
                $user->default_address_id = $created->id;
                $user->save();
            }
            DB::commit();

            return $created;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            DB::rollBack();
            return false;
        }
    }

    public function updateOne(array $data, int $id)
    {
        try {
            DB::beginTransaction();

            $model = $this->model->findOrFail($id);
            $updated = $model->update($data);
            if (!empty($data['is_default']) && $data['is_default'] == true) {
                $user = User::findOrFail($model->user_id);
                $user->default_address_id = $model->id;
                $user->save();
            }
            DB::commit();
            return $updated;
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }
    public function updateOneByUser(array $data, int $id): bool
    {
        try {
            DB::beginTransaction();

            // Find address that belongs to authenticated user
            $model = $this->model
                ->where('user_id', auth()->guard('user-api')->id())
                ->findOrFail($id);

            // Update address
            $updated = $model->update($data);

            // Handle setting as default address if requested
            if (!empty($data['is_default']) && $data['is_default'] === true) {
                $user = User::findOrFail($model->user_id);
                $user->update(['default_address_id' => $model->id]);
            }

            DB::commit();

            return $updated;

        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
    public function deleteOne(int $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->findOrFail($id);

            $deleted = $model->delete();
            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }
    public function deleteOneByUser(int $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model
                ->where('user_id', auth()->guard('user-api')->id())
                ->findOrFail($id);

            $deleted = $model->delete();
            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }
    public function setDefaultAddress(int $id, User $user): bool
    {
        try {
            DB::beginTransaction();

            // Verify address exists and belongs to user
            $address = $this->model
                ->where('user_id', $user->id)
                ->findOrFail($id);

            // Update user's default address
            $updated = $user->update([
                'default_address_id' => $id
            ]);

            DB::commit();

            return $updated;

        } catch (\Throwable $th) {
            DB::rollBack();

            return false;
        }

    }




}