<?php

namespace App\Http\Controllers;

use App\Models\JobCard;
use App\Models\Technician;
use Illuminate\Http\Request;

// app/Http/Controllers/TechnicianController.php
class TechnicianController extends Controller
{
    // Assign job to nearest qualified technician
    public function assignJob($jobCardId)
    {
        $jobCard = JobCard::findOrFail($jobCardId);
        $serviceRequest = $jobCard->serviceRequest;

        // Find nearest available technician with required skills
        $technician = $this->findNearestTechnician($serviceRequest);

        if ($technician) {
            $jobCard->update([
                'technician_id' => $technician->id,
                'status' => 'pending'
            ]);

            // Notify technician via mobile dashboard
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

    private function findNearestTechnician($serviceRequest)
    {
        // Simplified logic - in production, use geolocation
        return Technician::where('availability_status', 'available')
            ->orderBy('current_workload', 'asc')
            ->first();
    }

    // Update technician location and status
    public function updateLocation(Request $request, $id)
    {
        $technician = Technician::findOrFail($id);
        $technician->update([
            'current_location_lat' => $request->latitude,
            'current_location_lng' => $request->longitude,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateAvailability(Request $request, $id)
    {
        $technician = Technician::findOrFail($id);
        $technician->update(['availability_status' => $request->status]);

        return response()->json(['success' => true]);
    }
}
