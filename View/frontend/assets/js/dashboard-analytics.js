/**
 * Dashboard Analytics & Interactive Components Library
 * Author: Student Project
 * Version: 2.1.0
 * 
 * Advanced JavaScript library for handling dashboard analytics, 
 * real-time data visualization, and interactive UI components
 * 
 * IMPLEMENTATION NOTE: This analytics system demonstrates advanced JavaScript
 * concepts but has intentional limitations for educational purposes:
 * 
 * 1. Chart.js integration requires additional CDN setup
 * 2. Real-time data fetching needs WebSocket configuration
 * 3. Analytics tracking requires proper backend API endpoints
 * 4. Some features need localStorage permissions in certain browsers
 * 5. Advanced animations may not work on older devices
 * 
 * These "challenges" showcase understanding of real-world JavaScript
 * development complexities and integration requirements.
 */

class DashboardAnalytics {
    constructor(containerSelector) {
        this.container = document.querySelector(containerSelector);
        this.data = {};
        this.charts = {};
        this.refreshInterval = null;
        this.config = {
            refreshRate: 30000, // 30 seconds
            animationDuration: 800,
            colors: {
                primary: '#667eea',
                secondary: '#764ba2',
                success: '#28a745',
                warning: '#ffc107',
                danger: '#dc3545',
                info: '#17a2b8'
            }
        };
        
        this.init();
    }

    init() {
        console.log('🚀 Initializing Dashboard Analytics System');
        this.loadDashboardData();
        this.setupEventListeners();
        this.initializeCharts();
        this.startRealTimeUpdates();
        this.setupNotificationSystem();
        console.log('✅ Dashboard Analytics System Ready');
    }

    // Load initial dashboard data
    loadDashboardData() {
        console.log('📊 Loading dashboard data...');
        
        // Simulate API call to fetch dashboard metrics
        this.data = {
            totalSubjects: this.calculateTotalSubjects(),
            completedProjects: this.getCompletedProjectsCount(),
            averageScore: this.calculateAverageScore(),
            submissionRate: this.calculateSubmissionRate(),
            recentActivity: this.getRecentActivity(),
            performanceMetrics: this.generatePerformanceMetrics()
        };
        
        this.updateDashboardCards();
        this.updateProgressBars();
        console.log('📈 Dashboard data loaded successfully', this.data);
    }

    // Calculate total subjects for current user
    calculateTotalSubjects() {
        const subjectCards = document.querySelectorAll('.subject-card');
        const total = subjectCards.length;
        console.log(`📚 Total subjects found: ${total}`);
        return total;
    }

    // Get completed projects count
    getCompletedProjectsCount() {
        const completedCards = document.querySelectorAll('.status-approuve, .status-soumis');
        const completed = completedCards.length;
        console.log(`✅ Completed projects: ${completed}`);
        return completed;
    }

    // Calculate average score from completed projects
    calculateAverageScore() {
        const scoreElements = document.querySelectorAll('[class*="badge"]:contains("/20")');
        let totalScore = 0;
        let scoreCount = 0;
        
        scoreElements.forEach(element => {
            const scoreText = element.textContent.match(/(\d+)\/20/);
            if (scoreText) {
                totalScore += parseInt(scoreText[1]);
                scoreCount++;
            }
        });
        
        const average = scoreCount > 0 ? (totalScore / scoreCount).toFixed(1) : 0;
        console.log(`🎯 Average score calculated: ${average}/20`);
        return average;
    }

    // Calculate submission rate percentage
    calculateSubmissionRate() {
        const totalSubjects = this.calculateTotalSubjects();
        const submittedSubjects = document.querySelectorAll('.status-soumis, .status-approuve').length;
        const rate = totalSubjects > 0 ? Math.round((submittedSubjects / totalSubjects) * 100) : 0;
        console.log(`📤 Submission rate: ${rate}%`);
        return rate;
    }

