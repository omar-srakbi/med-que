<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Service;
use App\Models\TicketSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('services')->with('services')->latest()->get();
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sequence_prefix' => 'required|string|size:2|alpha',
            'queue_prefix' => [
                'required',
                'string',
                'size:2',
                \Illuminate\Validation\Rule::unique('departments', 'queue_prefix'),
            ],
        ]);

        $validated['sequence_prefix'] = strtoupper($validated['sequence_prefix']);
        $validated['queue_prefix'] = strtoupper($validated['queue_prefix']);

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة القسم بنجاح' : 'Department added successfully');
    }

    public function show(Department $department)
    {
        $department->load('services');
        $currentYear = (int) now()->year;
        $sequence = \App\Models\TicketSequence::firstOrCreate(
            ['sequence_prefix' => $department->sequence_prefix, 'sequence_year' => $currentYear],
            ['sequence_counter' => 0]
        );
        return view('departments.show', compact('department', 'sequence'));
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sequence_prefix' => 'required|string|size:2|alpha',
            'queue_prefix' => [
                'required',
                'string',
                'size:2',
                \Illuminate\Validation\Rule::unique('departments', 'queue_prefix')->ignore($department->id),
            ],
        ]);

        $validated['sequence_prefix'] = strtoupper($validated['sequence_prefix']);
        $validated['queue_prefix'] = strtoupper($validated['queue_prefix']);

        $department->update($validated);

        return redirect()->route('departments.show', $department)
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث القسم بنجاح' : 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        if ($department->tickets()->count() > 0) {
            return back()->withErrors(['error' => app()->getLocale() === 'ar' 
                ? 'لا يمكن حذف القسم بينما توجد تذاكر مرتبطة به' 
                : 'Cannot delete department while tickets are associated']);
        }
        
        $department->delete();
        
        return redirect()->route('departments.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف القسم بنجاح' : 'Department deleted successfully');
    }

    public function addService(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'shortcut' => 'nullable|string|max:20|unique:services,shortcut',
        ]);

        $validated['department_id'] = $department->id;

        Service::create($validated);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم إضافة الخدمة بنجاح' : 'Service added successfully');
    }

    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'shortcut' => 'nullable|string|max:20|unique:services,shortcut,' . $service->id,
            'is_active' => 'boolean',
        ]);

        $service->update($validated);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم تحديث الخدمة بنجاح' : 'Service updated successfully');
    }

    public function destroyService(Service $service)
    {
        $departmentId = $service->department_id;
        $service->delete();
        
        return redirect()->route('departments.show', $departmentId)
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف الخدمة بنجاح' : 'Service deleted successfully');
    }

    public function updateTicketSettings(Request $request, Department $department)
    {
        $validated = $request->validate([
            'sequence_prefix' => 'required|string|size:2',
            'queue_prefix' => 'required|string|size:2',
            'reset_sequence' => 'nullable|integer',
        ]);

        $newPrefix = strtoupper($validated['sequence_prefix']);
        $newQueuePrefix = strtoupper($validated['queue_prefix']);

        DB::beginTransaction();
        try {
            // Update department prefixes
            $department->update([
                'sequence_prefix' => $newPrefix,
                'queue_prefix' => $newQueuePrefix,
            ]);

            // Reset sequence if requested (create new sequence record for this prefix)
            if ($validated['reset_sequence'] == 1) {
                $currentYear = (int) now()->year;
                TicketSequence::updateOrCreate(
                    ['sequence_prefix' => $newPrefix, 'sequence_year' => $currentYear],
                    ['sequence_counter' => 0]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update: ' . $e->getMessage()]);
        }

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم تحديث إعدادات التذاكر بنجاح'
            : 'Ticket settings updated successfully');
    }

    /**
     * Check if a queue prefix is available and suggest next available
     */
    public function checkQueuePrefix(Request $request)
    {
        $prefix = strtoupper(trim($request->input('prefix', '')));
        
        // Validate input (2 characters, alphanumeric)
        if (strlen($prefix) !== 2) {
            return response()->json([
                'valid' => false,
                'available' => false,
                'message' => app()->getLocale() === 'ar' 
                    ? 'البادئة يجب أن تكون حرفين فقط' 
                    : 'Prefix must be exactly 2 characters',
            ]);
        }

        // Check if prefix exists
        $exists = Department::where('queue_prefix', $prefix)->exists();
        
        if ($exists) {
            // Find next available prefix
            $nextAvailable = $this->findNextAvailablePrefix();
            return response()->json([
                'valid' => true,
                'available' => false,
                'taken' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'هذه البادئة مستخدمة بالفعل'
                    : 'This prefix is already taken',
                'suggested' => $nextAvailable,
            ]);
        }

        return response()->json([
            'valid' => true,
            'available' => true,
            'taken' => false,
            'message' => app()->getLocale() === 'ar'
                ? 'هذه البادئة متاحة'
                : 'This prefix is available',
        ]);
    }

    /**
     * Find the next available queue prefix
     */
    private function findNextAvailablePrefix(): string
    {
        $existing = Department::pluck('queue_prefix')->map(fn($p) => strtoupper($p))->toArray();
        
        // Generate prefixes in order: Q1-Q9, QA-QZ, then A1-A9, AA-AZ, B1-B9, etc.
        $letters = range('A', 'Z');
        $digits = range(1, 9);
        
        // First try Q + digit (Q1-Q9)
        foreach ($digits as $digit) {
            $prefix = 'Q' . $digit;
            if (!in_array($prefix, $existing)) {
                return $prefix;
            }
        }
        
        // Then try Q + letter (QA-QZ)
        foreach ($letters as $letter) {
            $prefix = 'Q' . $letter;
            if (!in_array($prefix, $existing)) {
                return $prefix;
            }
        }
        
        // Then try all other combinations: A1-A9, AA-AZ, B1-B9, etc.
        foreach ($letters as $letter) {
            // Skip Q as we already checked it
            if ($letter === 'Q') continue;
            
            // Letter + digit
            foreach ($digits as $digit) {
                $prefix = $letter . $digit;
                if (!in_array($prefix, $existing)) {
                    return $prefix;
                }
            }
            
            // Letter + letter
            foreach ($letters as $secondLetter) {
                $prefix = $letter . $secondLetter;
                if (!in_array($prefix, $existing)) {
                    return $prefix;
                }
            }
        }
        
        // Fallback (should never reach here with 1296+ possibilities)
        return 'Z9';
    }
}
