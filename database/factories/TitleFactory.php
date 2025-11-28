<?php

namespace Database\Factories;

use App\Models\Title;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Title>
 */
class TitleFactory extends Factory
{
    protected $model = Title::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(random_int(3, 6));

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(10000, 999999),
            'user_id' => User::factory(),
            'entry_count' => 0,
            'last_entry_id' => null,
            'last_activity_at' => now(),
            'is_locked' => false,
            'is_pinned' => false,
        ];
    }
}