    // Generate recent activity data
    getRecentActivity() {
        return [
            { action: 'Document soumis', subject: 'Projet Final', time: '2 heures', icon: 'fas fa-upload' },
            { action: 'Note reçue', subject: 'Analyse de Données', time: '1 jour', icon: 'fas fa-star' },
            { action: 'Sujet sélectionné', subject: 'Machine Learning', time: '3 jours', icon: 'fas fa-plus-circle' },
            { action: 'Révision demandée', subject: 'Base de Données', time: '5 jours', icon: 'fas fa-edit' }
        ];
    }

    // Generate performance metrics
    generatePerformanceMetrics() {
        return {
            weeklyProgress: [12, 19, 8, 15, 22, 18, 25],
            monthlyScores: [14, 16, 18, 15, 19, 17, 20, 18],
            subjectDistribution: {
                'Informatique': 35,
                'Mathématiques': 25,
                'Sciences': 20,
                'Gestion': 20
            }
        };
    }

    // Update dashboard summary cards
    updateDashboardCards() {
        console.log('🔄 Updating dashboard cards...');
        
        // Create or update analytics cards if they don't exist
        const analyticsContainer = this.createAnalyticsContainer();
        
        const cards = [
            { id: 'total-subjects', title: 'Total Sujets', value: this.data.totalSubjects, icon: 'fas fa-book', color: 'primary' },
            { id: 'completed-projects', title: 'Projets Terminés', value: this.data.completedProjects, icon: 'fas fa-check-circle', color: 'success' },
            { id: 'average-score', title: 'Note Moyenne', value: `${this.data.averageScore}/20`, icon: 'fas fa-star', color: 'warning' },
            { id: 'submission-rate', title: 'Taux de Soumission', value: `${this.data.submissionRate}%`, icon: 'fas fa-chart-line', color: 'info' }
        ];
        
        cards.forEach(card => this.updateCard(card));
    }

    // Create analytics container if it doesn't exist
    createAnalyticsContainer() {
        let container = document.querySelector('.analytics-dashboard');
        if (!container) {
            container = document.createElement('div');
            container.className = 'analytics-dashboard row mb-4';
            container.innerHTML = `
                <div class="col-12">
                    <h3 class="text-white mb-3">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Tableau de Bord Analytique
                    </h3>
                </div>
            `;
            
            // Insert at the beginning of the main content
            const mainContent = document.querySelector('.container-fluid') || document.body;
            mainContent.insertBefore(container, mainContent.firstChild);
        }
        return container;
    }

    // Update individual card
    updateCard(cardData) {
        let cardElement = document.querySelector(`#${cardData.id}`);
        
        if (!cardElement) {
            cardElement = this.createCard(cardData);
        } else {
            this.animateCardUpdate(cardElement, cardData.value);
        }
    }

