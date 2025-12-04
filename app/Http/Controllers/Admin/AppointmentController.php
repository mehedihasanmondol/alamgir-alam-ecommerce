<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Admin Appointment Controller
 * 
 * Handles admin panel appointment management views
 * 
 * @category Controllers
 * @package  Admin
 * @created  2025-11-26
 */
class AppointmentController extends Controller
{
    /**
     * Display appointment management page
     */
    public function index()
    {
        return view('admin.appointments.index');
    }
}
