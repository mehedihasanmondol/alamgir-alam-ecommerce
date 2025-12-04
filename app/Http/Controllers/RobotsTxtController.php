<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Response;

/**
 * Robots.txt Controller
 * Purpose: Generate dynamic robots.txt file
 * 
 * @author Windsurf AI
 * @date 2025-11-20
 */
class RobotsTxtController extends Controller
{
    /**
     * Generate and return robots.txt content
     */
    public function index(): Response
    {
        $content = $this->generateRobotsTxt();
        
        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Generate robots.txt content
     */
    private function generateRobotsTxt(): string
    {
        $lines = [];
        
        // User-agent
        $lines[] = 'User-agent: *';
        $lines[] = '';
        
        // Disallow admin areas
        $lines[] = '# Admin Panel - No Indexing';
        $lines[] = 'Disallow: /admin/';
        $lines[] = 'Disallow: /admin';
        $lines[] = '';
        
        // Disallow authentication pages
        $lines[] = '# Authentication Pages';
        $lines[] = 'Disallow: /login';
        $lines[] = 'Disallow: /register';
        $lines[] = 'Disallow: /password/';
        $lines[] = 'Disallow: /logout';
        $lines[] = '';
        
        // Disallow cart and checkout
        $lines[] = '# User-Specific Pages';
        $lines[] = 'Disallow: /cart';
        $lines[] = 'Disallow: /checkout';
        $lines[] = 'Disallow: /wishlist';
        $lines[] = 'Disallow: /account/';
        $lines[] = '';
        
        // Disallow API endpoints
        $lines[] = '# API Endpoints';
        $lines[] = 'Disallow: /api/';
        $lines[] = 'Disallow: /livewire/';
        $lines[] = '';
        
        // Disallow search with parameters
        $lines[] = '# Search Pages with Parameters';
        $lines[] = 'Disallow: /*?*search=';
        $lines[] = 'Disallow: /*?*q=';
        $lines[] = '';
        
        // Allow public pages
        $lines[] = '# Allow Public Pages';
        $lines[] = 'Allow: /';
        $lines[] = 'Allow: /shop';
        $lines[] = 'Allow: /categories';
        $lines[] = 'Allow: /brands';
        $lines[] = 'Allow: /blog';
        $lines[] = 'Allow: /coupons';
        $lines[] = '';
        
        // Add custom rules from settings
        $customRules = SiteSetting::get('robots_txt_custom');
        if (!empty($customRules)) {
            $lines[] = '# Custom Rules';
            $lines[] = trim($customRules);
            $lines[] = '';
        }
        
        // Sitemap location
        if (SiteSetting::get('sitemap_enabled', '1') === '1') {
            $lines[] = '# Sitemap';
            $lines[] = 'Sitemap: ' . url('/sitemap.xml');
        }
        
        return implode("\n", $lines);
    }
}