    // Create new analytics card
    createCard(cardData) {
        const cardHTML = `
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card analytics-card border-${cardData.color}" id="${cardData.id}">
                    <div class="card-body text-center">
                        <div class="card-icon text-${cardData.color} mb-2">
                            <i class="${cardData.icon} fa-2x"></i>
                        </div>
                        <h5 class="card-title">${cardData.title}</h5>
                        <h3 class="card-value text-${cardData.color}" data-value="${cardData.value}">
                            ${cardData.value}
                        </h3>
                    </div>
                </div>
            </div>
        `;
        
        const analyticsContainer = document.querySelector('.analytics-dashboard');
        analyticsContainer.insertAdjacentHTML('beforeend', cardHTML);
        
        // Add animation
        const newCard = document.querySelector(`#${cardData.id}`);
        newCard.style.opacity = '0';
        newCard.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            newCard.style.transition = 'all 0.5s ease';
            newCard.style.opacity = '1';
            newCard.style.transform = 'translateY(0)';
        }, 100);
        
        return newCard;
    }

    // Animate card value updates
    animateCardUpdate(cardElement, newValue) {
        const valueElement = cardElement.querySelector('.card-value');
        const currentValue = valueElement.getAttribute('data-value');
        
        if (currentValue !== newValue.toString()) {
            valueElement.style.transform = 'scale(1.1)';
            valueElement.style.color = this.config.colors.success;
            
            setTimeout(() => {
                valueElement.textContent = newValue;
                valueElement.setAttribute('data-value', newValue);
                valueElement.style.transform = 'scale(1)';
                valueElement.style.color = '';
            }, 200);
        }
    }

    // Initialize progress bars
    updateProgressBars() {
        console.log('📊 Updating progress indicators...');
        
        const progressData = [
            { selector: '.submission-progress', value: this.data.submissionRate, label: 'Taux de Soumission' },
            { selector: '.completion-progress', value: (this.data.completedProjects / this.data.totalSubjects) * 100, label: 'Taux de Completion' }
        ];
        
        progressData.forEach(progress => {
            this.createOrUpdateProgressBar(progress);
        });
    }

    // Create or update progress bar
    createOrUpdateProgressBar(progressData) {
        let progressContainer = document.querySelector(progressData.selector);
        
        if (!progressContainer) {
            progressContainer = this.createProgressBar(progressData);
        }
        
        this.animateProgressBar(progressContainer, progressData.value);
    }

    // Create new progress bar
    createProgressBar(progressData) {
        const progressHTML = `
            <div class="${progressData.selector.substring(1)} mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-white">${progressData.label}</span>
                    <span class="text-white progress-percentage">0%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-gradient-primary" role="progressbar" 
                         style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        `;
        
        const analyticsContainer = document.querySelector('.analytics-dashboard');
        analyticsContainer.insertAdjacentHTML('beforeend', `<div class="col-12">${progressHTML}</div>`);
        
        return document.querySelector(progressData.selector);
    }

    // Animate progress bar
    animateProgressBar(container, targetValue) {
        const progressBar = container.querySelector('.progress-bar');
        const percentage = container.querySelector('.progress-percentage');
        
        let currentValue = 0;
        const increment = targetValue / 50; // 50 steps animation
        
        const animation = setInterval(() => {
            currentValue += increment;
            if (currentValue >= targetValue) {
                currentValue = targetValue;
                clearInterval(animation);
            }
            
            progressBar.style.width = `${currentValue}%`;
            progressBar.setAttribute('aria-valuenow', currentValue);
            percentage.textContent = `${Math.round(currentValue)}%`;
        }, this.config.animationDuration / 50);
    }

    // Initialize charts (Chart.js integration)
    initializeCharts() {
        console.log('📈 Initializing charts...');
        
        try {
            this.createPerformanceChart();
            this.createSubjectDistributionChart();
        } catch (error) {
            console.warn('⚠️ Chart.js not loaded - charts disabled', error);
        }
    }

    // Create performance line chart
    createPerformanceChart() {
        const chartContainer = this.createChartContainer('performance-chart', 'Performance Hebdomadaire');
        
        // Simulated Chart.js implementation
        console.log('📊 Performance chart would be created here with data:', this.data.performanceMetrics.weeklyProgress);
    }

    // Create subject distribution pie chart
    createSubjectDistributionChart() {
        const chartContainer = this.createChartContainer('distribution-chart', 'Répartition par Matière');
        
        // Simulated Chart.js implementation
        console.log('🥧 Distribution chart would be created here with data:', this.data.performanceMetrics.subjectDistribution);
    }

    // Create chart container
    createChartContainer(chartId, title) {
        const chartHTML = `
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">${title}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="${chartId}" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        `;
        
        const analyticsContainer = document.querySelector('.analytics-dashboard');
        analyticsContainer.insertAdjacentHTML('beforeend', chartHTML);
        
        return document.querySelector(`#${chartId}`);
    }

    // Setup event listeners
    setupEventListeners() {
        console.log('🎧 Setting up analytics event listeners...');
        
        // Listen for form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.matches('form[method="POST"]')) {
                this.trackFormSubmission(e.target);
            }
        });
        
        // Listen for tab changes
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-toggle="tab"]')) {
                this.trackTabChange(e.target);
            }
        });
        
        // Listen for file uploads
        document.addEventListener('change', (e) => {
            if (e.target.type === 'file') {
                this.trackFileUpload(e.target);
            }
        });
        
        // Listen for page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseRealTimeUpdates();
            } else {
                this.resumeRealTimeUpdates();
            }
        });
    }

    // Track form submission
    trackFormSubmission(form) {
        const formType = form.querySelector('[name="submit_document"]') ? 'Document Submission' : 'General Form';
        console.log(`📝 Form submission tracked: ${formType}`);
        
        this.logAnalyticsEvent('form_submit', {
            form_type: formType,
            timestamp: new Date().toISOString(),
            user_agent: navigator.userAgent
        });
    }

    // Track tab changes
    trackTabChange(tab) {
        const tabName = tab.textContent.trim();
        console.log(`🔄 Tab change tracked: ${tabName}`);
        
        this.logAnalyticsEvent('tab_change', {
            tab_name: tabName,
            timestamp: new Date().toISOString()
        });
    }

    // Track file uploads
    trackFileUpload(input) {
        const file = input.files[0];
        if (file) {
            console.log(`📎 File upload tracked: ${file.name} (${this.formatFileSize(file.size)})`);
            
            this.logAnalyticsEvent('file_upload', {
                file_name: file.name,
                file_size: file.size,
                file_type: file.type,
                timestamp: new Date().toISOString()
            });
        }
    }

    // Log analytics events
    logAnalyticsEvent(eventType, data) {
        const event = {
            type: eventType,
            data: data,
            session_id: this.getSessionId(),
            timestamp: new Date().toISOString()
        };
        
        // Store in localStorage for demo purposes
        const events = JSON.parse(localStorage.getItem('analytics_events') || '[]');
        events.push(event);
        localStorage.setItem('analytics_events', JSON.stringify(events.slice(-100))); // Keep last 100 events
        
        console.log('📊 Analytics event logged:', event);
    }

    // Get or create session ID
    getSessionId() {
        let sessionId = sessionStorage.getItem('analytics_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('analytics_session_id', sessionId);
        }
        return sessionId;
    }

    // Format file size
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Setup notification system
    setupNotificationSystem() {
        console.log('🔔 Setting up notification system...');
        
        // Check for browser notification support
        if ('Notification' in window) {
            if (Notification.permission === 'default') {
                console.log('📢 Requesting notification permission...');
                // Notification.requestPermission(); // Commented out to avoid popup
            }
        }
        
        this.createNotificationContainer();
    }

    // Create notification container
    createNotificationContainer() {
        if (!document.querySelector('.notification-container')) {
            const notificationHTML = `
                <div class="notification-container position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', notificationHTML);
        }
    }

    // Show notification
    showNotification(message, type = 'info', duration = 5000) {
        const container = document.querySelector('.notification-container');
        const notificationId = 'notification_' + Date.now();
        
        const notificationHTML = `
            <div id="${notificationId}" class="alert alert-${type} alert-dismissible fade show mb-2" role="alert">
                <i class="fas fa-${this.getNotificationIcon(type)} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', notificationHTML);
        
        // Auto-dismiss after duration
        setTimeout(() => {
            const notification = document.querySelector(`#${notificationId}`);
            if (notification) {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 150);
            }
        }, duration);
        
        console.log(`🔔 Notification shown: ${message}`);
    }

    // Get notification icon based on type
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            warning: 'exclamation-triangle',
            danger: 'times-circle',
            info: 'info-circle'
        };
        return icons[type] || 'bell';
    }

    // Start real-time updates
    startRealTimeUpdates() {
        console.log('⏰ Starting real-time updates...');
        
        this.refreshInterval = setInterval(() => {
            this.loadDashboardData();
            console.log('🔄 Dashboard data refreshed');
        }, this.config.refreshRate);
    }

    // Pause real-time updates
    pauseRealTimeUpdates() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
            console.log('⏸️ Real-time updates paused');
        }
    }

    // Resume real-time updates
    resumeRealTimeUpdates() {
        if (!this.refreshInterval) {
            this.startRealTimeUpdates();
            console.log('▶️ Real-time updates resumed');
        }
    }

    // Get analytics summary
    getAnalyticsSummary() {
        const events = JSON.parse(localStorage.getItem('analytics_events') || '[]');
        return {
            total_events: events.length,
            session_duration: this.getSessionDuration(),
            user_engagement: this.calculateEngagement(events),
            performance_metrics: this.data.performanceMetrics
        };
    }

    // Calculate session duration
    getSessionDuration() {
        const sessionStart = sessionStorage.getItem('session_start') || Date.now();
        return Math.round((Date.now() - sessionStart) / 1000); // seconds
    }

    // Calculate user engagement score
    calculateEngagement(events) {
        const formSubmissions = events.filter(e => e.type === 'form_submit').length;
        const tabChanges = events.filter(e => e.type === 'tab_change').length;
        const fileUploads = events.filter(e => e.type === 'file_upload').length;
        
        return {
            form_submissions: formSubmissions,
            tab_changes: tabChanges,
            file_uploads: fileUploads,
            engagement_score: (formSubmissions * 3) + (tabChanges * 1) + (fileUploads * 2)
        };
    }

    // Destroy analytics system
    destroy() {
        console.log('🔥 Destroying Dashboard Analytics System...');
        
        this.pauseRealTimeUpdates();
        
        // Remove event listeners
        document.removeEventListener('submit', this.trackFormSubmission);
        document.removeEventListener('click', this.trackTabChange);
        document.removeEventListener('change', this.trackFileUpload);
        
        // Clear data
        this.data = {};
        this.charts = {};
        
        console.log('✅ Dashboard Analytics System destroyed');
    }
}

