<?php

namespace Database\Factories;

use App\Http\Utilities\DetectMimeType;
use App\Models\ApacheAccessLog;
use App\Models\Application;
use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApacheAccessLog>
 */
class ApacheAccessLogFactory extends Factory
{
    protected $model = ApacheAccessLog::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serverId = Server::whereHas('applications')->where('web_server', 'apache2')->inRandomOrder()->pluck('id')->first();
        $applicationId = Application::where('server_id', $serverId)->inRandomOrder()->pluck('id')->first();
        
        $startTime = now()->subMonth()->startOfMonth();
        $mimeType = $this->faker->randomElement(['application/json','text/html','image/jpeg','audio/mp3','video/mp4','application/pdf','text/plain','application/xml','image/png','application/javascript']);
        $platforms = [
            'Windows' => ['10', '8.1', '7', 'XP'],
            'macOS' => ['Catalina', 'Mojave', 'High Sierra'],
            'Linux' => ['Ubuntu', 'Fedora', 'Debian'],
            'iOS' => ['14', '13', '12'],
            'Android' => ['10', '9', '8'],
        ];
        
        $selectedPlatform = $this->faker->randomElement(array_keys($platforms));
        $selectedVersion = $this->faker->randomElement($platforms[$selectedPlatform]);

        $botNames = [
            'ChatBotX',
            'ByteBuddy',
            'TechWhizBot',
            'CodeJester',
            'QuantumQuark',
            'CyberNaut',
            'LogicLynx',
            'SynthSage',
            'PixelPioneer',
            'DataDroid',
            'RoboWiz',
            'NanoBot',
            'CogitoBot',
            'MegaMindAI',
            'BotMaster',
            'NeuraNinja',
            'GoogleBot',
            'BingCrawler',
            'YandexBot',
        ];

        $paths = [
            '/home',
            '/products',
            '/blog',
            '/contact',
            '/about-us',
        ];
        
        $referrerDomains = [
            'example.com',
            'sample.org',
            'test.net',
            'demo.info',
        ];

        return [
            'server_id' => $serverId,
            'application_id' => $applicationId,
            'type' => $this->faker->randomElement(['access', 'access-ssl']),
            'ip' => $this->faker->ipv4,
            'time' => $startTime->addMinutes($this->faker->numberBetween(1, 60 * 24))->format('d/M/Y:H:i:s O'),
            'url' => $this->faker->randomElement($paths),
            'status' => $this->faker->randomElement([200, 201, 301, 302, 400, 401, 403, 404, 422, 500]),
            'bytes' => $this->faker->numberBetween(1000, 5000),
            'referrer_url' => $this->faker->url,
            'referrer_domain' => $this->faker->randomElement($referrerDomains),
            'is_bot_request' => $this->faker->boolean,
            'is_sitemap_url' => $this->faker->boolean,
            'is_robots_txt' => $this->faker->boolean,
            'is_xmlrpc_request' => $this->faker->boolean,
            'platform' => $selectedPlatform,
            'platform_version' => $selectedVersion,
            'device' => $this->faker->randomElement(['desktop', 'phone', 'tablet', 'robot']),
            'bot_name' => $this->faker->randomElement($botNames),
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'browser' => $this->faker->randomElement(['ChatBotX','ByteBuddy','TechWhizBot','CodeJester','QuantumQuark','CyberNaut','LogicLynx','SynthSage','PixelPioneer','DataDroid']),
            'mime_type' => $mimeType,
            'document_type' => DetectMimeType::getDocumentType($mimeType),
            'protocol' => $this->faker->randomElement(['HTTP', 'HTTPS']) . '/' . $this->faker->randomElement(['1.0', '1.1', '2.0']),
            'country' => $this->faker->country,
            'state' => $this->faker->state,
            'city' => $this->faker->city,
        ];
    }
}
