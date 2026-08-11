<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE member_documents MODIFY document_type ENUM(
            'identity_card_front',
            'identity_card_back',
            'diploma',
            'member_photo',
            'cv',
            'problematic_justification',
            'other'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE member_documents SET document_type = 'other' WHERE document_type = 'cv'");

        DB::statement("ALTER TABLE member_documents MODIFY document_type ENUM(
            'identity_card_front',
            'identity_card_back',
            'diploma',
            'member_photo',
            'problematic_justification',
            'other'
        )");
    }
};
