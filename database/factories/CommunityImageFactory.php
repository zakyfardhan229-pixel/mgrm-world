<?php

namespace Database\Factories;

use App\Models\CommunityImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CommunityImage>
 */
class CommunityImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => 'community/'.Str::random(20).'.jpg',
            'caption' => fake()->sentence(4),
        ];
    }
}
