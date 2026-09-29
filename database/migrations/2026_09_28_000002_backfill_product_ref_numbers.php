<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $products = DB::table('products')
            ->whereNull('ref_no')
            ->orderBy('id')
            ->get(['id', 'created_at']);

        $serial = 1;

        foreach ($products as $product) {
            $date = $product->created_at ? Carbon::parse($product->created_at) : now();
            $refNo = 'SB ' . $date->format('dmY') . $serial;

            while (DB::table('products')->where('ref_no', $refNo)->exists()) {
                $serial++;
                $refNo = 'SB ' . $date->format('dmY') . $serial;
            }

            DB::table('products')
                ->where('id', $product->id)
                ->update(['ref_no' => $refNo]);

            $serial++;
        }
    }

    public function down(): void
    {
        DB::table('products')
            ->where('ref_no', 'like', 'SB %')
            ->update(['ref_no' => null]);
    }
};
