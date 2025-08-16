<?php

namespace Controllers;

use Models\Run;

class StatsController {
    public function calculateStats($session) {
        if (!$session || $session->status !== 'active') {
            return [
                'runs' => [],
                'totalKilometers' => 0,
                'averageKilometers' => 0,
                'estimatedTotal' => 0,
                'remainingDays' => 0,
                'probability' => 0
            ];
        }

        $runs = Run::where('session_id', $session->id)
            ->orderBy('date', 'desc')
            ->get();
        
        $totalKilometers = $runs->sum('kilometers');
        $averageKilometers = Run::calculateAverageKilometers($session->start_date, $session->end_date, $session->id);
        
        // Calculate estimated total based on current average
        $totalDays = ceil((strtotime($session->end_date) - strtotime($session->start_date)) / (60 * 60 * 24)) + 1;
        $estimatedTotal = $averageKilometers * $totalDays;
        
        // Calculate remaining days
        $remainingDays = ceil((strtotime($session->end_date) - time()) / (60 * 60 * 24));
        $remainingDays = max(0, $remainingDays);
        
        // Calculate probability based on estimated total and current progress
        $currentProgress = ($totalKilometers / $session->target_kilometers) * 100;
        $estimatedProgress = ($estimatedTotal / $session->target_kilometers) * 100;
        
        // Calculate probability
        $probability = $this->calculateProbability($estimatedTotal, $currentProgress, $session->target_kilometers);

        return [
            'runs' => $runs,
            'totalKilometers' => $totalKilometers,
            'averageKilometers' => $averageKilometers,
            'estimatedTotal' => $estimatedTotal,
            'remainingDays' => $remainingDays,
            'probability' => $probability
        ];
    }

    private function calculateProbability($estimatedTotal, $currentProgress, $targetKilometers) {
        if ($estimatedTotal >= $targetKilometers) {
            // If we're projected to exceed target, probability is very high
            return min(100, 90 + ($currentProgress / 10));
        }

        // Base probability on estimated progress, but weight it by current progress
        $estimatedProgress = ($estimatedTotal / $targetKilometers) * 100;
        $currentWeight = 0.3; // 30% weight to current progress
        $estimatedWeight = 0.7; // 70% weight to estimated progress
        
        return min(100,
            ($currentProgress * $currentWeight) + 
            ($estimatedProgress * $estimatedWeight)
        );
    }
}
