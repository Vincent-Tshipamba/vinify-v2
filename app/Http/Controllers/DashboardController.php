<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\TextAnalysis;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $users = count(User::all());
        $nbrAnalyses = count(TextAnalysis::all());
        $nbrCriticalPlagiarism = TextAnalysis::where('plagiarism_percentage', '>', 15)->count();

        $percentageCriticalPlagiarism = $nbrAnalyses > 0 ? (int) ($nbrCriticalPlagiarism / $nbrAnalyses * 100) : 0;
        $nbrDocuments = count(Document::all());

        return view('dashboard', compact('users', 'nbrAnalyses', 'nbrDocuments', 'nbrCriticalPlagiarism', 'percentageCriticalPlagiarism'));
    }
}
