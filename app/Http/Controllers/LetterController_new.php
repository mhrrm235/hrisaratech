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
        
        // Regular users see only their letters, HR sees all
        if ($user->employee && $user->employee->role->name !== 'HR') {
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
        $this->authorize('view', $letter);
        return view('letters.show', compact('letter'));
    }

    public function edit(Letter $letter)
    {
        $this->authorize('update', $letter);
        
        if ($letter->status !== 'draft') {
            return redirect()->route('letters.index')->with('error', 'Only draft letters can be edited.');
        }
        
        $templates = LetterTemplate::where('is_active', true)->get();
        return view('letters.edit', compact('letter', 'templates'));
    }

    public function update(Request $request, Letter $letter)
    {
        $this->authorize('update', $letter);
        
        if ($letter->status !== 'draft') {
            return redirect()->route('letters.index')->with('error', 'Only draft letters can be edited.');
        }
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'letter_type' => 'required|in:official,memo,notice',
            'letter_template_id' => 'nullable|exists:letter_templates,id',
        ]);

        $letter->update($request->all());

        return redirect()->route('letters.index')->with('success', 'Letter updated successfully.');
    }

    public function destroy(Letter $letter)
    {
        $this->authorize('delete', $letter);
        
        if ($letter->status !== 'draft') {
            return redirect()->route('letters.index')->with('error', 'Only draft letters can be deleted.');
        }
        
        $letter->delete();
        return redirect()->route('letters.index')->with('success', 'Letter deleted successfully.');
    }

    public function submit(Letter $letter)
    {
        $this->authorize('update', $letter);
        
        if ($letter->status !== 'draft') {
            return redirect()->route('letters.index')->with('error', 'Letter cannot be submitted.');
        }

        // Generate letter number
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
        $this->authorize('approve', $letter);
        
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
        $this->authorize('approve', $letter);
        
        $request->validate(['reason' => 'required|string']);

        $letter->update([
            'status' => 'draft',
            'notes' => $request->reason,
        ]);

        return redirect()->route('letters.show', $letter)->with('success', 'Letter rejected and returned to draft.');
    }

    public function print(Letter $letter)
    {
        $this->authorize('print', $letter);
        
        if ($letter->status !== 'approved') {
            return redirect()->route('letters.show', $letter)->with('error', 'Only approved letters can be printed.');
        }

        $letter->update([
            'status' => 'printed',
            'printed_date' => now(),
        ]);

        // Generate PDF
        $pdf = \PDF::loadView('letters.pdf', compact('letter'));
        return $pdf->download('letter-' . $letter->letter_number . '.pdf');
    }

    private function generateLetterNumber(LetterConfiguration $config)
    {
        $format = $config->letter_number_format;
        $number = str_pad($config->current_number, 3, '0', STR_PAD_LEFT);
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $year = now()->year;
        $dept = 'HR'; // Can be customized based on user's department

        return str_replace(
            ['{NUMBER}', '{DEPT}', '{MONTH}', '{YEAR}'],
            [$number, $dept, $month, $year],
            $format
        );
    }
}
