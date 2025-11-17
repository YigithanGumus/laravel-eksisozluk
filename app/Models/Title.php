<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Title extends Model
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = ['title','slug','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function entry(Request $request)
    {
        return Entry::create($request->all());
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });

        static::creating(function ($model) {
            $model->slug = Str::slug($model->title).rand(999999,999999999);
        });
    }
}
