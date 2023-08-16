<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('email'),
                Forms\Components\Repeater::make('roles')
                    ->relationship('userRoles')
                    ->schema([
                        Forms\Components\Select::make('role_id')
                            ->label('Role')
                            ->options(self::rolesRoleOptions(...))
                            ->reactive() // removing this will eliminate the bad behavior, but will also eliminate the good behavior 😕
                            ->required(),
                    ])
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]);
    }

    private static function rolesRoleOptions(Get $get, $state)
    {
        // This is the way that was proposed...
        $ignore = collect($get('../../roles'))->pluck('role_id')->diff([$state]);

        /**
         * This is more what I would prefer:
         *  - filtering out null
         *  - transforming to force the ID to be an int...in case this was part of the problem (it doesn't appear to be)
         *  - sorting to make the test easier
         *  - calling values() to get a "clean" array
         */
        // $ignore = collect($get('../../roles'))
        //     ->pluck('role_id')
        //     ->diff([$state])
        //     ->filter()
        //     // ->transform(fn ($id): int => (int) $id)
        //     // ->sort()
        //     ->values();

        // \Log::debug($state);
        // \Log::debug(gettype($state));
        // \Log::debug(collect($get('../../roles'))->pluck('role_id'));
        // \Log::debug(collect($get('../../roles'))->pluck('role_id')->diff([$state])); // <-- here you can see the output changes
        // \Log::debug($ignore);
        // \Log::debug('-----------------------------------------------------');

        return Role::query()->whereNotIn(
                'id',
                $ignore,
            )
            // ->orderBy('id', 'asc') // this is done to more easily demonstrate the issue (adding an item that has a lower ID than a previous item seems to trigger the unexpected change to the previous item(s))
            ->pluck('name', 'id');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
