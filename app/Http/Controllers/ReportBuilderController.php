<?php

namespace App\Http\Controllers;

use App\Models\CustomReport;
use App\Models\ReportPermission;
use App\Models\Department;
use App\Models\Service;
use App\Models\Patient;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReportBuilderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get reports user has access to
        $reports = CustomReport::where('created_by', $user->id)
            ->orWhere('is_public', true)
            ->orWhereHas('permissions', function($q) use ($user) {
                $q->where(function($sub) use ($user) {
                    $sub->where('user_id', $user->id)
                        ->orWhere('role_id', $user->role_id);
                })->where('can_view', true);
            })
            ->with('creator')
            ->latest()
            ->paginate(20);

        return view('reports.builder.index', compact('reports'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermission('create_reports')) {
            abort(403, 'Unauthorized');
        }

        $dataSources = $this->getDataSources();
        $canUseAdvanced = auth()->user()->hasPermission('use_advanced_builder');

        return view('reports.builder.create', compact('dataSources', 'canUseAdvanced'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('create_reports')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_type' => 'required|in:simple,advanced',
            'data_source' => 'required|string',
            'columns' => 'required|array',
            'column_labels' => 'nullable|array',
            'filters' => 'nullable|array',
            'joins' => 'nullable|array',
            'calculations' => 'nullable|array',
            'group_by' => 'nullable|array',
            'order_by' => 'nullable|array',
            'column_width' => 'nullable|integer|min:50|max:500',
            'row_height' => 'nullable|integer|min:20|max:200',
            'report_header' => 'nullable|string',
            'report_footer' => 'nullable|string',
            'is_public' => 'boolean',
            'cache_duration_minutes' => 'integer|min:1|max:1440',
        ]);

        // Filter column_labels to only include labels for selected columns
        $columnLabels = [];
        if (!empty($validated['column_labels']) && !empty($validated['columns'])) {
            foreach ($validated['columns'] as $column) {
                if (isset($validated['column_labels'][$column]) && !empty($validated['column_labels'][$column])) {
                    $columnLabels[$column] = $validated['column_labels'][$column];
                }
            }
        }

        $report = CustomReport::create([
            ...$validated,
            'column_labels' => !empty($columnLabels) ? $columnLabels : null,
            'cache_enabled' => $request->has('cache_enabled'),
            'column_width' => $request->column_width ?? 150,
            'row_height' => $request->row_height ?? 40,
            'cache_duration_minutes' => $request->cache_duration_minutes ?? 10,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('reports.builder.index')
            ->with('success', __('Report created successfully!'));
    }

    public function edit(CustomReport $report)
    {
        if (!$report->canEdit(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        $dataSources = $this->getDataSources();
        $canUseAdvanced = auth()->user()->hasPermission('use_advanced_builder');

        return view('reports.builder.edit', compact('report', 'dataSources', 'canUseAdvanced'));
    }

    public function update(Request $request, CustomReport $report)
    {
        if (!$report->canEdit(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_type' => 'required|in:simple,advanced',
            'data_source' => 'required|string',
            'columns' => 'required|array',
            'column_labels' => 'nullable|array',
            'filters' => 'nullable|array',
            'joins' => 'nullable|array',
            'calculations' => 'nullable|array',
            'group_by' => 'nullable|array',
            'order_by' => 'nullable|array',
            'column_width' => 'nullable|integer|min:50|max:500',
            'row_height' => 'nullable|integer|min:20|max:200',
            'report_header' => 'nullable|string',
            'report_footer' => 'nullable|string',
            'is_public' => 'boolean',
            'cache_duration_minutes' => 'integer|min:1|max:1440',
        ]);

        // Filter column_labels to only include labels for selected columns
        $columnLabels = [];
        if (!empty($validated['column_labels']) && !empty($validated['columns'])) {
            foreach ($validated['columns'] as $column) {
                if (isset($validated['column_labels'][$column]) && !empty($validated['column_labels'][$column])) {
                    $columnLabels[$column] = $validated['column_labels'][$column];
                }
            }
        }

        try {
            $report->update([
                ...$validated,
                'column_labels' => !empty($columnLabels) ? $columnLabels : null,
                'cache_enabled' => $request->has('cache_enabled'),
                'column_width' => $request->column_width ?? 150,
                'row_height' => $request->row_height ?? 40,
                'cache_duration_minutes' => $request->cache_duration_minutes ?? 10,
                'updated_by' => auth()->id(),
            ]);

            // Clear cache when report is updated
            $report->clearCache();

            return redirect()->route('reports.builder.index')
                ->with('success', __('Report updated successfully!'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update report: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(CustomReport $report)
    {
        if (!$report->canDelete(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        $report->delete();

        return redirect()->route('reports.builder.index')
            ->with('success', __('Report deleted successfully!'));
    }

    public function preview(Request $request)
    {
        $dataSource = $request->input('data_source');
        $columns = $request->input('columns', []);
        $filters = $request->input('filters', []);
        $limit = $request->input('limit', 10);

        $data = $this->executeQuery($dataSource, $columns, $filters, $limit);

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => count($data),
        ]);
    }

    public function show(CustomReport $report)
    {
        if (!$report->canView(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        // Check cache first
        $cached = $report->getCache();
        
        if ($cached && $cached['cached']) {
            $data = $cached['data'];
            $isCached = true;
            $cachedAt = $cached['cached_at'];
            $expiresAt = $cached['expires_at'];
        } else {
            // Execute query
            $data = $this->executeQuery(
                $report->data_source,
                $report->columns,
                $report->filters,
                1000
            );
            
            // Cache if enabled and large dataset
            if ($report->cache_enabled && count($data) > 50000) {
                $report->setCache($data);
            }
            
            $isCached = false;
            $cachedAt = null;
            $expiresAt = null;
        }

        return view('reports.builder.show', compact('report', 'data', 'isCached', 'cachedAt', 'expiresAt'));
    }

    private function getDataSources()
    {
        return [
            'tickets' => [
                'name' => app()->getLocale() === 'ar' ? 'التذاكر' : 'Tickets',
                'columns' => [
                    'id', 'ticket_number', 'patient_id', 'department_id', 'service_id',
                    'cashier_id', 'queue_number', 'amount_paid', 'visit_date', 'created_at',
                ],
                'with' => ['patient', 'department', 'service', 'cashier'],
            ],
            'payments' => [
                'name' => app()->getLocale() === 'ar' ? 'المدفوعات' : 'Payments',
                'columns' => [
                    'id', 'ticket_id', 'amount', 'payment_method', 'receipt_number',
                    'cashier_id', 'created_at',
                ],
                'with' => ['ticket', 'cashier'],
            ],
            'patients' => [
                'name' => app()->getLocale() === 'ar' ? 'المرضى' : 'Patients',
                'columns' => [
                    'id', 'first_name', 'last_name', 'father_name', 'mother_name',
                    'birth_date', 'national_id', 'phone', 'created_at',
                ],
                'with' => [],
            ],
            'departments' => [
                'name' => app()->getLocale() === 'ar' ? 'الأقسام' : 'Departments',
                'columns' => [
                    'id', 'name', 'name_ar', 'ticket_prefix', 'ticket_current_seq',
                    'is_active', 'created_at',
                ],
                'with' => [],
            ],
            'services' => [
                'name' => app()->getLocale() === 'ar' ? 'الخدمات' : 'Services',
                'columns' => [
                    'id', 'name', 'name_ar', 'price', 'department_id', 'shortcut',
                    'is_active', 'created_at',
                ],
                'with' => ['department'],
            ],
        ];
    }

    private function executeQuery($dataSource, $columns, $filters, $limit = 1000)
    {
        switch ($dataSource) {
            case 'tickets':
                $query = Ticket::query();
                break;
            case 'payments':
                $query = Payment::query();
                break;
            case 'patients':
                $query = Patient::query();
                break;
            case 'departments':
                $query = Department::query();
                break;
            case 'services':
                $query = Service::query();
                break;
            default:
                return [];
        }

        // Apply filters
        if (isset($filters['date_range'])) {
            $query->whereBetween('created_at', [
                $filters['date_range']['start'],
                $filters['date_range']['end'],
            ]);
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['cashier_id'])) {
            $query->where('cashier_id', $filters['cashier_id']);
        }

        // Select columns
        if (!empty($columns)) {
            $query->select($columns);
        }

        return $query->limit($limit)->get()->toArray();
    }
}
