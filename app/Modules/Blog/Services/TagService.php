<?php

namespace App\Modules\Blog\Services;

use App\Modules\Blog\Models\Tag;
use App\Modules\Blog\Repositories\TagRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * ModuleName: Blog
 * Purpose: Business logic for blog tag management
 * 
 * @category Blog
 * @package  App\Modules\Blog\Services
 * @author   AI Assistant
 * @created  2025-11-07
 */
class TagService
{
    protected TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getAllTags(): Collection
    {
        return $this->tagRepository->all();
    }

    public function getPopularTags(int $limit = 10): Collection
    {
        return $this->tagRepository->getPopular($limit);
    }

    public function getTag(int $id): ?Tag
    {
        return $this->tagRepository->find($id);
    }

    public function getTagBySlug(string $slug): ?Tag
    {
        return $this->tagRepository->findBySlug($slug);
    }

    public function createTag(array $data): Tag
    {
        DB::beginTransaction();
        try {
            $tag = $this->tagRepository->create($data);

            // Log activity (TODO: Install spatie/laravel-activitylog package)
            // activity()
            //     ->performedOn($tag)
            //     ->causedBy(auth()->user())
            //     ->log('Created blog tag');

            DB::commit();
            return $tag;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateTag(int $id, array $data): Tag
    {
        DB::beginTransaction();
        try {
            $tag = $this->tagRepository->update($id, $data);

            // Log activity (TODO: Install spatie/laravel-activitylog package)
            // activity()
            //     ->performedOn($tag)
            //     ->causedBy(auth()->user())
            //     ->log('Updated blog tag');

            DB::commit();
            return $tag;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteTag(int $id): bool
    {
        $tag = $this->getTag($id);

        // Log activity (TODO: Install spatie/laravel-activitylog package)
        // activity()
        //     ->performedOn($tag)
        //     ->causedBy(auth()->user())
        //     ->log('Deleted blog tag');

        return $this->tagRepository->delete($id);
    }

    public function searchTags(string $query): Collection
    {
        return $this->tagRepository->search($query);
    }
}
