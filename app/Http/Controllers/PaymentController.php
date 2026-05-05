<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Consultation;
use App\Models\ConsultationInstallment;
use App\Models\CaseFile;

class PaymentController extends Controller
{
    /**
     * Guardar pago
     */
    public function store(Request $request)
    {
        $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'installment_id' => 'required|exists:consultation_installments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
        ]);

        $installment = ConsultationInstallment::findOrFail($request->installment_id);

        // 🔥 VALIDAR QUE NO EXCEDA
        if ($request->amount > $installment->pending_amount) {
            return response()->json([
                'error' => 'El monto excede el saldo pendiente'
            ], 422);
        }

        // 🔥 CREAR PAGO
        $payment = Payment::create([
            'consultation_id' => $request->consultation_id,
            'consultation_installment_id' => $installment->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'generate_case' => $request->generate_case ? 1 : 0,
            'created_by' => auth()->user()->id,
        ]);

        $consultation = $installment->consultation;

        // 🔥 GENERAR CASO SOLO SI NO EXISTE
        if ($request->generate_case && !$consultation->case) {
            //$status = ($consultation->lawyer_id > 0) ? 'assigned' : config('options.default_case_status') ;
            CaseFile::create([
                'consultation_id' => $consultation->id,
                'establishment_id' => $consultation->establishment_id,
                'client_id' => $consultation->client_id,
                'service_type' => $consultation->service_type,
                'legal_specialty_id' => $consultation->legal_specialty_id,
                'legal_subject_id' => $consultation->legal_subject_id,
                'lawyer_id' => $consultation->lawyer_id,
                'title' => $consultation->title,
                'description' => $consultation->description,
                'total_amount' => $consultation->total_amount,
                'status' => config('options.default_case_status'),
                'opened_at' => now(),
                'created_by' => auth()->user()->id,
            ]);

            $consultation->update([
                'status' => 'accepted'
            ]);
        }

        return response()->json([
            'ok' => true
        ]);
    }

    /**
     * Verificar si la consulta ya tiene caso
     */
    public function checkCase(Request $request)
    {
        $installment = ConsultationInstallment::find($request->installment_id);

        if (!$installment) {
            return response()->json(['has_case' => false]);
        }

        return response()->json([
            'has_case' => $installment->consultation->case ? true : false
        ]);
    }

    /**
     * Listar pagos por cuota (opcional)
     */
    public function byInstallment($installmentId)
    {
        return Payment::where('consultation_installment_id', $installmentId)
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    public function data(Request $request)
    {
        $inst = ConsultationInstallment::with('payments', 'consultation')->findOrFail($request->installment_id);

        return response()->json([
            'installment' => [
                'installment_number' => $inst->installment_number,
                'amount' => number_format($inst->amount,2),
                'paid' => number_format($inst->paid_amount,2),
                'pending' => number_format($inst->pending_amount,2),
                'is_paid' => $inst->is_paid,
            ],
            'payments' => $inst->payments->map(function($p){
                return [
                    'id' => $p->id,
                    'amount' => number_format($p->amount,2),
                    'date' => $p->payment_date->format('d/m/Y'),
                    'method' => config('options.payment_methods')[$p->payment_method] ?? '',
                ];
            }),
            'has_case' => $inst->consultation->case ? true : false
        ]);
    }

    public function delete(Request $request)
    {
        Payment::findOrFail($request->id)->delete();

        return response()->json(['ok'=>true]);
    }

}