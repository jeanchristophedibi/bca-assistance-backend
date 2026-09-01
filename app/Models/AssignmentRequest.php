<?php

namespace App\Models;

use App\Filters\AssignmentRequestFilters;
use App\Builders\AssignmentRequest\AssignmentRequestBuilder;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;


class AssignmentRequest extends Model
{
    use HasFactory, Filterable, HasHashId, HasHashIdRouting;

    protected string $default_filters = AssignmentRequestFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the assignments for this assignment request
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the user who created this assignment request
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who cancelled this assignment request
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Get the user who rejected this assignment request
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the user who last updated this assignment request
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted this assignment request
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get the status of this assignment request
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get all repairers for this assignment request
     */
    public function repairers(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class, 'assignment_request_repairers', 'assignment_request_id', 'repairer_id')
            ->withTimestamps()
            ->withPivot(['id', 'status_id', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'])
            ->wherePivotNull('deleted_at');
    }

    /**
     * Pivot rows linking repairers to this assignment request
     */
    public function assignmentRequestRepairers(): HasMany
    {
        return $this->hasMany(AssignmentRequestRepairer::class);
    }

    /**
     * First linked repairer (compat for legacy single-repairer usage).
     */
    public function getRepairerAttribute()
    {
        if ($this->relationLoaded('repairers')) {
            return $this->repairers->first();
        }

        return $this->repairers()->first();
    }

    /**
     * Get the client for this assignment request
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the vehicle for this assignment request
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the photos for this assignment request
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function newEloquentBuilder($query): AssignmentRequestBuilder
    {
        return new AssignmentRequestBuilder($query);
    }

    /**
     * Replace linked repairers from a request payload.
     *
     * @param  array<int, array{repairer_id: int|string}>|null  $repairers
     */
    public function syncRepairers(?array $repairers, ?int $userId = null): void
    {
        if ($repairers === null) {
            return;
        }

        AssignmentRequestRepairer::where('assignment_request_id', $this->id)->forceDelete();

        foreach ($repairers as $data) {
            if (empty($data['repairer_id'])) {
                continue;
            }

            AssignmentRequestRepairer::create([
                'assignment_request_id' => $this->id,
                'repairer_id' => $data['repairer_id'],
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        }
    }

}
