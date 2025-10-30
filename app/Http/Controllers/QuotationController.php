<?php

namespace App\Http\Controllers;

use App\Models\JobCard;
use App\Models\Quotation;
use App\Models\Technician;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    /**
     * List all quotations (Manager view)
     */
    public function index()
    {
        $quotations = Quotation::with('serviceRequest', 'serviceRequest.customer')
            ->paginate(15);

        return view('quotations.index', ['quotations' => $quotations]);
    }

    /**
     * Show quotation details (Manager view)
     */
    public function show($id)
    {
        $quotation = Quotation::with('serviceRequest', 'serviceRequest.customer', 'serviceRequest.machine')
            ->findOrFail($id);

        return view('quotations.show', ['quotation' => $quotation]);
    }

    /**
     * Approve quotation with AUTOMATIC technician assignment
     * FIXED: Returns redirect for form submission OR JSON for AJAX
     */
    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $quotation = Quotation::findOrFail($id);
            
            // Update quotation status
            $quotation->update([
                'status' => 'approved',
                'approved_at' => now()
            ]);

            $serviceRequest = $quotation->serviceRequest;
            
            // Create job card and auto-assign technician
            $result = $this->autoAssignTechnician($serviceRequest);

            if ($result['success']) {
                DB::commit();
                
                $message = 'Quotation approved successfully! Job assigned to ' . $result['technician_name'];
                
                // Check if it's an AJAX request (from JavaScript)
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'job_card_id' => $result['job_card_id'],
                        'technician_name' => $result['technician_name']
                    ]);
                }
                
                // For regular form submission, redirect back with success message
                return redirect()
                    ->back()
                    ->with('success', $message);
                    
            } else {
                DB::rollBack();
                
                // Check if it's an AJAX request
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ], 400);
                }
                
                // For regular form submission
                return redirect()
                    ->back()
                    ->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quotation approval failed: ' . $e->getMessage());
            
            $errorMessage = 'Failed to approve quotation. Please try again.';
            
            // Check if it's an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            // For regular form submission
            return redirect()
                ->back()
                ->with('error', $errorMessage);
        }
    }

    /**
     * AUTO-ASSIGN TECHNICIAN - Complete Implementation
     * Creates job card and assigns best available technician
     */
    private function autoAssignTechnician($serviceRequest)
    {
        try {
            // Find the best available technician
            $technician = $this->findBestTechnician($serviceRequest);

            if (!$technician) {
                // No technician available - update request status but don't fail
                $serviceRequest->update(['status' => 'pending_assignment']);
                
                return [
                    'success' => false,
                    'message' => 'No available technician found. Job marked as pending assignment.'
                ];
            }

            // Generate unique job reference number
            $jobReference = 'JOB-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

            // Create job card
            $jobCard = JobCard::create([
                'service_request_id' => $serviceRequest->id,
                'technician_id' => $technician->id,
                'job_reference' => $jobReference,
                'status' => 'pending',
                'estimated_duration' => $this->estimateDuration($serviceRequest),
                'notes' => 'Auto-assigned to ' . $technician->user->name . ' on ' . now()->format('Y-m-d H:i:s')
            ]);

            // Update service request status
            $serviceRequest->update(['status' => 'assigned']);

            // Update technician workload
            $technician->increment('current_workload');

            // Log the assignment
            Log::info("Job auto-assigned", [
                'job_card_id' => $jobCard->id,
                'service_request_id' => $serviceRequest->id,
                'technician_id' => $technician->id,
                'technician_name' => $technician->user->name
            ]);

            return [
                'success' => true,
                'job_card_id' => $jobCard->id,
                'technician_name' => $technician->user->name,
                'job_reference' => $jobReference
            ];

        } catch (\Exception $e) {
            Log::error('Auto-assignment failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Auto-assignment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Find the BEST available technician based on multiple factors
     * Priority: Availability > Skills Match > Workload > Location
     */
    private function findBestTechnician($serviceRequest)
    {
        // Get all available technicians
        $availableTechnicians = Technician::where('availability_status', 'available')
            ->with('user')
            ->get();

        if ($availableTechnicians->isEmpty()) {
            return null;
        }

        // Score each technician
        $scoredTechnicians = $availableTechnicians->map(function ($technician) use ($serviceRequest) {
            $score = 0;

            // 1. Skills match (highest priority) - +50 points
            if ($this->hasMatchingSkills($technician, $serviceRequest)) {
                $score += 50;
            }

            // 2. Low workload - +30 points for 0 jobs, decreasing by 5 per job
            $workloadScore = max(0, 30 - ($technician->current_workload * 5));
            $score += $workloadScore;

            // 3. Location proximity (if available) - +20 points for close proximity
            if ($technician->current_location_lat && $technician->current_location_lng) {
                // Simplified distance calculation (you can enhance this)
                $score += 20;
            }

            return [
                'technician' => $technician,
                'score' => $score
            ];
        });

        // Return technician with highest score
        $bestMatch = $scoredTechnicians->sortByDesc('score')->first();
        
        return $bestMatch ? $bestMatch['technician'] : null;
    }

    /**
     * Check if technician has skills matching the service request type
     */
    private function hasMatchingSkills($technician, $serviceRequest)
    {
        $requestType = $serviceRequest->request_type; // breakdown, maintenance, installation
        $technicianSkills = strtolower($technician->skills ?? '');

        // Check if skills contain relevant keywords
        $skillKeywords = [
            'breakdown' => ['repair', 'breakdown', 'emergency', 'troubleshoot'],
            'maintenance' => ['maintenance', 'preventive', 'service', 'inspection'],
            'installation' => ['installation', 'setup', 'commissioning', 'new']
        ];

        $keywords = $skillKeywords[$requestType] ?? [];

        foreach ($keywords as $keyword) {
            if (str_contains($technicianSkills, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Estimate job duration based on request type
     */
    private function estimateDuration($serviceRequest)
    {
        $durations = [
            'breakdown' => 4,      // 4 hours for breakdowns
            'maintenance' => 2,    // 2 hours for maintenance
            'installation' => 6    // 6 hours for installations
        ];

        return $durations[$serviceRequest->request_type] ?? 3;
    }

    /**
     * Reject quotation
     * FIXED: Returns redirect for form submission OR JSON for AJAX
     */
    public function reject(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->update([
            'status' => 'rejected',
            'rejected_at' => now()
        ]);

        $serviceRequest = $quotation->serviceRequest;
        $serviceRequest->update(['status' => 'rejected']);

        $message = 'Quotation rejected successfully.';
        
        // Check if it's an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        
        // For regular form submission
        return redirect()
            ->back()
            ->with('warning', $message);
    }

    // ============================================================
    // DATA CAPTURER METHODS
    // ============================================================

    /**
     * List quotations (Data Capturer view)
     */
    public function capturerIndex()
    {
        $quotations = Quotation::with('serviceRequest', 'serviceRequest.customer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('data-capturer.quotations.index', ['quotations' => $quotations]);
    }

    /**
     * Show quotation details (Data Capturer view)
     */
    public function capturerShow($id)
    {
        $quotation = Quotation::with('serviceRequest', 'serviceRequest.customer', 'serviceRequest.machine')
            ->findOrFail($id);

        return view('data-capturer.quotations.show', ['quotation' => $quotation]);
    }

    // ============================================================
    // MANUAL ASSIGNMENT (Fallback for managers)
    // ============================================================

    /**
     * Manually assign/reassign technician to job card
     * Used when auto-assignment fails or needs override
     */
    public function manualAssign(Request $request, $id)
    {
        $validated = $request->validate([
            'technician_id' => 'required|exists:technicians,id'
        ]);

        $quotation = Quotation::findOrFail($id);
        $jobCard = $quotation->serviceRequest->jobCard;

        if ($jobCard) {
            // Reassignment - decrease old technician's workload
            if ($jobCard->technician_id) {
                Technician::find($jobCard->technician_id)->decrement('current_workload');
            }

            $jobCard->update(['technician_id' => $validated['technician_id']]);
            Technician::find($validated['technician_id'])->increment('current_workload');

            return response()->json([
                'success' => true,
                'message' => 'Technician reassigned successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No job card found'
        ], 404);
    }

    /**
     * Get pending assignments (jobs without technicians)
     */
    public function pendingAssignments()
    {
        $pendingRequests = ServiceRequest::where('status', 'pending_assignment')
            ->with('quotation', 'customer')
            ->get();

        return view('quotations.pending-assignments', [
            'pendingRequests' => $pendingRequests
        ]);
    }
}