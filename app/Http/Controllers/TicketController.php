<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Patient;
use App\Models\Department;
use App\Models\Service;
use App\Models\Payment;
use App\Models\TicketSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['patient', 'department', 'service', 'cashier', 'payment']);
        
        if ($request->has('date')) {
            $query->whereDate('visit_date', $request->date);
        } else {
            $query->whereDate('visit_date', today());
        }
        
        if ($request->has('department')) {
            $query->where('department_id', $request->department);
        }
        
        $tickets = $query->latest()->paginate(20);
        $departments = Department::where('is_active', true)->get();
        
        return view('tickets.index', compact('tickets', 'departments'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $patients = Patient::limit(100)->orderBy('created_at', 'desc')->get();
        $canBookAdvance = auth()->user()->canBookAdvanceTickets();

        return view('tickets.create', compact('departments', 'patients', 'canBookAdvance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'department_id' => 'required|exists:departments,id',
            'service_id' => 'required|exists:services,id',
            'is_advance_booking' => 'nullable|boolean',
            'visit_date' => 'required|date|after_or_equal:today',
        ]);
        
        // Check if advance booking is allowed
        $canBookAdvance = auth()->user()->canBookAdvanceTickets();
        $visitDate = \Carbon\Carbon::parse($validated['visit_date']);
        $isAdvance = $visitDate->format('Y-m-d') !== today()->format('Y-m-d');
        
        // Only users with permission can book for next day
        if ($isAdvance && !$canBookAdvance) {
            return back()->withErrors(['visit_date' => app()->getLocale() === 'ar' 
                ? 'ليس لديك صلاحية حجز تذاكر لليوم التالي' 
                : 'You do not have permission to book tickets for the next day']);
        }
        
        // Only allow next day booking (not further)
        if ($isAdvance && $visitDate->format('Y-m-d') !== now()->addDay()->format('Y-m-d')) {
            return back()->withErrors(['visit_date' => app()->getLocale() === 'ar' 
                ? 'يسمح بالحجز ليوم غد فقط' 
                : 'Only booking for tomorrow is allowed']);
        }
        
        // Check if patient already has a ticket for this department on this date
        $existingTicket = Ticket::where('patient_id', $validated['patient_id'])
            ->where('department_id', $validated['department_id'])
            ->whereDate('visit_date', $validated['visit_date'])
            ->first();
        
        if ($existingTicket) {
            return back()->withErrors(['department' => app()->getLocale() === 'ar' 
                ? 'المريض لديه تذكرة سابقة لهذا القسم في هذا التاريخ' 
                : 'Patient already has a ticket for this department on this date']);
        }
        
        // Get service price
        $service = Service::findOrFail($validated['service_id']);

        DB::beginTransaction();
        try {
            // Get department for sequence (locked to prevent concurrent modifications)
            $department = Department::lockForUpdate()->findOrFail($validated['department_id']);

            // Get or create global sequence with locking (shared across all departments with same prefix)
            $currentYear = (int) now()->year;
            $sequence = TicketSequence::lockForUpdate()->firstOrCreate(
                ['sequence_prefix' => $department->sequence_prefix, 'sequence_year' => $currentYear],
                ['sequence_counter' => 0]
            );
            $sequence->increment('sequence_counter');
            $sequenceNumber = str_pad($sequence->sequence_counter, 8, '0', STR_PAD_LEFT);

            // Generate queue number for this department (per-day, unique prefix + 4 digits)
            $queueCount = Ticket::where('department_id', $validated['department_id'])
                ->whereDate('visit_date', $validated['visit_date'])
                ->lockForUpdate()
                ->count();
            $queuePrefix = $department->queue_prefix ?? 'Q';
            $queueNumberFormatted = $queuePrefix . str_pad($queueCount + 1, 4, '0', STR_PAD_LEFT);

            // Generate ticket number: 2-char prefix + 8-digit sequence
            $ticketNumber = $department->sequence_prefix . $sequenceNumber;

            // Create ticket
            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'patient_id' => $validated['patient_id'],
                'department_id' => $validated['department_id'],
                'service_id' => $validated['service_id'],
                'cashier_id' => auth()->id(),
                'queue_number' => $queueNumberFormatted,
                'amount_paid' => $service->price,
                'visit_date' => $validated['visit_date'],
                'created_at_time' => now(),
                'is_advance_booking' => $isAdvance,
                'booking_date' => $isAdvance ? today() : null,
            ]);

            // Create payment with locked receipt number generation
            $receiptCount = Payment::whereDate('created_at', today())
                ->lockForUpdate()
                ->count();
            $receiptNumber = 'RCP-' . date('Ymd') . '-' . str_pad($receiptCount + 1, 4, '0', STR_PAD_LEFT);

            Payment::create([
                'ticket_id' => $ticket->id,
                'amount' => $service->price,
                'payment_method' => 'cash',
                'receipt_number' => $receiptNumber,
                'cashier_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('tickets.receipt', $ticket)
                ->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء التذكرة بنجاح' : 'Ticket created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create ticket: ' . $e->getMessage()]);
        }
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['patient', 'department', 'service', 'cashier', 'payment', 'medicalRecord']);
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $departments = Department::where('is_active', true)->get();
        $patients = Patient::latest()->limit(50)->get();
        
        return view('tickets.edit', compact('ticket', 'departments', 'patients'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'department_id' => 'required|exists:departments,id',
            'service_id' => 'required|exists:services,id',
        ]);
        
        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث التذكرة بنجاح' : 'Ticket updated successfully');
    }

    public function receipt(Ticket $ticket)
    {
        $ticket->load(['patient', 'department', 'service', 'cashier', 'payment']);

        // Get print settings
        $settings = [];
        foreach (\App\Models\PrintSetting::all() as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }

        // Log the print action
        \App\Models\PrintLog::logPrint('receipt', $ticket, null, 1, 'success');

        return view('tickets.receipt', compact('ticket', 'settings'));
    }

    public function call(Ticket $ticket)
    {
        $ticket->update([
            'called_number' => $ticket->queue_number,
        ]);
        
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم استدعاء المريض' : 'Patient called');
    }

    public function complete(Ticket $ticket)
    {
        $ticket->update([
            'completed_at' => now(),
        ]);
        
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم إكمال التذكرة' : 'Ticket completed');
    }

    public function destroy(Ticket $ticket)
    {
        $user = auth()->user();
        
        // Admin can delete any ticket
        if ($user->role->name === 'Admin') {
            $ticket->delete();
            return redirect()->route('tickets.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم حذف التذكرة بنجاح' : 'Ticket deleted successfully');
        }
        
        // Head Cashier can only delete incomplete tickets
        if ($user->hasPermission('delete_tickets')) {
            if ($ticket->completed_at) {
                return back()->withErrors(['error' => app()->getLocale() === 'ar' 
                    ? 'لا يمكن حذف التذكرة المكتملة' 
                    : 'Cannot delete completed ticket']);
            }
            
            $ticket->delete();
            return redirect()->route('tickets.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم حذف التذكرة بنجاح' : 'Ticket deleted successfully');
        }
        
        return back()->withErrors(['error' => app()->getLocale() === 'ar' 
            ? 'ليس لديك صلاحية حذف التذاكر' 
            : 'You do not have permission to delete tickets']);
    }
}
