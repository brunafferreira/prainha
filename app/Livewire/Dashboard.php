<?php

namespace App\Livewire;

use App\Models\BeachClass;
use App\Models\CourtReservation;
use App\Models\MonthlyFee;
use App\Models\Student;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public bool $sidebarOpen = false;

    public function render()
    {
        return view('livewire.dashboard', [
            'studentCount' => Student::where('active', true)->count(),
            'classCount' => BeachClass::where('active', true)->count(),
            'pendingCount' => MonthlyFee::where('status', 'pending')->count(),
            'pendingAmount' => MonthlyFee::where('status', 'pending')->sum('amount'),
            'recentStudents' => Student::with('beachClass')->latest()->limit(5)->get(),
            'upcomingReservationCount' => CourtReservation::whereDate('reservation_date', '>=', now()->toDateString())->count(),
            'upcomingReservations' => CourtReservation::whereDate('reservation_date', '>=', now()->toDateString())
                ->orderBy('reservation_date')
                ->orderBy('starts_at')
                ->limit(5)
                ->get(),
            'monthLabel' => Carbon::now()->locale('pt_BR')->translatedFormat('F Y'),
        ]);
    }
}
