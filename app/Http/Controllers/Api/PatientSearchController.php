<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $patients = Patient::where(function($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('national_id', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%");
        })
        ->limit(10)
        ->get()
        ->map(function($patient) {
            return [
                'id' => $patient->id,
                'text' => $patient->full_name . ' - ' . $patient->national_id,
                'name' => $patient->full_name,
                'national_id' => $patient->national_id,
                'phone' => $patient->phone,
                'is_complete' => $patient->is_profile_complete,
            ];
        });
        
        return response()->json($patients);
    }

    public function searchOrCreate(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['error' => 'Search query too short'], 400);
        }
        
        // Check if patient exists by exact national_id or phone (more specific than name)
        $patient = Patient::where('national_id', $query)
            ->orWhere('phone', $query)
            ->first();
        
        if ($patient) {
            return response()->json([
                'action' => 'found',
                'patient' => [
                    'id' => $patient->id,
                    'text' => $patient->full_name . ' - ' . $patient->national_id,
                    'name' => $patient->full_name,
                    'national_id' => $patient->national_id,
                    'phone' => $patient->phone,
                    'is_complete' => $patient->is_profile_complete,
                ]
            ]);
        }
        
        // Check if user has quick registration permission
        $user = auth()->user();
        $permissions = $user->role->permissions ?? [];
        $hasQuickRegistration = in_array('*', $permissions) || in_array('quick_registration', $permissions);
        
        if (!$hasQuickRegistration) {
            return response()->json([
                'action' => 'not_found',
                'message' => 'Patient not found. You need quick_registration permission to auto-create.',
            ]);
        }
        
        // Parse name
        $firstName = trim($query);
        $lastName = ' ';
        
        // If query contains space, split into first and last name
        if (strpos($firstName, ' ') !== false) {
            $parts = explode(' ', $firstName, 2);
            $firstName = $parts[0];
            $lastName = $parts[1] ?? ' ';
        }
        
        // Check if patient with same first and last name already exists
        $existingByName = Patient::where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->first();
        
        if ($existingByName) {
            // Patient with same name exists - return it instead of creating duplicate
            return response()->json([
                'action' => 'found',
                'patient' => [
                    'id' => $existingByName->id,
                    'text' => $existingByName->full_name . ' - ' . $existingByName->national_id,
                    'name' => $existingByName->full_name,
                    'national_id' => $existingByName->national_id,
                    'phone' => $existingByName->phone,
                    'is_complete' => $existingByName->is_profile_complete,
                ],
                'message' => 'Patient with same name already exists. Using existing patient.',
            ]);
        }
        
        // Auto-create patient with minimal info (even if name exists - different person)
        $patient = Patient::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'father_name' => ' ',
            'mother_name' => ' ',
            'birth_date' => null,
            'birth_place' => ' ',
            'national_id' => 'TEMP-' . time() . '-' . rand(1000, 9999),
            'phone' => '0000000000',
            'created_by' => auth()->id(),
            'is_profile_complete' => false,
            'completed_at' => null,
        ]);
        
        return response()->json([
            'action' => 'created',
            'patient' => [
                'id' => $patient->id,
                'text' => $patient->full_name . ' - ' . $patient->national_id,
                'name' => $patient->full_name,
                'national_id' => $patient->national_id,
                'phone' => $patient->phone,
                'is_complete' => false,
            ],
            'message' => 'Patient created with minimal info. Profile needs to be completed.',
        ]);
    }
}
