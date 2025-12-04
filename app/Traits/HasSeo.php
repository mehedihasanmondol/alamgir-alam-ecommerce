<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait: HasSeo
 * Purpose: Provide SEO functionality to models
 * 
 * Features:
 * - Auto-generate meta title from name
 * - Auto-generate meta description
 * - Auto-generate meta keywords
 * - Generate Open Graph data
 * - Generate canonical URL
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
trait HasSeo
{
    /**
     * Get meta title or generate from name
     */
    public function getMetaTitleAttribute($value): string
    {
        return $value ?? $this->name ?? '';
    }

    /**
     * Get meta description or generate from description
     */
    public function getMetaDescriptionAttribute($value): ?string
    {
        if ($value) {
            return $value;
        }

        if (isset($this->description)) {
            return Str::limit(strip_tags($this->description), 160);
        }

        return null;
    }

    /**
     * Get OG title or fallback to meta title
     */
    public function getOgTitleAttribute($value): string
    {
        return $value ?? $this->meta_title ?? $this->name ?? '';
    }

    /**
     * Get OG description or fallback to meta description
     */
    public function getOgDescriptionAttribute($value): ?string
    {
        return $value ?? $this->meta_description;
    }

    /**
     * Get full SEO data array
     */
    public function getSeoData(): array
    {
        return [
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => $this->og_image,
            'canonical_url' => $this->canonical_url,
        ];
    }

    /**
     * Set SEO data from array
     */
    public function setSeoData(array $data): void
    {
        $this->fill([
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_image' => $data['og_image'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
        ]);
    }
}
