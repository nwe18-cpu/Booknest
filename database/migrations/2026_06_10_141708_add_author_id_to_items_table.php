<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('id')->constrained('authors')->onDelete('restrict');
        });

        // Migrate existing author string data to authors table
        $items = DB::table('items')->get();
        foreach ($items as $item) {
            if (!empty($item->author)) {
                $authorId = DB::table('authors')->where('name', $item->author)->value('id');
                if (!$authorId) {
                    $authorId = DB::table('authors')->insertGetId([
                        'name' => $item->author,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                DB::table('items')->where('id', $item->id)->update(['author_id' => $authorId]);
            }
        }

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('author')->nullable()->after('name');
        });

        $items = DB::table('items')->get();
        foreach ($items as $item) {
            if ($item->author_id) {
                $authorName = DB::table('authors')->where('id', $item->author_id)->value('name');
                DB::table('items')->where('id', $item->id)->update(['author' => $authorName]);
            }
        }

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');
        });
    }
};
