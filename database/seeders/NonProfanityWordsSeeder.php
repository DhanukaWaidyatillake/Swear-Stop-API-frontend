<?php

namespace Database\Seeders;

use Database\Seeders\Datasets\ProfanityDataset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class NonProfanityWordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Redis::del('words');

        for ($i = 1; $i < 4; $i++) {
            $handle = fopen('database/seeders/Datasets/words_dictionary_' . $i . '.json', 'r');

            $profanity_dataset = new ProfanityDataset();

            $profanity_words = $profanity_dataset->words;
            $profanity_words = Arr::flatten($profanity_words);
            $profanity_words = array_map('strtolower', $profanity_words);

            while (($line = fgets($handle)) !== false) {
                if (!($line == "{\n" || $line == "}")) {
                    $item = explode(':', $line);
                    $word = str_replace('"', '', trim($item[0]));
                    if (strlen($word) > 2 && !in_array($word, $profanity_words)) {
                        Redis::sadd('words', $word);
                    }
                }
            }
        }
    }
}
