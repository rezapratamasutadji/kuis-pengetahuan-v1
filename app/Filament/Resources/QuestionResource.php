<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Category;
use App\Models\Question;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    private const ROUNDS = [
        'qualification' => 'Kualifikasi',
        'semifinal' => 'Semi Final',
        'final' => 'Final',
    ];

    private const DIFFICULTIES = [
        'easy' => 'Easy',
        'medium' => 'Medium',
        'hard' => 'Hard',
    ];

    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Manajemen Kuis';

    protected static ?string $modelLabel = 'Soal';

    protected static ?string $pluralModelLabel = 'Soal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                    ->label('Kategori')
                    ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('round')
                    ->label('Babak')
                    ->options(self::ROUNDS)
                    ->required(),
                TextInput::make('number')
                    ->label('Nomor soal')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(25)
                    ->required(),
                Select::make('difficulty')
                    ->label('Tingkat kesulitan')
                    ->options(self::DIFFICULTIES)
                    ->required(),
                Textarea::make('prompt')
                    ->label('Pertanyaan')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('option_a')
                    ->label('Opsi A')
                    ->required(),
                TextInput::make('option_b')
                    ->label('Opsi B')
                    ->required(),
                TextInput::make('option_c')
                    ->label('Opsi C')
                    ->required(),
                TextInput::make('option_d')
                    ->label('Opsi D')
                    ->required(),
                Select::make('correct_option')
                    ->label('Jawaban benar')
                    ->options([
                        'a' => 'A',
                        'b' => 'B',
                        'c' => 'C',
                        'd' => 'D',
                    ])
                    ->required(),
                Textarea::make('explanation')
                    ->label('Penjelasan')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('round')
                    ->label('Babak')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::ROUNDS[$state] ?? $state),
                TextColumn::make('number')
                    ->label('Nomor')
                    ->sortable(),
                TextColumn::make('difficulty')
                    ->label('Level')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::DIFFICULTIES[$state] ?? $state),
                TextColumn::make('prompt')
                    ->label('Pertanyaan')
                    ->limit(70)
                    ->searchable(),
                TextColumn::make('correct_option')
                    ->label('Jawaban')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->since(),
            ])
            ->defaultSort('category_id')
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
