<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signature;
use App\Models\SignatureVerification;
use App\Models\Letter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    /**
     * Show signature pad for signing a document
     */
    public function pad($signable, $id)
    {
        // Find the signable model
        $model = $this->findSignableModel($signable, $id);
        if (!$model) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        return view('signatures.pad', compact('model', 'signable', 'id'));
    }

    /**
     * Store a new signature
     */
    public function store(Request $request, $signable, $id)
    {
        $request->validate([
            'signature_data' => 'required|string',
            'signature_reason' => 'nullable|string|max:255',
        ]);

        $model = $this->findSignableModel($signable, $id);
        if (!$model) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        // Generate signature hash
        $signatureHash = Signature::generateSignatureHash(
            $request->signature_data,
            Auth::id(),
            $model->id
        );

        // Store signature
        $signature = Signature::create([
            'user_id' => Auth::id(),
            'signable_id' => $model->id,
            'signable_type' => get_class($model),
            'signature_image' => $request->signature_data,
            'signature_hash' => $signatureHash,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'verification_token' => Signature::generateVerificationToken(),
        ]);

        return redirect()->route('signatures.list', ['signable' => $signable, 'id' => $id])
            ->with('success', 'Signature added successfully.');
    }

    /**
     * List all signatures for a document
     */
    public function list($signable, $id)
    {
        $model = $this->findSignableModel($signable, $id);
        if (!$model) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $signatures = $model->signatures()->with('signer', 'verifications')->get();

        return view('signatures.list', compact('model', 'signatures', 'signable', 'id'));
    }

    /**
     * View verification logs
     */
    public function logs()
    {
        $user = Auth::user();
        $query = Signature::with('signer', 'signable', 'verifications');

        // HR/Power User see all signatures
        if ($user->employee && !in_array($user->employee->role->title, ['HR', 'Power User'])) {
            $query->where('user_id', $user->id);
        }

        $signatures = $query->latest()->paginate(20);

        return view('signatures.logs', compact('signatures'));
    }

    /**
     * Verify a signature (HR/Power User only)
     */
    public function verify(Request $request, Signature $signature)
    {
        $user = Auth::user();
        if (!$user->employee || !in_array($user->employee->role->title, ['HR', 'Power User'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:verified,rejected',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($request->status === 'verified') {
            $signature->verify(Auth::id(), $request->remarks);
            $message = 'Signature verified successfully.';
        } else {
            $signature->reject(Auth::id(), $request->remarks);
            $message = 'Signature rejected.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Download signed document as PDF
     */
    public function download(Signature $signature)
    {
        // Authorization check
        $user = Auth::user();
        if ($signature->user_id !== $user->id && ($user->employee && !in_array($user->employee->role->title, ['HR', 'Power User']))) {
            abort(403, 'Unauthorized action.');
        }

        // Eager load relationships for PDF rendering
        $signature->load('signer', 'verifications.verifier');

        // Get the signable model
        $model = $signature->signable;

        if ($model instanceof Letter) {
            // Generate PDF with signature
            $html = view('signatures.signed-letter-pdf', ['letter' => $model, 'signature' => $signature])->render();
            
            // Load PDF options to disable image processing if GD is not available
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('chroot', public_path());
            
            $pdf = \PDF::loadHTML($html);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);

            $filename = 'Surat_Tertandatangan_' . str_replace('/', '_', $model->letter_number ?? 'Draft') . '.pdf';
            return $pdf->download($filename);
        }

        return redirect()->back()->with('error', 'Cannot download this document type.');
    }

    /**
     * Validate/verify signature authenticity
     */
    public function validate(Signature $signature)
    {
        if ($signature->isValid()) {
            return response()->json([
                'valid' => true,
                'message' => 'Signature is authentic and has not been tampered with.',
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Signature validation failed. Document may have been tampered with.',
        ], 422);
    }

    /**
     * Find signable model based on type and ID
     */
    private function findSignableModel($signable, $id)
    {
        switch ($signable) {
            case 'letter':
                return Letter::find($id);
            default:
                return null;
        }
    }
}
