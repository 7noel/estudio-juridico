<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([

            'case_id' => 'nullable|exists:cases,id',

            'category' => 'required|string',

            'amount' => 'required|numeric|min:0',

            'expense_date' => 'required|date',

            'payment_method' => 'nullable|string|max:100',

            'reference' => 'nullable|string|max:255',

            'description' => 'nullable|string',

            'attachment' =>
                'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',

        ]);

        DB::beginTransaction();

        try {

            $case = null;

            if($request->case_id){

                $case = CaseFile::findOrFail($request->case_id);

                $this->authorize('update', $case);
            }

            $attachment = null;

            if($request->hasFile('attachment')){

                $attachment =
                    $request->file('attachment')
                        ->store('expenses', 'public');
            }

            $expense = Expense::create([

                'establishment_id' =>
                    $case?->establishment_id,

                'case_id' => $request->case_id,

                'user_id' => auth()->id(),

                'category' => $request->category,

                'amount' => $request->amount,

                'expense_date' => $request->expense_date,

                'payment_method' => $request->payment_method,

                'reference' => $request->reference,

                'description' => $request->description,

                'attachment' => $attachment,

            ]);

            DB::commit();

            return response()->json([

                'success' => true,

                'message' => 'Gasto registrado correctamente.',

                'expense' => $expense->load('user'),

            ]);

        } catch (\Exception $e){

            DB::rollBack();

            throw $e;
        }
    }

    public function update(Request $request, Expense $expense)
    {
        if($expense->case){

            $this->authorize('update', $expense->case);
        }

        $request->validate([

            'category' => 'required|string',

            'amount' => 'required|numeric|min:0',

            'expense_date' => 'required|date',

            'payment_method' => 'nullable|string|max:100',

            'reference' => 'nullable|string|max:255',

            'description' => 'nullable|string',

            'attachment' =>
                'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',

        ]);

        DB::beginTransaction();

        try {

            $attachment = $expense->attachment;

            if($request->hasFile('attachment')){

                if($expense->attachment){

                    Storage::disk('public')
                        ->delete($expense->attachment);
                }

                $attachment =
                    $request->file('attachment')
                        ->store('expenses', 'public');
            }

            $expense->update([

                'category' => $request->category,

                'amount' => $request->amount,

                'expense_date' => $request->expense_date,

                'payment_method' => $request->payment_method,

                'reference' => $request->reference,

                'description' => $request->description,

                'attachment' => $attachment,

            ]);

            DB::commit();

            return response()->json([

                'success' => true,

                'message' => 'Gasto actualizado correctamente.',

            ]);

        } catch (\Exception $e){

            DB::rollBack();

            throw $e;
        }
    }

    public function destroy(Expense $expense)
    {
        if($expense->case){

            $this->authorize('update', $expense->case);
        }

        DB::beginTransaction();

        try {

            if($expense->attachment){

                Storage::disk('public')
                    ->delete($expense->attachment);
            }

            $expense->delete();

            DB::commit();

            return response()->json([

                'success' => true,

                'message' => 'Gasto eliminado correctamente.',

            ]);

        } catch (\Exception $e){

            DB::rollBack();

            throw $e;
        }
    }
}

