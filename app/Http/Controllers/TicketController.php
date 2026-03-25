<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Patient;
use App\Models\Department;
use App\Models\Service;
use App\Models\Payment;
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
        
        // Get department for ticket number format
        $department = Department::findOrFail($validated['department_id']);
        
        // Check if sequence needs to be reset (new day)
        if (!$department->ticket_seq_reset_date || $department->ticket_seq_reset_date->format('Y-m-d') !== $visitDate->format('Y-m-d')) {
            $department->update([
                'ticket_current_seq' => 0,
                'ticket_seq_reset_date' => $visitDate,
            ]);
            $department->refresh();
        }
        
        // Generate queue number for this department on visit date
        $queueNumber = Ticket::where('department_id', $validated['department_id'])
            ->whereDate('visit_date', $validated['visit_date'])
            ->count() + 1;
        
        // Increment sequence
        $department->increment('ticket_current_seq');
        $sequence = str_pad($department->ticket_current_seq, $department->ticket_seq_padding, '0', STR_PAD_LEFT);
        
        // Generate ticket number based on format
        $ticketNumber = str_replace(
            ['{prefix}', '{date}', '{seq}', '{dept}'],
            [
                $department->ticket_prefix ?? 'TKT',
                $visitDate->format('Ymd'),
                $sequence,
                strtoupper(substr($department->name, 0, 3))
            ],
            $department->ticket_number_format ?? '{prefix}-{date}-{seq}'
        );
        
        DB::beginTransaction();
        try {
            // Create ticket
            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'patient_id' => $validated['patient_id'],
                'department_id' => $validated['department_id'],
                'service_id' => $validated['service_id'],
                'cashier_id' => auth()->id(),
                'queue_number' => $queueNumber,
                'amount_paid' => $service->price,
                'visit_date' => $validated['visit_date'],
                'created_at_time' => now(),
                'is_advance_booking' => $isAdvance,
                'booking_date' => $isAdvance ? today() : null,
            ]);
            
            // Create payment
            $receiptNumber = 'RCP-' . date('Ymd') . '-' . str_pad(Payment::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            
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
        return view('tickets.receipt', compact('ticket'));
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
