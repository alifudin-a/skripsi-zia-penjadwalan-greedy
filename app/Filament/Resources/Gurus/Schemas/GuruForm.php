<?php

namespace App\Filament\Resources\Gurus\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;

class GuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /**
                 * NIP guru
                 */
                TextInput::make('nip')
                    ->required(),

                /**
                 * Nama guru
                 */
                TextInput::make('nama')
                    ->required(),

                /**
                 * Nomor HP
                 */
                TextInput::make('no_hp'),

                /**
                 * Alamat
                 */
                Textarea::make('alamat'),

                /**
                 * Relasi guru ↔ mata pelajaran
                 */
                CheckboxList::make('mataPelajarans')
                    ->relationship('mataPelajarans', 'nama')
                    ->columns(2)
                    ->label('Mata Pelajaran'),

                /**
                 * Section akun login
                 *
                 * HANYA tampil saat create
                 * supaya tidak bentrok state edit/create
                 */
                Section::make('Akun Login')

                    ->visibleOn('create')

                    ->schema([

                        /**
                         * Email login guru
                         */
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->dehydrated(false)
                            ->autocomplete(false),

                        /**
                         * Password login guru
                         */
                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->dehydrated(false)
                            ->autocomplete(false),
                    ]),
            ]);
    }
}
