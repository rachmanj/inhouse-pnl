<?php

namespace App\Actions\Reports;

use App\Models\ApprovalStep;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RejectApprovalStepAction
{
    public function execute(ApprovalStep $step, User $actor, string $comments): void
    {
        if (trim($comments) === '') {
            throw new InvalidArgumentException('Rejection comments are required.');
        }

        DB::transaction(function () use ($step, $actor, $comments) {
            $step->update([
                'status' => 'rejected',
                'acted_by' => $actor->id,
                'acted_at' => now(),
                'comments' => $comments,
            ]);

            $step->reportPackage->update(['status' => 'draft']);
        });
    }
}
