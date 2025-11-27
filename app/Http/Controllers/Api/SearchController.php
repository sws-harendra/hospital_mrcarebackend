<?php

namespace App\Http\Controllers\Api;

use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
   /**
     * API: Handles asynchronous unified search requests (returning JSON).
     * Searches doctors and hospitals based on text query, sorting by distance if location is provided.
     */
    public function apiUnifiedSearch(Request $request)
    {
        $query = $request->input('query');
        
        // --- 1. Get User Location ---
        $userLat = $request->input('lat', null); 
        $userLon = $request->input('lon', null); 
        $locationAvailable = false; 
        // dd($query, $userLat, $userLon);

        if (empty($query) && (empty($userLat) || empty($userLon))) {
            return response()->json(['doctors' => [], 'hospitals' => [], 'query' => $query], 200);
        }

        // Define the common WHERE clause for text search
        $textWhereClause = function ($q, $query, $model) {
            $q->where('name', 'LIKE', "%$query%")
              ->orWhere('address', 'LIKE', "%$query%")
              ->orWhere('city', 'LIKE', "%$query%")
              ->orWhere('state', 'LIKE', "%$query%");
            
            if ($model === 'Doctor') {
                $q->orWhere('specialization', 'LIKE', "%$query%");
            } else {
                $q->orWhere('hospital_type', 'LIKE', "%$query%");
            }
        };

        // --- 2. Build Distance Calculation Clause (Haversine Formula) ---
        $distanceSelect = 'id';
        $distanceBindings = [];
        
        if ($userLat !== null && $userLon !== null) {
            $locationAvailable = true; 
            
            // Haversine Formula (Calculates distance in KM)
            $distanceSelect = DB::raw("
                ( 6371 * acos( 
                    LEAST(1.0, GREATEST(-1.0, 
                    cos( radians(?) ) * cos( radians( latitude ) ) 
                    * cos( radians( longitude ) - radians(?) ) 
                    + sin( radians(?) ) * sin( radians( latitude ) ) 
                    ))
                ) ) AS distance
            ");
            $distanceBindings = [$userLat, $userLon, $userLat];
        }


        // --- 3. Search Logic for DOCTORS ---
        $doctorQuery = Doctor::select('*');
        if ($locationAvailable) {
            $doctorQuery->selectRaw($distanceSelect, $distanceBindings);
        }
        
        $doctors = $doctorQuery
            ->where('status', 1)
            ->where(function ($q) use ($query, $textWhereClause) {
                if (!empty($query)) {
                    $textWhereClause($q, $query, 'Doctor');
                } else {
                    $q->whereRaw('1=1'); 
                }
            })
            ->orWhereHas('departments', function ($q) use ($query) {
                if (!empty($query)) $q->where('name', 'LIKE', "%$query%");
            });
            
        // Apply distance sort (closest first)
        if ($locationAvailable) {
            $doctors = $doctors->orderBy('distance');
        } else {
            $doctors = $doctors->orderBy('name'); 
        }

        $doctors = $doctors->with(['reviews', 'photos'])->take(20)->get();

        // --- 4. Search Logic for HOSPITALS ---
        $hospitalQuery = Hospital::select('*');
        if ($locationAvailable) {
            $hospitalQuery->selectRaw($distanceSelect, $distanceBindings);
        }

        $hospitals = $hospitalQuery
            ->where('status', 1)
            ->where(function ($q) use ($query, $textWhereClause) {
                if (!empty($query)) {
                    $textWhereClause($q, $query, 'Hospital');
                } else {
                    $q->whereRaw('1=1');
                }
            })
            ->orWhereHas('departments', function ($q) use ($query) {
                if (!empty($query)) $q->where('name', 'LIKE', "%$query%");
            });

        if ($locationAvailable) {
            $hospitals = $hospitals->orderBy('distance');
        } else {
            $hospitals = $hospitals->orderBy('name');
        }

        $hospitals = $hospitals->with(['reviews', 'photos'])->take(20)->get();

        // --- 5. Final Mapping and Response ---
        $doctors = $doctors->map(function($doc) {
            $doc->avg_rating = $doc->reviews->avg('rating') ?? 0;
            return $doc;
        });

        $hospitals = $hospitals->map(function($hosp) {
            $hosp->avg_rating = $hosp->reviews->avg('rating') ?? 0;
            $hosp->main_image = $hosp->logo ?? ($hosp->photos->first()->photo_path ?? 'img/hospital-default.jpg');
            return $hosp;
        });


        return response()->json(compact('doctors', 'hospitals', 'query', 'locationAvailable'), 200);
    }
}
