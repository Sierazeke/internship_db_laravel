<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    protected ApplicationService $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'internship_id' => 'required|integer|exists:internships,id',
            'motivation_letter' => 'nullable|string|max:2000',
        ]);

        try {
            $application = $this->applicationService->createApplication(
                $request->input('user_id'),
                $request->input('internship_id'),
                $request->input('motivation_letter')
            );

            return response()->json([
                'success' => true,
                'message' => 'Pieteikums veiksmīgi izveidots.',
                'data' => $application,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        //
    }

    /**
     * Create application using stored procedure.
     */
    public function storeWithProcedure(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'internship_id' => 'required|integer|exists:internships,id',
            'motivation_letter' => 'nullable|string|max:2000',
        ]);

        try {
            $applicationId = null;
            $errorMessage = null;

            // Call stored procedure
            DB::statement(
                'CALL create_application_with_validation(?, ?, ?, @p_application_id, @p_error_message)',
                [
                    $request->input('user_id'),
                    $request->input('internship_id'),
                    $request->input('motivation_letter'),
                ]
            );

            // Get output parameters
            $result = DB::select('SELECT @p_application_id AS application_id, @p_error_message AS error_message');
            
            if (isset($result[0]->application_id)) {
                $application = Application::find($result[0]->application_id);

                return response()->json([
                    'success' => true,
                    'message' => 'Pieteikums veiksmīgi izveidots, izmantojot saglabāto procedūru.',
                    'data' => $application,
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result[0]->error_message ?? 'Nezināma kļūda.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
