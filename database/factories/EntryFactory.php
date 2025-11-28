<?php

namespace Database\Factories;

use App\Models\Entry;
use App\Models\Title;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Entry>
 */
class EntryFactory extends Factory
{
    protected $model = Entry::class;

    public function definition(): array
    {
        return [
            'title_id' => Title::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->paragraphs(random_int(1, 3), true),
            'is_locked' => false,
            'is_pinned' => false,
            'is_deleted' => false,
        ];
    }
}
