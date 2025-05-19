<?php

namespace App\Filament\Resources;

use App\Enums\ContentType;
use App\Enums\Role;
use App\Filament\Resources\ContentResource\Pages;
use App\Models\Content;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static ?string $slug = 'contents';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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

        return $record->getAttribute('author_id') === $user->id || $user->role === Role::Admin->value;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $record->getAttribute('author_id') === $user->id || $user->role === Role::Admin->value;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Content Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(Content::class, 'slug', fn($record) => $record),
                        Select::make('type')
                            ->options(ContentType::class),
                        SpatieMediaLibraryFileUpload::make('cover_image')
                            ->collection('cover_images'),
                        Textarea::make('description')
                            ->label('Excerpt or Short Description')
                            ->maxLength(500),
                        RichEditor::make('body'),
                        DatePicker::make('published_at')
                            ->label('Published Date'),
                        Select::make('author_id')
                            ->label('Author')
                            ->options(fn() => User::where('role', Role::Admin->value)
                                ->orWhere('role', Role::Editor->value)
                                ->get()
                                ->pluck('name', 'id'))
                            ->preload()
                            ->searchable(),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable(),
                        Placeholder::make('created_at')
                            ->label('Created Date')
                            ->content(fn(?Content $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                        Placeholder::make('updated_at')
                            ->label('Last Modified Date')
                            ->content(fn(?Content $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover_image')
                    ->collection('cover_images')
                    ->conversion('thumb'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type'),
                TextColumn::make('author.name')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Published Date')
                    ->date(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        QueryBuilder\Constraints\TextConstraint::make('title'),
                        QueryBuilder\Constraints\TextConstraint::make('description'),
                        QueryBuilder\Constraints\TextConstraint::make('body'),
                        QueryBuilder\Constraints\SelectConstraint::make('type')
                            ->options(ContentType::class)
                            ->multiple(),
                        QueryBuilder\Constraints\DateConstraint::make('published_at'),
                        QueryBuilder\Constraints\RelationshipConstraint::make('category')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->preload()
                                    ->searchable()
                                    ->multiple(),
                            ),
                        QueryBuilder\Constraints\RelationshipConstraint::make('author')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->modifyRelationshipQueryUsing(fn (Builder $query) => $query->where('role', Role::Admin->value)->orWhere('role', Role::Editor->value))
                                    ->preload()
                                    ->searchable()
                                    ->multiple(),
                            ),
                        QueryBuilder\Constraints\DateConstraint::make('created_at'),
                        QueryBuilder\Constraints\DateConstraint::make('updated_at'),
                    ]),
                SelectFilter::make('type')
                    ->options(ContentType::class),
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('author')
                    ->searchable()
                    ->relationship('author', 'name')
                    ->preload(),
            ], layout: FiltersLayout::AboveContentCollapsible)
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
            'index' => Pages\ListContents::route('/'),
            'create' => Pages\CreateContent::route('/create'),
            'edit' => Pages\EditContent::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'category.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->category) {
            $details['Category'] = $record->category->name;
        }

        return $details;
    }
}
