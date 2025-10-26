<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsApiService
{
    private $apiKey;
    private $baseUrl = 'https://newsapi.org/v2';

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
    }

    /**
     * Get badminton news headlines
     */
    public function getBadmintonNews($limit = 5)
    {
        $cacheKey = 'badminton_news_' . $limit;
        
        return Cache::remember($cacheKey, 300, function () use ($limit) { // 5 minutes cache
            try {
                $response = Http::timeout(10)->get($this->baseUrl . '/everything', [
                    'q' => 'badminton AND (tournament OR championship OR "world championship" OR "olympic" OR "BWF" OR "All England" OR "Malaysia Open" OR "Indonesia Open" OR "China Open" OR "Japan Open" OR "Denmark Open" OR "French Open" OR "German Open" OR "Swiss Open" OR "India Open" OR "Singapore Open" OR "Thailand Open" OR "Hong Kong Open" OR "Korea Open" OR "Taiwan Open" OR "Macau Open" OR "Vietnam Open" OR "Philippines Open" OR "Australian Open" OR "New Zealand Open" OR "Canada Open" OR "US Open" OR "Brazil Open" OR "Peru Open" OR "Mexico Open" OR "Guatemala Open" OR "Cuba Open" OR "Jamaica Open" OR "Trinidad Open" OR "Barbados Open" OR "Suriname Open" OR "Guyana Open" OR "Venezuela Open" OR "Colombia Open" OR "Ecuador Open" OR "Chile Open" OR "Argentina Open" OR "Uruguay Open" OR "Paraguay Open" OR "Bolivia Open" OR "Peru Open" OR "Ecuador Open" OR "Colombia Open" OR "Venezuela Open" OR "Guyana Open" OR "Suriname Open" OR "Barbados Open" OR "Jamaica Open" OR "Cuba Open" OR "Guatemala Open" OR "Mexico Open" OR "Peru Open" OR "Brazil Open" OR "US Open" OR "Canada Open" OR "New Zealand Open" OR "Australian Open" OR "Philippines Open" OR "Vietnam Open" OR "Macau Open" OR "Taiwan Open" OR "Korea Open" OR "Hong Kong Open" OR "Thailand Open" OR "Singapore Open" OR "India Open" OR "Swiss Open" OR "German Open" OR "French Open" OR "Denmark Open" OR "Japan Open" OR "China Open" OR "Indonesia Open" OR "Malaysia Open" OR "All England" OR "BWF" OR "olympic" OR "world championship" OR "championship" OR "tournament")',
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => $limit,
                    'apiKey' => $this->apiKey,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $articles = $data['articles'] ?? [];
                    
                    // Filter out non-badminton articles
                    $filteredArticles = array_filter($articles, function($article) {
                        $title = strtolower($article['title'] ?? '');
                        $description = strtolower($article['description'] ?? '');
                        $content = $title . ' ' . $description;
                        
                        // Must contain badminton-related keywords
                        $badmintonKeywords = [
                            'badminton', 'shuttlecock', 'racket', 'bwf', 'all england',
                            'malaysia open', 'indonesia open', 'china open', 'japan open',
                            'denmark open', 'french open', 'german open', 'swiss open',
                            'india open', 'singapore open', 'thailand open', 'hong kong open',
                            'korea open', 'taiwan open', 'macau open', 'vietnam open',
                            'philippines open', 'australian open', 'new zealand open',
                            'canada open', 'us open', 'brazil open', 'peru open',
                            'mexico open', 'guatemala open', 'cuba open', 'jamaica open',
                            'trinidad open', 'barbados open', 'suriname open', 'guyana open',
                            'venezuela open', 'colombia open', 'ecuador open', 'chile open',
                            'argentina open', 'uruguay open', 'paraguay open', 'bolivia open'
                        ];
                        
                        foreach ($badmintonKeywords as $keyword) {
                            if (strpos($content, $keyword) !== false) {
                                return true;
                            }
                        }
                        
                        return false;
                    });
                    
                    return $this->formatNewsData(array_values($filteredArticles));
                }

                return $this->getFallbackNews();
            } catch (\Exception $e) {
                \Log::error('NewsAPI Error: ' . $e->getMessage());
                return $this->getFallbackNews();
            }
        });
    }

    /**
     * Get sports news (broader category)
     */
    public function getSportsNews($limit = 5)
    {
        $cacheKey = 'sports_news_' . $limit;
        
        return Cache::remember($cacheKey, 300, function () use ($limit) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . '/top-headlines', [
                    'category' => 'sports',
                    'language' => 'en',
                    'pageSize' => $limit,
                    'apiKey' => $this->apiKey,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatNewsData($data['articles'] ?? []);
                }

                return $this->getFallbackNews();
            } catch (\Exception $e) {
                \Log::error('NewsAPI Sports Error: ' . $e->getMessage());
                return $this->getFallbackNews();
            }
        });
    }

    /**
     * Format news data for display
     */
    private function formatNewsData($articles)
    {
        return collect($articles)->map(function ($article) {
            return [
                'title' => $article['title'] ?? 'No title',
                'description' => $article['description'] ?? 'No description available',
                'url' => $article['url'] ?? '#',
                'image' => $article['urlToImage'] ?? $this->getDefaultImage(),
                'source' => $article['source']['name'] ?? 'Unknown Source',
                'published_at' => $article['publishedAt'] ? 
                    \Carbon\Carbon::parse($article['publishedAt'])->diffForHumans() : 
                    'Recently',
                'published_date' => $article['publishedAt'] ? 
                    \Carbon\Carbon::parse($article['publishedAt'])->format('M d, Y') : 
                    'Unknown',
            ];
        })->toArray();
    }

    /**
     * Get fallback news when API fails
     */
    private function getFallbackNews()
    {
        return [
            [
                'title' => 'BWF World Championships 2025 Updates',
                'description' => 'The Badminton World Federation has announced the latest updates for the upcoming World Championships, including venue details and qualification criteria.',
                'url' => 'https://bwfbadminton.com',
                'image' => $this->getDefaultImage(),
                'source' => 'BWF Official',
                'published_at' => '2 hours ago',
                'published_date' => 'Oct 26, 2025',
            ],
            [
                'title' => 'All England Open 2025 Draw Released',
                'description' => 'The draw for the prestigious All England Open Badminton Championships has been announced, featuring top players from around the world.',
                'url' => 'https://bwfbadminton.com',
                'image' => $this->getDefaultImage(),
                'source' => 'All England Open',
                'published_at' => '1 day ago',
                'published_date' => 'Oct 25, 2025',
            ],
            [
                'title' => 'Malaysia Open 2025 Tournament Preview',
                'description' => 'A comprehensive preview of the upcoming Malaysia Open Super 1000 tournament, including player profiles and match predictions.',
                'url' => 'https://bwfbadminton.com',
                'image' => $this->getDefaultImage(),
                'source' => 'Badminton Malaysia',
                'published_at' => '3 days ago',
                'published_date' => 'Oct 23, 2025',
            ],
            [
                'title' => 'Olympic Badminton Qualification Updates',
                'description' => 'Latest updates on badminton qualification for the 2025 Olympics, including current rankings and upcoming qualifying tournaments.',
                'url' => 'https://bwfbadminton.com',
                'image' => $this->getDefaultImage(),
                'source' => 'Olympic Committee',
                'published_at' => '4 days ago',
                'published_date' => 'Oct 22, 2025',
            ],
            [
                'title' => 'Badminton Racket Technology Advances',
                'description' => 'New innovations in badminton racket technology are changing the game, with lighter materials and improved string technology.',
                'url' => 'https://bwfbadminton.com',
                'image' => $this->getDefaultImage(),
                'source' => 'Badminton Gear Pro',
                'published_at' => '5 days ago',
                'published_date' => 'Oct 21, 2025',
            ],
            [
                'title' => 'Youth Badminton Development Programs',
                'description' => 'International badminton federations are launching new programs to develop young talent and grow the sport globally.',
                'url' => 'https://bwfbadminton.com',
                'image' => $this->getDefaultImage(),
                'source' => 'BWF Development',
                'published_at' => '6 days ago',
                'published_date' => 'Oct 20, 2025',
            ],
        ];
    }

    /**
     * Get default image for news articles
     */
    private function getDefaultImage()
    {
        return asset('images/badminton-news-default.jpg');
    }

    /**
     * Check if API key is configured
     */
    public function isConfigured()
    {
        return !empty($this->apiKey);
    }

    /**
     * Get API status
     */
    public function getStatus()
    {
        if (!$this->isConfigured()) {
            return [
                'status' => 'not_configured',
                'message' => 'NewsAPI key not configured',
            ];
        }

        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/sources', [
                'apiKey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return [
                    'status' => 'active',
                    'message' => 'NewsAPI is working correctly',
                ];
            }

            return [
                'status' => 'error',
                'message' => 'NewsAPI returned an error',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Unable to connect to NewsAPI: ' . $e->getMessage(),
            ];
        }
    }
}
