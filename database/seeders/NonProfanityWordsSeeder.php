<?php

namespace Database\Seeders;

use App\Models\ProfanityWord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use function Laravel\Prompts\progress;

class NonProfanityWordsSeeder extends Seeder
{
    private array $symbols = array("!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "_", "+", "=", "{", "}", "[", "]", ":", ";", ",", ".", "<", ">", "/", "?", "|", "'");

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Clearing words in redis
        Redis::del('words');

        //Seeding list of countries and cities
        $path = 'database/seeders/Datasets/countries.json';
        $jsonData = file_get_contents($path);
        $countries = json_decode($jsonData, true);
        $progress = progress(label: 'Seeding List of Countries and cities (In Redis) ', steps: sizeof($countries));
        foreach ($countries as $country => $cities) {
            Redis::sadd('words', strtolower($country));
            foreach ($cities as $city) {
                $city = implode('', explode(' ', $city));
                $city = str_replace($this->symbols, '', $city);
                Redis::sadd('words', strtolower($city));
            }
            $progress->advance();
        }
        $progress->finish();

        //Seeding list of musical instruments
        $path = 'database/seeders/Datasets/musical_instruments.json';
        $jsonData = file_get_contents($path);
        $data = json_decode($jsonData, true)['instruments'];
        $progress = progress(label: 'Seeding List of Musical Instruments (In Redis) ', steps: sizeof($data));
        $progress->start();
        foreach ($data as $datum) {
            $datum = strtolower($datum);
            $combined_word = implode('', explode(' ', $datum));
            Redis::sadd('words', $datum);
            Redis::sadd('words', $combined_word);
            $progress->advance();
        }
        $progress->finish();


        //Seeding dictionary
        for ($i = 1; $i < 4; $i++) {
            $path = 'database/seeders/Datasets/words_dictionary_' . $i . '.json';
            $handle = fopen($path, 'r');

            $profanity_words = ProfanityWord::query()->select('word_1')
                ->whereNull('word_2')
                ->whereNull('word_3')
                ->get()->pluck('word_1')
                ->toArray();

            foreach ($profanity_words as $profanity_word) {
                $profanity_words[] = $profanity_word . 'ed';
                $profanity_words[] = $profanity_word . 'ing';
            }


            $lineCount = count(file($path));
            $progress = progress(label: 'Seeding Dictionary (In Redis) ' . $i, steps: $lineCount);
            $progress->start();

            while (($line = fgets($handle)) !== false) {
                if (!($line == "{\n" || $line == "}")) {
                    $item = explode(':', $line);
                    $word = str_replace('"', '', trim($item[0]));

                    if (strlen($word) > 1) {
                        $initial_word = $word;
                        if (in_array($word, $profanity_words)) {
                            //If the word is  in the profanity list, then we skip
                            continue;
                        } else if (Str::endsWith($word, 'ing') && in_array(Str::replaceEnd('ing', '', $word), $profanity_words)) {
                            //if word ends with 'ing' and if the word exists in profanity list after removing 'ing', we skip
                            continue;
                        } else if (Str::endsWith($word, 'ed') && in_array(Str::replaceEnd('ed', '', $word), $profanity_words)) {
                            //if word ends with 'ed' and if the word exists in profanity list after removing 'ed', we skip
                            continue;
                        } else if (in_array(Str::singular($word), $profanity_words)) {
                            // If singular form of the word is  in profanity list, then we skip
                            continue;
                        } else if (in_array(Str::plural($word), $profanity_words)) {
                            //If plural form of word is in the profanity list, we skip
                            continue;
                        } else {
                            Redis::sadd('words', $initial_word);

                            if (Str::singular($initial_word) != $initial_word) {
                                Redis::sadd('words', Str::singular($initial_word));
                            }

                            if (Str::plural($initial_word) != $initial_word) {
                                Redis::sadd('words', Str::plural($initial_word));
                            }
                        }
                    }
                }
                $progress->advance();
            }
            $progress->finish();
        }
    }
}
