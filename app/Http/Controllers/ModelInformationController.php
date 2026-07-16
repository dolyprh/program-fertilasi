<?php

namespace App\Http\Controllers;

use App\Services\DecisionTreeService;
use Inertia\Inertia;
use Inertia\Response;

class ModelInformationController extends Controller
{
    public function __invoke(DecisionTreeService $decisionTreeService): Response
    {
        return Inertia::render('model/Index', [
            'model' => config('fertility.model'),
            'rules' => $decisionTreeService->rules(),
        ]);
    }
}