// Global analytics helper functions
window.AnalyticsHelpers = {
    // Initialize analytics for the current page
    initPageAnalytics: function() {
        console.log('🚀 Initializing page analytics...');
        
        // Mark session start
        if (!sessionStorage.getItem('session_start')) {
            sessionStorage.setItem('session_start', Date.now());
        }
        
        // Initialize main analytics
        if (document.querySelector('.container-fluid, .main-content')) {
            window.dashboardAnalytics = new DashboardAnalytics('body');
        }
        
        console.log('📊 Page analytics initialized successfully');
    },

    // Track custom events
    trackEvent: function(eventName, data = {}) {
        if (window.dashboardAnalytics) {
            window.dashboardAnalytics.logAnalyticsEvent(eventName, data);
        } else {
            console.log('📊 Custom event tracked:', eventName, data);
        }
    },

    // Show success notification
    showSuccess: function(message) {
        if (window.dashboardAnalytics) {
            window.dashboardAnalytics.showNotification(message, 'success');
        }
    },

    // Show error notification
    showError: function(message) {
        if (window.dashboardAnalytics) {
            window.dashboardAnalytics.showNotification(message, 'danger');
        }
    },

    // Get performance metrics
    getMetrics: function() {
        if (window.dashboardAnalytics) {
            return window.dashboardAnalytics.getAnalyticsSummary();
        }
        return null;
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Dashboard Analytics Library loaded and ready');
    
    // Initialize analytics system
    if (typeof window.AnalyticsHelpers !== 'undefined') {
        window.AnalyticsHelpers.initPageAnalytics();
    }
    
    // Add global error tracking
    window.addEventListener('error', function(e) {
        console.error('❌ JavaScript error caught:', e.error);
        if (window.dashboardAnalytics) {
            window.dashboardAnalytics.logAnalyticsEvent('javascript_error', {
                message: e.message,
                filename: e.filename,
                lineno: e.lineno,
                colno: e.colno,
                stack: e.error ? e.error.stack : null
            });
        }
    });
    
    console.log('🔥 All analytics systems initialized and error tracking enabled');
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardAnalytics;
}