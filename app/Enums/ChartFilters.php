<?php

namespace App\Enums;

use App\Models\TextFilterAudit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;

enum ChartFilters: string
{
    case TODAY = 'Today';
    case LAST_7_DAYS = 'Last 7 days';
    case LAST_30_DAYS = 'Last 30 days';
    case THIS_WEEK = 'This week';
    case THIS_MONTH = 'This month';

    public function profanityFrequency(User $user): array
    {
        return match ($this) {
            self::TODAY => $this->calculateProfanityFrequencyForGivenInterval(Carbon::now()->startOfDay(), Carbon::now()->endOfDay()),
            self::LAST_7_DAYS => $this->calculateProfanityFrequencyForGivenInterval(Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()),
            self::LAST_30_DAYS => $this->calculateProfanityFrequencyForGivenInterval(Carbon::now()->subDays(30)->startOfDay(), Carbon::now()->endOfDay()),
            self::THIS_WEEK => $this->calculateProfanityFrequencyForGivenInterval(Carbon::now()->startOfWeek()->startOfDay(), Carbon::now()->endOfDay()),
            self::THIS_MONTH => $this->calculateProfanityFrequencyForGivenInterval(Carbon::now()->startOfMonth()->startOfDay(), Carbon::now()->endOfDay()),
        };
    }

    public function calculateProfanityFrequencyForGivenInterval(Carbon $from_date, Carbon $to_date): array
    {
        $result = TextFilterAudit::query()
            ->where('is_successful', true)
            ->where('created_at', '>=', $from_date)
            ->where('created_at', '<', $to_date)
            ->whereNotNull('profanity_caught')
            ->whereNotNull('profanity_categories_caught')
            ->select('profanity_caught')
            ->get()->toArray();

        $words_array = explode(',',implode(',',Arr::pluck($result,'profanity_caught')));

        $wordCounts = array_count_values($words_array);
        arsort($wordCounts);
        return array_slice($wordCounts, 0, 10, true);
    }
}
