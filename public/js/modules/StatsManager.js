import { DateFormatter } from './DateFormatter.js';
import probabilityManager from './ProbabilityManager.js';

export class StatsManager {
    constructor() {
        this.cards = document.querySelectorAll('.card');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Add click handler for probability card
        const probabilityCard = this.cards[3];
        if (probabilityCard) {
            probabilityCard.addEventListener('click', () => this.showProbabilityInfo());
        }
    }

    showProbabilityInfo() {
        // Get the current values from the cards
        const currentKm = parseFloat(this.cards[0].querySelector('.card-text').textContent);
        const estimatedKm = parseFloat(this.cards[1].querySelector('.card-text').textContent);
        const targetKm = parseFloat(this.cards[3].querySelector('.card-subtext').textContent.match(/\d+(\.\d+)?/)[0]);
        const probability = parseFloat(this.cards[3].querySelector('.card-text').textContent);

        // Calculate percentages
        const currentProgress = (currentKm / targetKm) * 100;
        const estimatedProgress = (estimatedKm / targetKm) * 100;

        // Update modal values using ProbabilityManager
        probabilityManager.updateProbabilityInfo(
            currentKm,
            targetKm,
            estimatedKm,
            currentProgress,
            estimatedProgress,
            probability
        );

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('probabilityInfoModal'));
        modal.show();
    }

    async updateStats(session, runs) {
        // Calculate stats
        const totalKilometers = runs.reduce((sum, run) => sum + parseFloat(run.kilometers), 0);
        const startDate = new Date(session.start_date);
        const endDate = new Date(session.end_date);
        const now = new Date();

        // Calculate days since start (for average)
        const daysSinceStart = Math.ceil((Math.min(now, endDate) - startDate) / (1000 * 60 * 60 * 24)) + 1;
        
        // Calculate total session days (for estimated total)
        const totalSessionDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        
        // Calculate daily average based on days since start
        const averageKilometers = daysSinceStart > 0 ? totalKilometers / daysSinceStart : 0;
        
        // Calculate estimated total based on current average
        const estimatedTotal = averageKilometers * totalSessionDays;
        
        // Calculate remaining days
        const remainingDays = Math.max(0, Math.ceil((endDate - now) / (1000 * 60 * 60 * 24)));
        
        // Calculate probability based on estimated total and current progress
        const currentProgress = (totalKilometers / session.target_kilometers) * 100;
        const estimatedProgress = (estimatedTotal / session.target_kilometers) * 100;
        
        // If we're ahead of schedule (estimated > target), probability should be high
        // If we're behind schedule but catching up (current < target but estimated close to target), probability should be moderate
        // If we're far behind (both current and estimated far from target), probability should be low
        let probability;
        if (estimatedTotal >= session.target_kilometers) {
            // If we're projected to exceed target, probability is very high
            probability = Math.min(100, 90 + (currentProgress / 10));
        } else {
            // Base probability on estimated progress, but weight it by current progress
            const progressRatio = estimatedProgress / 100;
            const currentWeight = 0.3; // 30% weight to current progress
            const estimatedWeight = 0.7; // 70% weight to estimated progress
            
            probability = Math.min(100,
                (currentProgress * currentWeight) + 
                (estimatedProgress * estimatedWeight)
            );
        }

        // Update stats in UI
        this.cards[0].querySelector('.card-text').textContent = `${totalKilometers.toFixed(1)} km`;
        this.cards[0].querySelector('.card-subtext').textContent = `since ${DateFormatter.formatDateForDisplay(startDate)}`;
        this.cards[1].querySelector('.card-subtext').textContent = `daily average of ${averageKilometers.toFixed(1)} km`;
        this.cards[1].querySelector('.card-text').textContent = `${estimatedTotal.toFixed(1)} km`;
        this.cards[2].querySelector('.card-text').textContent = `${remainingDays} days`;
        this.cards[2].querySelector('.card-subtext').textContent = `until ${DateFormatter.formatDateForDisplay(endDate)}`;
        this.cards[3].querySelector('.card-subtext').textContent = `Target of ${session.target_kilometers.toFixed(1)} km`;
        this.cards[3].querySelector('.card-text').textContent = `${probability.toFixed(1)}%`;
    }

    clearStats() {
        const emptySession = { 
            target_kilometers: 0, 
            start_date: new Date(), 
            end_date: new Date() 
        };
        this.updateStats(emptySession, []);
    }

    calculateSessionStats(session, runs) {
        const totalKilometers = runs.reduce((sum, run) => sum + parseFloat(run.kilometers), 0);
        const startDate = new Date(session.start_date);
        const endDate = new Date(session.end_date);
        const now = new Date();
        
        // Calculate days since start (for average)
        const daysSinceStart = Math.ceil((Math.min(now, endDate) - startDate) / (1000 * 60 * 60 * 24)) + 1;
        const dailyAverage = daysSinceStart > 0 ? totalKilometers / daysSinceStart : 0;

        return {
            totalKilometers,
            dailyAverage,
            daysSinceStart
        };
    }
}
