<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Follow>
 */
class FollowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // public function definition(): array
    // {
    //     $follower = \App\Models\User::get()->random();
    //     $followed = \App\Models\User::where('id', '!=', $follower->id)->inRandomOrder()->first();

    //     // Vérifier si l'utilisateur $follower suit déjà $followed
    //     $alreadyFollowing = $follower->followed()->where('otherUser_id', $followed->id)->exists();

    //     if ($alreadyFollowing) {
    //         // Si l'utilisateur $follower suit déjà $followed, générez des données de test différentes
    //         return $this->definition();
    //     }

    //     return [
    //         'user_id' => $follower->id,
    //         'otherUser_id' => $followed->id,
    //         'created_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
    //     ];
    // }

    public function definition(): array
    {
        $follower = \App\Models\User::get()->random();

        // Obtenez une liste de tous les utilisateurs qui ne sont pas encore suivis par $follower
        $notFollowed = \App\Models\User::where('id', '!=', $follower->id)
            ->whereNotIn('id', $follower->followed()->pluck('otherUser_id'))
            ->get();

        // Si tous les utilisateurs sont déjà suivis par $follower, vous devrez gérer ce cas
        if ($notFollowed->isEmpty()) {
            // Gérer ce cas comme vous le souhaitez, par exemple en renvoyant un tableau vide
            return [];
        }

        $followed = $notFollowed->random();

        return [
            'user_id' => $follower->id,
            'otherUser_id' => $followed->id,
            'created_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
