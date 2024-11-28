<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Товар
 *
 * @property int $id ID записи
 * @property string $created_at Дата и время создания записи
 * @property string $updated_at Дата и время обновления записи
 * @property string $title Наименование
 * @property float $price Цена
 */
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
}
