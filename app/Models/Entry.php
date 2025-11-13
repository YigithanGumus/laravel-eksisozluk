<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Entry extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = ['title_id', 'user_id', 'content'];

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
