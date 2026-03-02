<?php

use App\Enums\TravelRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->string('requester_name');
            $table->foreignId('requester_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('destination');
            $table->date('departure_date');
            $table->date('return_date');
            $table->enum('status', TravelRequestStatus::values())->default(TravelRequestStatus::Requested->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_requests');
    }
};
