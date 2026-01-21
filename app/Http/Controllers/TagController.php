<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index(Request $request)
    {
        $tags = $this->tagService->getAll(
            filters: $request->all(),
            searchQuery: $request->query('q')
        );

        return inertia('Tags/Index', [
            'tags' => $tags,
        ]);
    }
}
