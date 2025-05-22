<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentVariant extends Model
{
    use HasFactory;

    protected $primaryKey = 'comment_variant_id';

    protected $fillable = ['comment_id', 'user_id', 'content'];
}
