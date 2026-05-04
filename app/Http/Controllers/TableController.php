<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Sale;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $outletId = session('outlet_id');
        
        $tables = Table::where('outlet_id', $outletId)
            ->when($request->filled('area'), function ($q) use ($request) {
                $q->where('area', $request->area);
            })
            ->orderBy('sort_order')
            ->get();

        return view('tables.index', compact('tables'));
    }

    public function updateStatus(Request $request, Table $table)
    {
        $this->authorize('update', $table);

        $validated = $request->validate([
            'status' => 'required|in:empty,occupied,reserved,requesting_bill',
        ]);

        $table->update($validated);

        // If marking as occupied, associate with sale if provided
        if ($request->has('sale_id')) {
            $sale = Sale::find($request->sale_id);
            if ($sale) {
                $sale->update(['table_id' => $table->id]);
                $table->update(['current_sale_id' => $sale->id]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $table,
        ]);
    }

    public function merge(Request $request)
    {
        $request->validate([
            'table_ids' => 'required|array|min:2',
            'table_ids.*' => 'exists:tables,id',
            'target_table_id' => 'required|exists:tables,id',
        ]);

        $targetTable = Table::find($request->target_table_id);
        $this->authorize('update', $targetTable);

        $tableIds = $request->table_ids;
        unset($tableIds[array_search($request->target_table_id, $tableIds)]);

        DB::transaction(function () use ($tableIds, $targetTable) {
            foreach ($tableIds as $tableId) {
                $sourceTable = Table::find($tableId);
                
                // Move sale to target table
                if ($sourceTable->current_sale_id) {
                    Sale::where('id', $sourceTable->current_sale_id)
                        ->update(['table_id' => $targetTable->id]);
                }

                // Update status and clear current sale
                $sourceTable->update([
                    'status' => 'empty',
                    'current_sale_id' => null,
                ]);
            }

            // Update target table status
            if ($targetTable->current_sale_id) {
                $targetTable->update(['status' => 'occupied']);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Meja berhasil digabung',
        ]);
    }

    public function move(Table $table, $target, Request $request)
    {
        $targetTable = Table::findOrFail($target);
        $this->authorize('update', $table);

        if ($table->current_sale_id != $targetTable->current_sale_id) {
            throw new \Exception("Meja sumber dan target harus memiliki transaksi yang sama");
        }

        $table->update([
            'status' => 'empty',
            'current_sale_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dipindah',
        ]);
    }
}
