<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Пользователь авто
 *
 * @property int id идентификатор
 * @property string name имя
 * @property string phone телефон
 * @property Car car привязанное авто
 */
class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone'
    ];

    /**
     * Авто, привязанное к клиенту
     */
    public function car()
    {
        return $this->hasOne(Car::class);
    }
}
