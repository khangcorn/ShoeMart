<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $primaryKey = 'slider_id';

    protected $fillable = ['image_url', 'caption', 'link', 'position'];
}
