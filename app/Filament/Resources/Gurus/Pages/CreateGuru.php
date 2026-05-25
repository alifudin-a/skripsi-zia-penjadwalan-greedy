<?php

namespace App\Filament\Resources\Gurus\Pages;

use App\Filament\Resources\Gurus\GuruResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateGuru extends CreateRecord
{
    protected static string $resource = GuruResource::class;

    /**
     * Mutate data sebelum create guru
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /**
         * Create akun user guru
         */
        $user = User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'guru',
        ]);

        /**
         * Relasikan ke guru
         */
        $data['user_id'] = $user->id;

        /**
         * Hapus field sementara
         * karena bukan column gurus
         */
        unset($data['email']);
        unset($data['password']);

        return $data;
    }
}