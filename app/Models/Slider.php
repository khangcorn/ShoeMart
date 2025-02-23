<?php


// app/Models/Slider.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $table = 'sliders';
    protected $primaryKey = 'slider_id';
    public $timestamps = false;

    protected $fillable = ['image_url', 'caption', 'link', 'position'];
}