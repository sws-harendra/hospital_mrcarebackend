<?php
// app/Http/Controllers/Frontend/SearchController.php (Modified for Fee & Hospital Names)

namespace App\Http\Controllers\Frontend;

use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function unifiedSearch(Request $request)
    {
        $query = $request->input('query');
        $userLat = $request->input('lat', null); 
        $userLon = $request->input('lon', null); 
        $locationAvailable = ($userLat !== null && $userLon !== null); 

        if (empty($query) && (empty($userLat) || empty($userLon))) {
            return view('frontend.pages.search-results', ['doctors' => collect(), 'hospitals' => collect(), 'query' => $query]);
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
        
        if ($locationAvailable) {
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
            })
            
            // FIX: Eager load hospitals to display their names
            ->with(['reviews', 'photos', 'hospitals.departments']) 
            
            ->when($locationAvailable, function ($q) {
                return $q->orderBy('distance');
            }, function ($q) {
                return $q->orderBy('name'); 
            })
            ->take(20)
            ->get();

            // dd($doctors);
        // --- 4. Search Logic for HOSPITALS (Same as before) ---
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
            })
            ->when($locationAvailable, function ($q) {
                return $q->orderBy('distance');
            }, function ($q) {
                return $q->orderBy('name');
            })
            ->with(['reviews', 'photos', 'departments'])
            ->take(20)->get();

            // dd($hospitals);

        // --- 5. Final Mapping ---
        $doctors = $doctors->map(function($doc) {
            $doc->avg_rating = $doc->reviews->avg('rating') ?? 0;
            // Get a string of hospital names (e.g., 'City Clinic, Apollo')
            $doc->hospital_names = $doc->hospitals->pluck('name')->implode(', ');
            return $doc;
        });

        $hospitals = $hospitals->map(function($hosp) {
            $hosp->avg_rating = $hosp->reviews->avg('rating') ?? 0;
            $hosp->main_image = $hosp->logo ?? ($hosp->photos->first()->photo_path ?? 'img/hospital-default.jpg');
            return $hosp;
        });


        return view('frontend.pages.search-results', compact('doctors', 'hospitals', 'query', 'locationAvailable'));
    }
}