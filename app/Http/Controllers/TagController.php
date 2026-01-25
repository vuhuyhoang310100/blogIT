<?php

namespace App\Http\Controllers;

use App\DTOs\Tag\TagFilterDTO;
use App\Services\TagService;
use Illuminate\Http\Request;
use Inertia\Inertia;


class TagController extends Controller
{
	protected $tagService;

	public function __construct(TagService $tagService)
	{
		$this->tagService = $tagService;
	}

	public function index(Request $request)
	{
		$filterDTO = TagFilterDTO::fromRequest($request->all());

		$tags = $this->tagService->getAll($filterDTO);

		return Inertia::render('tags/index', [
			'tags' => $tags,
		]);
	}
}