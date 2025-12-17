<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LibraryStatisticsService;
use Illuminate\View\View;

class HomeController extends Controller  
{
    public function __construct(
        private readonly LibraryStatisticsService $libraryStats
    ) {}

    public function index(): View
    {
        $data = [
            'title' => 'Quản Lý Thư Viện',
            'titleWeb' => 'Quản Lý Thư Viện',
            ...$this->libraryStats->getStatistics()
        ];
        return view('home', $data);
    }
}
