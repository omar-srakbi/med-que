<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Service;
use Illuminate\Http\Request;

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
        ]);
        
        Department::create($validated);
        
        return redirect()->route('departments.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة القسم بنجاح' : 'Department added successfully');
    }

    public function show(Department $department)
    {
        $department->load('services');
        return view('departments.show', compact('department'));
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
        ]);
        
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
            'ticket_prefix' => 'required|string|max:10',
            'ticket_number_format' => 'required|string|max:255',
            'ticket_seq_padding' => 'required|integer|min:2|max:10',
            'reset_sequence' => 'nullable|integer',
        ]);
        
        $updateData = [
            'ticket_prefix' => $validated['ticket_prefix'],
            'ticket_number_format' => $validated['ticket_number_format'],
            'ticket_seq_padding' => $validated['ticket_seq_padding'],
        ];
        
        if ($validated['reset_sequence'] == 1) {
            $updateData['ticket_current_seq'] = 0;
            $updateData['ticket_seq_reset_date'] = today();
        }
        
        $department->update($updateData);
        
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم تحديث إعدادات التذاكر بنجاح' : 'Ticket settings updated successfully');
    }
}
