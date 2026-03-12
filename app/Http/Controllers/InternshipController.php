<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //No status code control
        // return Internship::all();

        // Explict JSON with status code, and extra info in the response
        return response()->json([
            'message' => 'Succeeded',
            'data' => Internship::all()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // After sending a post request, you can see newly created internship with its id in the response
        $internship = Internship::create($request->all());
        return response()->json($internship, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Internship $internship)
    {
        // Laravel automatically finds the internship by id from the URL and returns it as JSON
        return response()->json($internship, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Internship $internship)
    {
        // Updading an existing record
        $internship->update($request->all());
        return response()->json($internship, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Internship $internship)
    {
        // Deletes the record and returns 204
        $internship->delete();
        return response()->json(null, 204);
    }
}
