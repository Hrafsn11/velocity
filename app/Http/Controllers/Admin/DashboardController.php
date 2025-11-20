<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'totalPermissions' => Permission::count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'userRoles' => auth()->user()->getRoleNames(),
        ];

        return view('admin.dashboard.index', $data);
    }
}
