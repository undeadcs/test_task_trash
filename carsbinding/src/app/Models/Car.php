<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Автомобиль
 *
 * @property int id идентификатор
 * @property string model марка и модель
 * @property string number номер
 * @property Customer customer пользователь авто
 */
class Car extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'model',
        'number'
    ];

    /**
     * Пользователь авто
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
