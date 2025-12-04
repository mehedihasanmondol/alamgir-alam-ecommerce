<?php

namespace App\Modules\Blog\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Services\TagService;
use App\Modules\Blog\Requests\StoreTagRequest;
use App\Modules\Blog\Requests\UpdateTagRequest;

/**
 * ModuleName: Blog
 * Purpose: Admin controller for blog tag management
 * 
 * @category Blog
 * @package  App\Modules\Blog\Controllers\Admin
 * @author   AI Assistant
 * @created  2025-11-07
 */
class TagController extends Controller
{
    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        return view('admin.blog.tags.index-livewire');
    }

    public function create()
    {
        return view('admin.blog.tags.create');
    }

    public function store(StoreTagRequest $request)
    {
        $tag = $this->tagService->createTag($request->validated());

        // Return JSON response for AJAX requests (modal)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'tag' => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'description' => $tag->description,
                ],
                'message' => 'Tag created successfully',
            ]);
        }

        // Return redirect for normal form submissions
        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'ট্যাগ সফলভাবে তৈরি হয়েছে');
    }

    public function edit($id)
    {
        $tag = $this->tagService->getTag($id);
        return view('admin.blog.tags.edit', compact('tag'));
    }

    public function update(UpdateTagRequest $request, $id)
    {
        $this->tagService->updateTag($id, $request->validated());

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'ট্যাগ সফলভাবে আপডেট হয়েছে');
    }

    public function destroy($id)
    {
        $this->tagService->deleteTag($id);

        return response()->json([
            'success' => true,
            'message' => 'ট্যাগ সফলভাবে মুছে ফেলা হয়েছে',
        ]);
    }
}
