@extends('layouts.admin')

@section('title', 'WhatsApp Analytics Dashboard')

@section('content')
<div class="container-fluid" x-data="whatsappAnalytics()">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📱 WhatsApp Analytics</h1>
            <p class="text-muted">Comprehensive analytics for WhatsApp bot performance</p>
        </div>
        <div class="d-flex gap-2">
            <select x-model="selectedPeriod" @change="loadAnalytics()" class="form-select">
                <option value="1d">Last 24 Hours</option>
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
                <option value="90d">Last 90 Days</option>
            </select>
            <button @click="refreshAnalytics()" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button @click="exportData()" class="btn btn-success">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Loading analytics data...</p>
    </div>

    <!-- Analytics Content -->
    <div x-show="!loading" style="display: none;">
        <!-- Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Messages</h6>
                                <h3 x-text="analytics.overview?.total_messages || 0"></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-comments fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <small>
                            <span x-text="analytics.overview?.avg_daily_messages || 0"></span> per day average
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Active Users</h6>
                                <h3 x-text="analytics.overview?.total_users || 0"></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <small>
                            <span x-text="analytics.overview?.growth_rate || 0"></span>% growth
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Subscriptions</h6>
                                <h3 x-text="analytics.overview?.active_subscriptions || 0"></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-bell fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <small>Active subscribers</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Response Rate</h6>
                                <h3><span x-text="analytics.overview?.response_rate || 0"></span>%</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-reply fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <small>Bot response efficiency</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Message Trends Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">📈 Message Trends</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="messageTrendsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Message Types Pie Chart -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">📊 Message Types</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="messageTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function whatsappAnalytics() {
    return {
        loading: true,
        selectedPeriod: '7d',
        analytics: {},
        
        async init() {
            await this.loadAnalytics();
        },
        
        async loadAnalytics() {
            this.loading = true;
            try {
                const response = await fetch(`/admin/whatsapp-analytics/data?period=${this.selectedPeriod}`);
                this.analytics = await response.json();
                this.$nextTick(() => {
                    this.renderCharts();
                });
            } catch (error) {
                console.error('Failed to load analytics:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async refreshAnalytics() {
            await fetch('/admin/whatsapp-analytics/refresh', { method: 'POST' });
            await this.loadAnalytics();
        },
        
        exportData() {
            window.open(`/admin/whatsapp-analytics/export?period=${this.selectedPeriod}&format=csv`);
        },
        
        renderCharts() {
            this.renderMessageTrendsChart();
            this.renderMessageTypesChart();
        },
        
        renderMessageTrendsChart() {
            const ctx = document.getElementById('messageTrendsChart');
            if (!ctx || !this.analytics.messages?.daily_stats) return;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.analytics.messages.daily_stats.map(d => d.date),
                    datasets: [{
                        label: 'Incoming',
                        data: this.analytics.messages.daily_stats.map(d => d.incoming),
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }, {
                        label: 'Outgoing',
                        data: this.analytics.messages.daily_stats.map(d => d.outgoing),
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        },
        
        renderMessageTypesChart() {
            const ctx = document.getElementById('messageTypesChart');
            if (!ctx || !this.analytics.messages?.message_types) return;
            
            const types = this.analytics.messages.message_types;
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(types),
                    datasets: [{
                        data: Object.values(types),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });
        }
    }
}
</script>
@endpush
@endsection