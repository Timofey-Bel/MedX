<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель организации (юридического лица)
 * 
 * @property int $id
 * @property string $inn
 * @property string|null $kpp
 * @property string|null $ogrn
 * @property string $name_full
 * @property string|null $name_short
 * @property string|null $legal_address
 * @property string|null $postal_address
 * @property string|null $director_name
 * @property string|null $director_position
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $bank_name
 * @property string|null $bank_bik
 * @property string|null $bank_account
 * @property string|null $bank_corr_account
 * @property string|null $opf
 * @property string $status
 * @property string|null $dadata_json
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Organization extends Model
{
    /**
     * Название таблицы
     */
    protected $table = 'orgs';

    /**
     * Поля, доступные для массового заполнения
     */
    protected $fillable = [
        'inn',
        'kpp',
        'ogrn',
        'name_full',
        'name_short',
        'legal_address',
        'postal_address',
        'director_name',
        'director_position',
        'phone',
        'email',
        'bank_name',
        'bank_bik',
        'bank_account',
        'bank_corr_account',
        'opf',
        'status',
        'dadata_json',
    ];

    /**
     * Поля, которые должны быть приведены к типам
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Статусы организации
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_LIQUIDATED = 'liquidated';

    /**
     * Получить пользователей, связанных с этой организацией
     */
    public function users()
    {
        return $this->hasMany(User::class, 'org_id');
    }

    /**
     * Проверить, активна ли организация
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Получить данные DaData в виде массива
     */
    public function getDaDataAttribute(): ?array
    {
        return $this->dadata_json ? json_decode($this->dadata_json, true) : null;
    }

    /**
     * Получить краткое название или полное, если краткого нет
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name_short ?: $this->name_full;
    }

    /**
     * Создать организацию из данных DaData
     */
    public static function createFromDaData(array $dadataResponse): self
    {
        $data = $dadataResponse['data'] ?? [];
        
        return self::create([
            'inn' => $data['inn'] ?? null,
            'kpp' => $data['kpp'] ?? null,
            'ogrn' => $data['ogrn'] ?? null,
            'name_full' => $data['name']['full_with_opf'] ?? $dadataResponse['value'] ?? '',
            'name_short' => $data['name']['short_with_opf'] ?? null,
            'legal_address' => $data['address']['value'] ?? null,
            'postal_address' => $data['address']['value'] ?? null,
            'director_name' => $data['management']['name'] ?? null,
            'director_position' => $data['management']['post'] ?? null,
            'opf' => $data['opf']['full'] ?? null,
            'status' => self::mapDaDataStatus($data['state']['status'] ?? 'ACTIVE'),
            'dadata_json' => json_encode($dadataResponse),
        ]);
    }

    /**
     * Обновить организацию из данных DaData
     */
    public function updateFromDaData(array $dadataResponse): bool
    {
        $data = $dadataResponse['data'] ?? [];
        
        return $this->update([
            'kpp' => $data['kpp'] ?? $this->kpp,
            'ogrn' => $data['ogrn'] ?? $this->ogrn,
            'name_full' => $data['name']['full_with_opf'] ?? $dadataResponse['value'] ?? $this->name_full,
            'name_short' => $data['name']['short_with_opf'] ?? $this->name_short,
            'legal_address' => $data['address']['value'] ?? $this->legal_address,
            'postal_address' => $data['address']['value'] ?? $this->postal_address,
            'director_name' => $data['management']['name'] ?? $this->director_name,
            'director_position' => $data['management']['post'] ?? $this->director_position,
            'opf' => $data['opf']['full'] ?? $this->opf,
            'status' => self::mapDaDataStatus($data['state']['status'] ?? 'ACTIVE'),
            'dadata_json' => json_encode($dadataResponse),
        ]);
    }

    /**
     * Преобразовать статус DaData в статус системы
     */
    protected static function mapDaDataStatus(string $dadataStatus): string
    {
        return match ($dadataStatus) {
            'ACTIVE' => self::STATUS_ACTIVE,
            'LIQUIDATING', 'LIQUIDATED' => self::STATUS_LIQUIDATED,
            default => self::STATUS_INACTIVE,
        };
    }
}
