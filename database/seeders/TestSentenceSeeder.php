<?php

namespace Database\Seeders;

use App\Models\TestSentence;
use Illuminate\Database\Seeder;

class TestSentenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sentences = [
            "Our Text Moderation was built to be both fast and smart. It will catch phonetic variations such as 'phuk that' or 'big azz'",
            "Letter replacements: a$$, f*ck",
            "Relevant letter omissions: 'wanna have sx', 'you are a dck'",
            "Blanks and special characters: _a_sᅳ_s_ and B * O ** O  B S",
            "Grawlix: pile of $#!t",
            "Embedded words 12fuckblablah, youidiot, while smartly ignoring potential false positives such as phuket, bassguitar and amass",
            "Complex leet typing: coc|<s, F|_|CK",
            "S͛p͛e͛c͛i͛a͛l͛ cͨaͣrͬaͣcͨtͭeͤrͬs ⒜⒩⒟ c̴r̴a̴p̴p̴y̴ unicode: 𝓬𝓻𝓪𝓹 t̼i̼t̼s̼ p̷o̷r̷n̷ as 🅡🅔🅣🅐🅡🅓"
        ];

        foreach ($sentences as $sentence) {
            TestSentence::query()->create([
                'sentence' => $sentence
            ]);
        }
    }
}
