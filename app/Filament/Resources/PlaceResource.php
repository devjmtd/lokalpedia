<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\PlaceResource\Pages;
use App\Models\Place;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Tapp\FilamentGoogleAutocomplete\Forms\Components\GoogleAutocomplete;

class PlaceResource extends Resource
{
    protected static ?string $model = Place::class;

    protected static ?string $slug = 'places';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function canCreate(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->role === Role::Admin->value || $user->role === Role::Editor->value;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->role === Role::Admin->value || $user->role === Role::Editor->value;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->role === Role::Admin->value;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Place Information')
                    ->schema([
                        GoogleAutocomplete::make('google_search')
                            ->label('Search Here')
                            ->countries(['PH'])
                            ->withFields([
                                TextInput::make('name')
                                    ->required()
                                    ->extraInputAttributes([
                                        'data-google-field' => 'formatted_address',
                                        'data-google-value' => 'long_name',
                                    ]),
                                TextInput::make('latitude')
                                    ->extraFieldWrapperAttributes([
                                        'class' => 'hidden'
                                    ])
                                    ->extraInputAttributes([
                                        'data-google-field' => 'latitude',
                                    ]),
                                TextInput::make('longitude')
                                    ->extraFieldWrapperAttributes([
                                        'class' => 'hidden'
                                    ])
                                    ->extraInputAttributes([
                                        'data-google-field' => 'longitude',
                                    ]),
                            ])->columns(1),
                        Textarea::make('description')
                            ->maxLength(1500),
                        Placeholder::make('created_at')
                            ->label('Created Date')
                            ->content(fn(?Place $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                        Placeholder::make('updated_at')
                            ->label('Last Modified Date')
                            ->content(fn(?Place $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description'),

                TextColumn::make('latitude'),

                TextColumn::make('longitude'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlaces::route('/'),
            'create' => Pages\CreatePlace::route('/create'),
            'edit' => Pages\EditPlace::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }
}
