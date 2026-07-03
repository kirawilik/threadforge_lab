<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blueprint;
use App\Http\Requests\UpdateBlueprintRequest;
use App\Http\Requests\StoreBlueprintRequest;
use App\Http\Resources\BlueprintResource;

class BlueprintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
return BlueprintResource::collection(auth()->user()->blueprints()->latest()->get());
        return response()->json($blueprints, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlueprintRequest $request)
    {
       $blueprint = auth()->user()->blueprints()->create(
        $request->validated()
    );


        return (new BlueprintResource($blueprint))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $blueprint = Blueprint::findOrFail($id);

    return response()->json($blueprint, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlueprintRequest $request, Blueprint $blueprint)
    {
       $blueprint->update($request->validated());

        return new BlueprintResource($blueprint);
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Blueprint $blueprint)
    {
        $blueprint->delete();

        return response()->json([
            'message' => 'blueprint deleted successfully'
        ], 200);
    }
}
