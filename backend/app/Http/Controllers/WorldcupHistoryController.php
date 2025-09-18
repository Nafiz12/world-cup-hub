<?php

namespace App\Http\Controllers;

use App\Models\WorldcupHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorldcupHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():JsonResponse
    {
        // Fetch all records from the worldcup_history table
        $history = WorldcupHistory::all();

        // Return the data as a JSON response
        return response()->json($history);
    }
}
