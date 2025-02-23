<?php

// app/Models/ParentCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentCategory extends Model
{
    use HasFactory;

    protected $table = 'parent_categories';
    protected $primaryKey = 'parent_id';
    public $timestamps = true;

    protected $fillable = ['name'];

    public function childCategories()
    {
        return $this->hasMany(ChildCategory::class, 'parent_id');
    }
}