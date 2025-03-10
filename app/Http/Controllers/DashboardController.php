<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()->latest()->get();
        return view('dashboard', compact('projects'));
    }
} 