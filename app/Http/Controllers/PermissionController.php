<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function create()
    {
    // if (!auth()->user()->hasRole('super_admin')) {
    //     abort(403, 'Unauthorized action.');
    // }
    // return view('permission.create');

        if (!auth()->user()->can('create program') || !auth()->user()->hasAnyRole(['admin', 'perangkat_daerah'])) {
            abort(403, 'Unauthorized action.');
        }
        return view('permission.create');
    }
}
