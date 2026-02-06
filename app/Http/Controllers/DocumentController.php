<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::where('user_id', Auth::id())->latest()->get();

        return view('vinify.documents.index', compact('documents'));
    }
}
