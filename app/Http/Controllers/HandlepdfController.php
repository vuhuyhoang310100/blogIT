<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\HandlepdfService;

class HandlepdfController extends Controller
{
    protected $handlepdfService;
    public function __construct(HandlepdfService $handlepdfService)
    {
        $this->handlepdfService = $handlepdfService;
    }
    public function index()
    {
        $handlepdfs = $this->handlepdfService->getAll();
        return Inertia::render('handlepdfs/index')->with([
            'handlepdfs' => $handlepdfs
        ]);
    }
}
