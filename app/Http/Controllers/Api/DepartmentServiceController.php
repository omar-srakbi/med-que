<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class DepartmentServiceController extends Controller
{
    public function index($departmentId)
    {
        $services = Service::where('department_id', $departmentId)
            ->where('is_active', true)
            ->get(['id', 'name', 'name_ar', 'price', 'shortcut']);
        
        return response()->json($services);
    }
}
