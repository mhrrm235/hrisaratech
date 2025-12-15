<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Letter;
use App\Models\LetterTemplate;
use App\Models\LetterConfiguration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LetterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Letter::with('user', 'approver', 'template');
        
        // Only show own letters to non-HR users
        if ($user->employee && $user->employee->role->title !== 'HR' && $user->employee->role->title !== 'Power User') {
            $query->where('user_id', $user->id);
        }
        
        $letters = $query->latest()->get();
        return view('letters.index', compact('letters'));
    }

    public function create()
    {
        $templates = LetterTemplate::where('is_active', true)->get();
        return view('letters.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'letter_type' => 'required|in:official,memo,notice',
            'letter_template_id' => 'nullable|exists:letter_templates,id',
        ]);

        Letter::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'content' => $request->content,
            'letter_type' => $request->letter_type,
            'letter_template_id' => $request->letter_template_id,
            'status' => 'draft',
            'created_date' => now(),
        ]);

        return redirect()->route('letters.index')->with('success', 'Letter created successfully.');
    }

    public function show(Letter $letter)
    {
        return view('letters.show', compact('letter'));
    }

    public function edit(Letter $letter)
    {
        // Allow editing draft and pending letters
        if (!in_array($letter->status, ['draft', 'pending'])) {
            return redirect()->route('letters.index')->with('error', 'Only draft and pending letters can be edited.');
        }
        
        // Only allow user who created the letter to edit
        if ($letter->user_id !== Auth::id()) {
            return redirect()->route('letters.index')->with('error', 'You cannot edit this letter.');
        }
        
        $templates = LetterTemplate::where('is_active', true)->get();
        return view('letters.edit', compact('letter', 'templates'));
    }

    public function update(Request $request, Letter $letter)
    {
        // Allow updating draft and pending letters
        if (!in_array($letter->status, ['draft', 'pending'])) {
            return redirect()->route('letters.index')->with('error', 'Only draft and pending letters can be edited.');
        }
        
        // Only allow user who created the letter to update
        if ($letter->user_id !== Auth::id()) {
            return redirect()->route('letters.index')->with('error', 'You cannot update this letter.');
        }
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'letter_type' => 'required|in:official,memo,notice',
            'letter_template_id' => 'nullable|exists:letter_templates,id',
        ]);

        $letter->update($request->all());
        return redirect()->route('letters.show', $letter)->with('success', 'Letter updated successfully.');
    }

    public function destroy(Letter $letter)
    {
        // Only allow user who created the letter or HR/Power User to delete
        $user = Auth::user();
        if ($letter->user_id !== $user->id && ($user->employee && $user->employee->role->title !== 'HR' && $user->employee->role->title !== 'Power User')) {
            return redirect()->route('letters.index')->with('error', 'You cannot delete this letter.');
        }
        
        if ($letter->status !== 'draft') {
            return redirect()->route('letters.index')->with('error', 'Only draft letters can be deleted.');
        }
        
        $letter->delete();
        return redirect()->route('letters.index')->with('success', 'Letter deleted successfully.');
    }

    public function submit(Letter $letter)
    {
        if ($letter->status !== 'draft') {
            return redirect()->route('letters.index')->with('error', 'Letter cannot be submitted.');
        }

        $config = LetterConfiguration::first();
        if (!$config) {
            return redirect()->route('letters.index')->with('error', 'Letter configuration not set.');
        }

        $config->current_number++;
        $letterNumber = $this->generateLetterNumber($config);
        $config->save();

        $letter->update([
            'letter_number' => $letterNumber,
            'status' => 'pending',
        ]);

        return redirect()->route('letters.show', $letter)->with('success', 'Letter submitted for approval.');
    }

    public function approve(Letter $letter)
    {
        // Authorization check - only HR and Power User can approve
        $user = Auth::user();
        if (!$user->employee || ($user->employee->role->title !== 'HR' && $user->employee->role->title !== 'Power User')) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($letter->status !== 'pending') {
            return redirect()->route('letters.show', $letter)->with('error', 'Letter cannot be approved.');
        }

        $letter->update([
            'status' => 'approved',
            'approver_id' => Auth::id(),
            'approved_date' => now(),
        ]);

        return redirect()->route('letters.show', $letter)->with('success', 'Letter approved.');
    }

    public function reject(Request $request, Letter $letter)
    {
        // Authorization check - only HR and Power User can reject
        $user = Auth::user();
        if (!$user->employee || ($user->employee->role->title !== 'HR' && $user->employee->role->title !== 'Power User')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate(['reason' => 'required|string']);

        $letter->update([
            'status' => 'draft',
            'notes' => $request->reason,
        ]);

        return redirect()->route('letters.show', $letter)->with('success', 'Letter rejected.');
    }

    public function print(Letter $letter)
    {
        // Authorization check - only HR and Power User can print
        $user = Auth::user();
        if (!$user->employee || ($user->employee->role->title !== 'HR' && $user->employee->role->title !== 'Power User')) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($letter->status !== 'approved') {
            return redirect()->route('letters.show', $letter)->with('error', 'Only approved letters can be printed.');
        }
        
        $letter->update([
            'status' => 'printed',
            'printed_date' => now(),
        ]);
        
        return redirect()->route('letters.show', $letter)->with('success', 'Letter marked as printed.');
    }

    public function export(Letter $letter)
    {
        // Authorization - only approved/printed letters can be exported, by HR/Power User
        $user = Auth::user();
        if (!$user->employee || ($user->employee->role->title !== 'HR' && $user->employee->role->title !== 'Power User')) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!in_array($letter->status, ['approved', 'printed'])) {
            return redirect()->route('letters.show', $letter)->with('error', 'Only approved or printed letters can be exported.');
        }
        
        // Create HTML content for PDF
        $html = view('letters.pdf', compact('letter'))->render();
        
        // Generate PDF
        $pdf = \PDF::loadHTML($html);
        
        // Return PDF download
        $filename = 'Surat_' . str_replace('/', '_', $letter->letter_number ?? 'Draft') . '.pdf';
        return $pdf->download($filename);
    }

    private function generateLetterNumber(LetterConfiguration $config)
    {
        $format = $config->letter_number_format;
        $number = str_pad($config->current_number, 3, '0', STR_PAD_LEFT);
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $year = now()->year;
        $dept = 'HR';

        return str_replace(
            ['{NUMBER}', '{DEPT}', '{MONTH}', '{YEAR}'],
            [$number, $dept, $month, $year],
            $format
        );
    }
}
