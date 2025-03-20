<?php

namespace App\Services;

use App\Models\Criteria;
use App\Models\Smartphone;
use Illuminate\Support\Collection;

class TopsisService
{
    /**
     * Mendapatkan rekomendasi smartphone berdasarkan budget dan kriteria
     *
     * @param int $minBudget
     * @param int $maxBudget
     * @param array $criteriaWeights
     * @return Collection
     */
    public function getRecommendation(int $minBudget, int $maxBudget, array $criteriaWeights): Collection
    {
        // Ambil smartphone yang sesuai dengan budget
        $smartphones = Smartphone::whereBetween('price', [$minBudget, $maxBudget])->get();

        if ($smartphones->isEmpty()) {
            return collect([]);
        }

        // Ambil kriteria dari database dan update bobot berdasarkan input user
        $criteria = Criteria::all();

        // Update criteria weight dari input user jika ada
        foreach ($criteria as $c) {
            if (isset($criteriaWeights[$c->code])) {
                $c->weight = $criteriaWeights[$c->code];
            }
        }

        // Buat decision matrix
        $matrix = $this->createDecisionMatrix($smartphones, $criteria);

        // Normalisasi matrix
        $normalizedMatrix = $this->normalizeMatrix($matrix);

        // Hitung weighted normalized matrix
        $weightedMatrix = $this->calculateWeightedMatrix($normalizedMatrix, $criteria);

        // Tentukan solusi ideal positif dan negatif
        list($positiveIdeal, $negativeIdeal) = $this->findIdealSolutions($weightedMatrix, $criteria);

        // Hitung jarak ke solusi ideal
        $distances = $this->calculateDistances($weightedMatrix, $positiveIdeal, $negativeIdeal);

        // Hitung closeness coefficient
        $results = $this->calculateClosenessCoefficient($smartphones, $distances);

        // Urutkan hasil berdasarkan closeness coefficient (tertinggi = terbaik)
        return $results->sortByDesc('score');
    }

    /**
     * Membuat decision matrix dari data smartphone
     */
    private function createDecisionMatrix(Collection $smartphones, Collection $criteria): array
    {
        $matrix = [];

        foreach ($smartphones as $index => $smartphone) {
            $scores = $smartphone->getCriteriaScores();

            foreach ($criteria as $c) {
                $matrix[$index][$c->code] = $scores[$c->code] ?? 0;
            }
        }

        return $matrix;
    }

    /**
     * Normalisasi matrix
     */
    private function normalizeMatrix(array $matrix): array
    {
        $normalizedMatrix = [];
        $squareSums = [];

        // Hitung jumlah kuadrat untuk setiap kriteria
        foreach ($matrix as $row) {
            foreach ($row as $criteriaCode => $value) {
                if (!isset($squareSums[$criteriaCode])) {
                    $squareSums[$criteriaCode] = 0;
                }
                $squareSums[$criteriaCode] += pow($value, 2);
            }
        }

        // Normalisasi matrix
        foreach ($matrix as $rowIndex => $row) {
            foreach ($row as $criteriaCode => $value) {
                $normalizedMatrix[$rowIndex][$criteriaCode] = $squareSums[$criteriaCode] > 0
                    ? $value / sqrt($squareSums[$criteriaCode])
                    : 0;
            }
        }

        return $normalizedMatrix;
    }

    /**
     * Hitung weighted normalized matrix
     */
    private function calculateWeightedMatrix(array $normalizedMatrix, Collection $criteria): array
    {
        $weightedMatrix = [];

        foreach ($normalizedMatrix as $rowIndex => $row) {
            foreach ($row as $criteriaCode => $value) {
                $weight = $criteria->firstWhere('code', $criteriaCode)->weight ?? 1;
                $weightedMatrix[$rowIndex][$criteriaCode] = $value * $weight;
            }
        }

        return $weightedMatrix;
    }

    /**
     * Tentukan solusi ideal positif dan negatif
     */
    private function findIdealSolutions(array $weightedMatrix, Collection $criteria): array
    {
        $positiveIdeal = [];
        $negativeIdeal = [];

        foreach ($criteria as $c) {
            $values = array_column($weightedMatrix, $c->code);

            if ($c->type === 'benefit') {
                $positiveIdeal[$c->code] = max($values);
                $negativeIdeal[$c->code] = min($values);
            } else { // 'cost'
                $positiveIdeal[$c->code] = min($values);
                $negativeIdeal[$c->code] = max($values);
            }
        }

        return [$positiveIdeal, $negativeIdeal];
    }

    /**
     * Hitung jarak ke solusi ideal
     */
    private function calculateDistances(array $weightedMatrix, array $positiveIdeal, array $negativeIdeal): array
    {
        $distances = [];

        foreach ($weightedMatrix as $rowIndex => $row) {
            $positiveDistance = 0;
            $negativeDistance = 0;

            foreach ($row as $criteriaCode => $value) {
                $positiveDistance += pow($value - $positiveIdeal[$criteriaCode], 2);
                $negativeDistance += pow($value - $negativeIdeal[$criteriaCode], 2);
            }

            $distances[$rowIndex] = [
                'positive' => sqrt($positiveDistance),
                'negative' => sqrt($negativeDistance),
            ];
        }

        return $distances;
    }

    /**
     * Hitung closeness coefficient
     */
    private function calculateClosenessCoefficient(Collection $smartphones, array $distances): Collection
    {
        $results = collect();

        foreach ($smartphones as $index => $smartphone) {
            if (!isset($distances[$index])) {
                continue;
            }

            $positiveDistance = $distances[$index]['positive'];
            $negativeDistance = $distances[$index]['negative'];

            // Hitung Closeness Coefficient (CC)
            $score = $positiveDistance + $negativeDistance > 0
                ? $negativeDistance / ($positiveDistance + $negativeDistance)
                : 0;

            $results->push([
                'smartphone' => $smartphone,
                'score' => $score,
            ]);
        }

        return $results;
    }
}