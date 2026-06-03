<?php

namespace App\Services;

use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Models\Assessment;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AssessmentService
{
    protected AssessmentRepositoryInterface $assessmentRepository;

    public function __construct(AssessmentRepositoryInterface $assessmentRepository)
    {
        $this->assessmentRepository = $assessmentRepository;
    }

    public function getAssessmentsPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->assessmentRepository->paginate($perPage, $filters);
    }

    public function getAllAssessments(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->assessmentRepository->all($filters);
    }


    public function getAssessmentById(string $id): ?Assessment
    {
        return $this->assessmentRepository->find($id);
    }

    public function createAssessment(array $data, ?array $groupIds = null, ?array $questions = null): Assessment
    {
        return DB::transaction(function () use ($data, $groupIds, $questions) {
            $assessment = $this->assessmentRepository->create($data);

            if (!empty($groupIds)) {
                $assessment->groups()->sync($groupIds);
            }

            if (!empty($questions)) {
                $syncData = [];
                foreach ($questions as $index => $questionId) {
                    $syncData[$questionId] = ['order_no' => $index + 1];
                }
                $assessment->questions()->sync($syncData);
            }

            return $assessment->load(['groups', 'questions']);
        });
    }

    public function updateAssessment(string $id, array $data, ?array $groupIds = null, ?array $questions = null): bool
    {
        return DB::transaction(function () use ($id, $data, $groupIds, $questions) {
            $assessment = $this->assessmentRepository->find($id);
            if (!$assessment) {
                return false;
            }

            $this->assessmentRepository->update($id, $data);

            if ($groupIds !== null) {
                $assessment->groups()->sync($groupIds);
            }

            if ($questions !== null) {
                $syncData = [];
                foreach ($questions as $index => $questionId) {
                    $syncData[$questionId] = ['order_no' => $index + 1];
                }
                $assessment->questions()->sync($syncData);
            }

            return true;
        });
    }

    public function deleteAssessment(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $assessment = $this->assessmentRepository->find($id);
            if (!$assessment) {
                return false;
            }

            $assessment->groups()->detach();
            $assessment->questions()->detach();

            return $this->assessmentRepository->delete($id);
        });
    }
}
