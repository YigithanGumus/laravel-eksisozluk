<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Favorite;
use App\Models\Vote;
use App\Models\EntryEdit;

class Entry extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = ['title_id', 'user_id', 'content', 'is_locked', 'is_pinned', 'lock_reason', 'pin_reason', 'is_deleted', 'deleted_reason'];

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
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function edits()
    {
        return $this->hasMany(EntryEdit::class);
    }

    public function scopeWithMeta($query)
    {
        return $query->with(['user:id,name,username', 'title:id,title,slug'])
            ->withCount([
                'favorites as favorites_count',
                'votes as up_votes_count' => function ($q) {
                    $q->where('value', 'up');
                },
                'votes as down_votes_count' => function ($q) {
                    $q->where('value', 'down');
                },
            ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });

        static::created(function ($entry) {
            $entry->title()->update([
                'entry_count' => $entry->title->entries()->count(),
                'last_entry_id' => $entry->id,
                'last_activity_at' => now(),
            ]);
        });

        static::deleted(function ($entry) {
            if ($entry->title) {
                $entry->title()->update([
                    'entry_count' => $entry->title->entries()->count(),
                    'last_entry_id' => optional($entry->title->entries()->latest()->first())->id,
                    'last_activity_at' => now(),
                ]);
            }
        });
    }
}
