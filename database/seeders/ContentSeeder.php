<?php

namespace Database\Seeders;

use App\Models\Entry;
use App\Models\Favorite;
use App\Models\Follow;
use App\Models\Title;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Seed Ekşi Sözlük benzeri içerik ve ilişkiler.
     */
    public function run(): void
    {
        // Fabrika ile toplu kullanıcı üret
        User::factory(25)->create();

        // Admin ve önceden eklenen kullanıcıları da havuza kat
        $users = User::all();

        // Rastgele takip ilişkileri
        $users->each(function (User $user) use ($users) {
            $size = min(8, max(1, $users->count() - 1));
            $candidates = $users->where('id', '!=', $user->id)->random(rand(1, $size));
            foreach ($candidates as $candidate) {
                Follow::firstOrCreate([
                    'follower_id' => $user->id,
                    'followed_id' => $candidate->id,
                ]);
            }
        });

        // Başlık ve entry üretimi
        $titles = collect();
        for ($i = 0; $i < 40; $i++) {
            $owner = $users->random();
            $title = Title::factory()
                ->for($owner)
                ->create([
                    'is_pinned' => rand(1, 10) === 1, // %10 pin
                    'is_locked' => false,
                ]);
            $titles->push($title);

            $entryAuthors = $users->random(rand(3, min(12, $users->count())));
            foreach ($entryAuthors as $author) {
                $entry = Entry::factory()
                    ->for($title)
                    ->for($author)
                    ->create([
                        'is_locked' => false,
                        'is_pinned' => false,
                    ]);

                    // Rastgele favori
                    $fanCount = rand(0, min(6, $users->count()));
                    if ($fanCount > 0) {
                        $fans = $users->random($fanCount);
                        foreach ($fans as $fan) {
                            Favorite::firstOrCreate([
                                'user_id' => $fan->id,
                                'entry_id' => $entry->id,
                            ]);
                        }
                    }

                    // Oylar
                    $voters = $users->random(rand(2, min(10, $users->count())));
                    foreach ($voters as $voter) {
                        Vote::updateOrCreate(
                            [
                                'user_id' => $voter->id,
                                'entry_id' => $entry->id,
                            ],
                            [
                                'value' => rand(0, 1) ? 'up' : 'down',
                            ]
                        );
                    }
            }
        }

        // Başlık istatistiklerini güncelle
        Title::with('entries')->get()->each(function (Title $title) {
            $lastEntry = $title->entries()->latest()->first();
            $title->update([
                'entry_count' => $title->entries()->count(),
                'last_entry_id' => optional($lastEntry)->id,
                'last_activity_at' => optional($lastEntry)->created_at ?: $title->created_at,
            ]);
        });
    }
}
