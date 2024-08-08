<?php

namespace Database\Seeders;

use App\Models\ProfanityCategory;
use App\Models\ProfanityWord;
use Database\Seeders\Datasets\ProfanityDataset;
use Illuminate\Database\Seeder;

class ProfanityDatasetSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataset = new ProfanityDataset();
        $words = $dataset->words;
        foreach ($words as $word_type => $word_array) {
            $profanity_category_id = ProfanityCategory::query()->where('profanity_category_code', $word_type)->first()->id;

            foreach ($word_array as $phrase) {

                //Separating phrase into words
                $words_in_phrase = explode(' ', $phrase);
                $no_of_words_in_phrase = sizeof($words_in_phrase);

                if ($no_of_words_in_phrase == 1) {
                    //If phrase has dashes, we separate the phrase by dashes
                    $words_in_phrase = explode('-', $phrase, 3);
                    $no_of_words_in_phrase = sizeof($words_in_phrase);
                }

                if ($no_of_words_in_phrase == 1) {
                    ProfanityWord::query()->create([
                        'word_1' => strtolower($phrase),
                        'profanity_category_id' => $profanity_category_id
                    ]);

                } else if ($no_of_words_in_phrase == 2) {
                    ProfanityWord::query()->create([
                        'word_1' => strtolower($words_in_phrase[0]),
                        'word_2' => strtolower($words_in_phrase[1]),
                        'profanity_category_id' => $profanity_category_id
                    ]);
                } else if ($no_of_words_in_phrase == 3) {
                    ProfanityWord::query()->create([
                        'word_1' => strtolower($words_in_phrase[0]),
                        'word_2' => strtolower($words_in_phrase[1]),
                        'word_3' => strtolower($words_in_phrase[2]),
                        'profanity_category_id' => $profanity_category_id
                    ]);
                }
            }
        }

    }

}
