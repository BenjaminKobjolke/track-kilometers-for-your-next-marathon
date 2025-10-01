import { DateFormatter } from './DateFormatter.js';
import probabilityManager from './ProbabilityManager.js';
import translationManager from './TranslationManager.js';
import numberFormatter from './NumberFormatter.js';

export class StatsManager {
    constructor() {
        this.cards = document.querySelectorAll('.card');
        this.initializeEventListeners();
    }


    initializeEventListeners() {
        // Add click handler for probability card (identified by class)
        const probabilityCard = document.querySelector('.probability-card');
        if (probabilityCard) {
            probabilityCard.addEventListener('click', () => this.showProbabilityInfo());
        }
    }

    showProbabilityInfo() {
        // Get the current values from the cards using IDs for accuracy
        const currentKm = parseFloat(document.getElementById('total-amount').textContent);
        const estimatedKm = parseFloat(document.getElementById('estimated-total').textContent);
        const targetKm = parseFloat(document.getElementById('target-info').textContent.match(/\d+(\.\d+)?/)[0]);
        const probability = parseFloat(document.getElementById('target-probability').textContent);

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
        const totalKilometers = runs.reduce((sum, run) => sum + parseFloat(run.amount), 0);
        const startDate = new Date(session.start_date);
        const endDate = new Date(session.end_date);
        const now = new Date();

        // Calculate total session days
        const totalSessionDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;

        // Calculate elapsed days for daily average
        let elapsedDaysForAverage;
        if (now < startDate) {
            elapsedDaysForAverage = 0;
        } else if (now > endDate) {
            elapsedDaysForAverage = totalSessionDays;
        } else {
            elapsedDaysForAverage = Math.ceil((now - startDate) / (1000 * 60 * 60 * 24)) + 1;
        }

        // Calculate daily average based on elapsed days (not total session days)
        const averageKilometers = elapsedDaysForAverage > 0 ? totalKilometers / elapsedDaysForAverage : 0;

        // Calculate estimated total based on current average
        const estimatedTotal = averageKilometers * totalSessionDays;
        
        // Calculate remaining days
        let remainingDays;
        if (now < startDate) {
            // Session hasn't started yet - show total session duration
            remainingDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
        } else {
            // Session is active or past - show remaining days from now
            remainingDays = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));
        }
        remainingDays = Math.max(0, remainingDays);

        // Calculate training period stats
        const trainingDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        let elapsedDays;
        if (now < startDate) {
            elapsedDays = 0;
        } else if (now > endDate) {
            elapsedDays = trainingDays;
        } else {
            elapsedDays = Math.ceil((now - startDate) / (1000 * 60 * 60 * 24)) + 1;
        }
        
        // Calculate probability based on estimated total and current progress
        const currentProgress = session.target_kilometers > 0 ? (totalKilometers / session.target_kilometers) * 100 : 0;
        const estimatedProgress = session.target_kilometers > 0 ? (estimatedTotal / session.target_kilometers) * 100 : 0;
        
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

        // Update stats in UI with NaN protection using specific IDs
        const unitShort = session.unit_short || 'units';
        
        // Use specific IDs for targeted updates
        const totalAmountEl = document.getElementById('total-amount');
        const dailyAverageEl = document.getElementById('daily-average');
        const estimatedTotalEl = document.getElementById('estimated-total');
        const targetInfoEl = document.getElementById('target-info');
        const targetProbabilityEl = document.getElementById('target-probability');
        const dateRangeEl = document.getElementById('date-range');
        const dayProgressEl = document.getElementById('day-progress');
        const remainingInfoEl = document.getElementById('remaining-info');
        
        if (totalAmountEl) totalAmountEl.textContent = `${numberFormatter.format(totalKilometers)} ${unitShort}`;
        if (dailyAverageEl) dailyAverageEl.textContent = translationManager.translate('stats_daily_average', {
            amount: numberFormatter.format(averageKilometers),
            unit_short: unitShort
        });
        if (estimatedTotalEl) estimatedTotalEl.textContent = `${numberFormatter.format(estimatedTotal)} ${unitShort}`;

        if (targetInfoEl) targetInfoEl.textContent = translationManager.translate('stats_target_of', {
            amount: numberFormatter.format(session.target_kilometers),
            unit_short: unitShort
        });
        if (targetProbabilityEl) targetProbabilityEl.textContent = `${numberFormatter.format(probability)}%`;

        // Update training period components
        if (dateRangeEl && session.start_date && session.end_date) {
            const formatDate = (dateStr) => {
                const date = new Date(dateStr);
                return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
            };
            const startDateFormatted = formatDate(session.start_date);
            const endDateFormatted = formatDate(session.end_date);
            dateRangeEl.textContent = `${startDateFormatted} - ${endDateFormatted}`;
        }

        if (dayProgressEl) {
            dayProgressEl.textContent = translationManager.translate('stats_day_progress', {
                current: elapsedDays,
                total: trainingDays
            });
        }

        if (remainingInfoEl) {
            remainingInfoEl.textContent = translationManager.translate('stats_remaining_days_info', {
                count: remainingDays
            });
        }
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
        const totalKilometers = runs.reduce((sum, run) => sum + parseFloat(run.amount), 0);
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
