<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = 'Usuarios';
    protected static ?string $slug = 'users';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Configuraciones';


   
  
 protected static ?string $navigationIcon =  'heroicon-o-user';
 

    public static function form(Form $form): Form
    {
        return $form
    ->schema([
        Forms\Components\Section::make('Información del Usuario')
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre y Apellido')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ejemplo: Juan Pérez'),

                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Ejemplo: usuario@email.com')
                    ->helperText('Ingrese un correo válido para el usuario.'),

                Forms\Components\TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // Obligatorio solo en creación
                    ->hiddenOn('edit') // Ocultar en edición
                    ->minLength(8)
                  
                    ->placeholder('Mínimo 8 caracteres')
                    ->helperText('Debe contener al menos 8 caracteres.'),

                Forms\Components\Select::make('roles')
                    ->label('Rol del Usuario')
                    ->options([
                        'admin' => 'Administrador',
                        'user' => 'Usuario',
                    ])
                    ->required()
                    ->multiple()
                    ->relationship('roles', 'name', function (Builder $query) {
                        if (auth()->user()->hasRole('Administrador')) {
                            return null; // No aplicamos filtro si el usuario tiene el rol 'Admin'
                        }
                        return $query->where('name', '!=', 'Administrador'); // Ocultar rol de Admin si no es administrador
                    })
                    ->preload()
                    ->placeholder('Seleccione un rol')
                    ->helperText('Seleccione el rol adecuado para el usuario.'),

                Forms\Components\FileUpload::make('profile_photo_path')
                    ->label('Foto de Perfil')
                    ->avatar()
                    ->preserveFilenames()
                    ->imageEditor()
                    ->downloadable()
                    ->directory('profile-photos')
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->maxSize(2048)
                    ->helperText('Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB.'),
            ])
            ->columns(2), // Organiza los campos en 2 columnas para mejor visualización
    ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable()
                    ->label(label:'Nombre y Apellido'),
                //Tables\Columns\TextColumn::make('email')->sortable()->searchable()
                //   ->label(label:'Correo'),
                  // Tables\Columns\TextColumn::make('roles.name') // Accede al nombre del rol desde la relación
                  Tables\Columns\TextColumn::make('roles')
                   ->formatStateUsing(fn ($record) => $record->roles->pluck('name')->join(', '))
                   ->sortable()
                   ->badge()
                   ->label('Rol')
                   ->colors([
                       'Administrador' => 'success',
                       'Usuario' => 'danger',
                   ]),
                 //  Tables\Columns\ImageColumn::make('profile_photo_path')
                   //   ->circular()
                    //   ->label('Foto'),
   

               
                Tables\Columns\TextColumn::make('created_at')
                ->label(label:'Fecha de Creacion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'Administrador' => 'Administrador',
                        'Usuario' => 'Usuario',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                 
                Tables\Actions\RestoreAction::make(),
            ]);
          
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
    
        // Aplicar el filtro de roles si el usuario no es un administrador
        $query = auth()->user()->hasRole('Administrador')
            ? $query
            : $query->whereHas(
                'roles',
                fn (Builder $query) => $query->where('name', 'Administrador')
            );
    
        // Eliminar los scopes globales
        return $query->withoutGlobalScopes([SoftDeletingScope::class]);
    }
    
/*  public static function getNavigationBadge(): ?string
  {
      return static::getModel()::count();
  }
  */

  
}
