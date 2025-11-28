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

    protected $fillable = ['title','slug','user_id','is_locked','is_pinned','lock_reason','pin_reason'];

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

    public function scopeWithMeta($query)
    {
        return $query->withCount('entries')
            ->with('user:id,name,username');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });

        static::creating(function ($model) {
            $slugBase = Str::slug($model->title);
            $model->slug = $model->slug ?: $slugBase . '-' . Str::random(6);
            $model->last_activity_at = now();
        });
    }
}
