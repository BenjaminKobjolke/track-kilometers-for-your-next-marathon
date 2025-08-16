import translationManager from './TranslationManager.js';

export class ProbabilityManager {
    constructor() {
        this.currentProgressText = document.getElementById('currentProgressText');
        this.estimatedProgressText = document.getElementById('estimatedProgressText');
        this.finalProbabilityText = document.getElementById('finalProbabilityText');
    }

    updateProbabilityInfo(currentKm, targetKm, estimatedKm, currentPercent, estimatedPercent, finalPercent) {
        // Update current progress text
        this.currentProgressText.innerHTML = translationManager.translate('probability_current_progress_text', {
            percent: currentPercent.toFixed(1),
            current: currentKm.toFixed(1),
            target: targetKm.toFixed(1)
        });

        // Update estimated progress text
        this.estimatedProgressText.innerHTML = translationManager.translate('probability_estimated_progress_text', {
            percent: estimatedPercent.toFixed(1),
            estimated: estimatedKm.toFixed(1)
        });

        // Update final probability text
        this.finalProbabilityText.innerHTML = translationManager.translate('probability_final', {
            percent: finalPercent.toFixed(1)
        });
    }
}

// Create a singleton instance
const probabilityManager = new ProbabilityManager();
export default probabilityManager;
