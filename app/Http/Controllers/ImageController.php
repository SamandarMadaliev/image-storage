<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        return response()->json();
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json();
    }

    public function show(Request $request, string $image): JsonResponse
    {
        return response()->json();
    }

    public function destroy(Request $request, string $image): JsonResponse
    {
        return response()->json();
    }
}
