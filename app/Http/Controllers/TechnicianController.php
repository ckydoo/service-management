<?php

namespace App\Http\Controllers;

use App\Models\JobCard;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TechnicianController extends Controller
{
    /**
     * Display list of technicians (Manager only)
     */
    public function index()
    {
        $technicians = Technician::with('user')->paginate(15);
        return view('technicians.index', ['technicians' => $technicians]);
    }

    /**
     * Show technician details
     */
    public function show($id)
    {
        $technician = Technician::with('user', 'jobCards')->findOrFail($id);
        return view('technicians.show', ['technician' => $technician]);
    }

    /**
     * Show create technician form
     */
    public function create()
    {
        return view('technicians.create');
    }

    /**
     * Store a new technician (Manager only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|unique:technicians,license_number',
            'skills' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        // Create user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'technician',
            'email_verified_at' => now(),
        ]);

        // Create technician profile
        Technician::create([
            'user_id' => $user->id,
            'specialization' => $validated['specialization'],
            'license_number' => $validated['license_number'],
            'skills' => $validated['skills'],
            'availability_status' => 'available',
            'current_workload' => 0,
            'current_location_lat' => -17.8252,
            'current_location_lng' => 31.0335,
        ]);

        return redirect()->route('technicians.index')
            ->with('success', 'Technician added successfully');
    }

    /**
     * Show edit technician form
     */
    public function edit($id)
    {
        $technician = Technician::with('user')->findOrFail($id);
        return view('technicians.edit', ['technician' => $technician]);
    }

    /**
     * Update technician information
     */
    public function update(Request $request, $id)
    {
        $technician = Technician::with('user')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $technician->user->id,
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|unique:technicians,license_number,' . $id,
            'skills' => 'required|string',
        ]);

        $technician->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $technician->update([
            'specialization' => $validated['specialization'],
            'license_number' => $validated['license_number'],
            'skills' => $validated['skills'],
        ]);

        return redirect()->route('technicians.show', $technician->id)
            ->with('success', 'Technician updated successfully');
    }

    /**
     * Assign job to technician (Manager only)
     */
    public function assignJob($jobCardId)
    {
        $jobCard = JobCard::findOrFail($jobCardId);
        $serviceRequest = $jobCard->serviceRequest;

        $technician = $this->findNearestTechnician($serviceRequest);

        if ($technician) {
            $jobCard->update([
                'technician_id' => $technician->id,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'technician_id' => $technician->id,
                'message' => 'Job assigned to technician'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No available technician found'
        ], 404);
    }

    /**
     * Find nearest available technician
     */
    private function findNearestTechnician($serviceRequest)
    {
        return Technician::where('availability_status', 'available')
            ->orderBy('current_workload', 'asc')
            ->first();
    }




    /**
     * Get technician profile
     */
    public function profile()
    {
        $technician = auth()->user()->technician;
        return view('technicians.profile', ['technician' => $technician]);
    }

/**
 * Update technician's current location
 * Used by technician mobile app or dashboard
 */
public function updateLocation(Request $request)
{
    $user = auth()->user();
    $technician = $user->technician;

    if (!$technician) {
        return response()->json(['error' => 'User is not a technician'], 403);
    }

    $validated = $request->validate([
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
    ]);

    $technician->update([
        'current_location_lat' => $validated['latitude'],
        'current_location_lng' => $validated['longitude'],
        'last_location_update' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Location updated successfully'
    ]);
}

/**
 * Update technician's availability status
 * Statuses: available, busy, offline
 */
public function updateAvailability(Request $request)
{
    $user = auth()->user();
    $technician = $user->technician;

    if (!$technician) {
        return response()->json(['error' => 'User is not a technician'], 403);
    }

    $validated = $request->validate([
        'status' => 'required|in:available,busy,offline',
    ]);

    $technician->update([
        'availability_status' => $validated['status'],
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Availability status updated successfully',
        'status' => $validated['status']
    ]);
}





}
