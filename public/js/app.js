document.addEventListener('DOMContentLoaded', function() {
    
    initPageTransitions();
    
    initInteractiveElements();
    
    initLoadingAnimations();
    
    initChartEnhancements();
    
    initNotificationSystem();
    
    initRealTimeUpdates();
    
    initKeyboardShortcuts();
    
    initThemeToggle();
    
    initDragDrop();
    
    initModernNavbar();
    
    initReportAnalysis();
});

function initPageTransitions() {
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
            <div class="loading-text">Yükleniyor...</div>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
    
    setTimeout(() => {
        loadingOverlay.style.opacity = '0';
        setTimeout(() => loadingOverlay.remove(), 300);
    }, 800);
    
    document.querySelectorAll('a[href^="/"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.href !== window.location.href) {
                e.preventDefault();
                showLoadingOverlay();
                setTimeout(() => {
                    window.location.href = this.href;
                }, 300);
            }
        });
    });
}

function initInteractiveElements() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-12px) scale(1.03)';
            createParticleEffect(this);
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    const tableRows = document.querySelectorAll('.table tr');
    tableRows.forEach((row, index) => {
        if (index === 0) return; 
        
        row.style.animation = `fadeInUp 0.6s ease-out ${index * 0.05}s both`;
        
        row.addEventListener('click', function() {
            this.style.background = 'rgba(34,211,238,0.1)';
            setTimeout(() => {
                this.style.background = '';
            }, 200);
        });
    });
    
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        const label = input.previousElementSibling;
        if (label && label.tagName === 'LABEL') {
            input.addEventListener('focus', () => {
                label.style.transform = 'translateY(-8px) scale(0.9)';
                label.style.color = 'var(--primary)';
            });
            
            input.addEventListener('blur', () => {
                if (!input.value) {
                    label.style.transform = '';
                    label.style.color = '';
                }
            });
        }
        
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.style.borderColor = 'var(--success)';
                this.style.boxShadow = '0 0 0 4px rgba(22,163,74,0.1)';
            } else {
                this.style.borderColor = 'var(--danger)';
                this.style.boxShadow = '0 0 0 4px rgba(220,38,38,0.1)';
            }
        });
    });
    
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('div');
            ripple.className = 'ripple';
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255,255,255,0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple-effect 0.6s linear;
                pointer-events: none;
            `;
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
}

function initLoadingAnimations() {
    const animatedElements = document.querySelectorAll('.panel, .card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.8s ease-out both';
            }
        });
    }, { threshold: 0.1 });
    
    animatedElements.forEach(el => observer.observe(el));
}

function initChartEnhancements() {
    const chartCanvas = document.getElementById('seoChart');
    if (chartCanvas) {
        const chartContainer = chartCanvas.parentElement;
        chartContainer.innerHTML = `
            <div class="chart-loading">
                <div class="chart-skeleton"></div>
                <div class="loading-text">Grafik yükleniyor...</div>
            </div>
            ${chartContainer.innerHTML}
        `;
        
        setTimeout(() => {
            fetch('/api/reports/last10')
                .then(r => r.json())
                .then(data => {
                    document.querySelector('.chart-loading').remove();
                    
                    const labels = data.map(x => x.label);
                    const speed = data.map(x => x.avg_speed);
                    const seo = data.map(x => x.seo_score);
                    const acc = data.map(x => x.accessibility);
                    
                    new Chart(chartCanvas, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Hız Skoru',
                                data: speed,
                                borderColor: '#22d3ee',
                                backgroundColor: 'rgba(34,211,238,0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'SEO Skoru',
                                data: seo,
                                borderColor: '#60a5fa',
                                backgroundColor: 'rgba(96,165,250,0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'Erişilebilirlik',
                                data: acc,
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139,92,246,0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    labels: { color: '#e5e7eb' }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#1f2937' },
                                    ticks: { color: '#9ca3af' }
                                },
                                x: {
                                    grid: { color: '#1f2937' },
                                    ticks: { color: '#9ca3af' }
                                }
                            }
                        }
                    });
                })
                .catch(() => {
                    document.querySelector('.chart-loading').innerHTML = 
                        '<div class="error-state">Grafik yüklenemedi</div>';
                });
        }, 1000);
    }
}

function initNotificationSystem() {
    window.showNotification = function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">${getNotificationIcon(type)}</div>
                <div class="notification-message">${message}</div>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 100);
        
        setTimeout(() => removeNotification(notification), 5000);
        
        notification.querySelector('.notification-close').addEventListener('click', () => {
            removeNotification(notification);
        });
    };
    
    function removeNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }
    
    function getNotificationIcon(type) {
        const icons = {
            success: '✓',
            error: '✗',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || icons.info;
    }
}

function initRealTimeUpdates() {
    setInterval(() => {
        const cards = document.querySelectorAll('.card b');
        cards.forEach(card => {
            if (Math.random() < 0.1) {
                card.style.animation = 'pulse 0.5s ease-in-out';
                setTimeout(() => card.style.animation = '', 500);
            }
        });
    }, 5000);
    
    const statusBadges = document.querySelectorAll('.badge.in_progress');
    statusBadges.forEach(badge => {
        setInterval(() => {
            badge.style.animation = 'pulse 1s ease-in-out';
        }, 3000);
    });
}

function initKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case '1':
                    e.preventDefault();
                    window.location.href = '/';
                    break;
                case '2':
                    e.preventDefault();
                    window.location.href = '/tickets';
                    break;
                case '3':
                    e.preventDefault();
                    window.location.href = '/reports';
                    break;
            }
        }
    });
}

function showLoadingOverlay() {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
            <div class="loading-text">Yönlendiriliyor...</div>
        </div>
    `;
    document.body.appendChild(overlay);
}

// SEO Rapor Analizi Fonksiyonları
function initReportAnalysis() {
    // URL Analizi Form Handler
    const urlForm = document.getElementById('urlAnalysisForm');
    if (urlForm) {
        urlForm.addEventListener('submit', handleUrlAnalysis);
    }
    
    // Toplu Analiz Form Handler
    const bulkForm = document.getElementById('bulkAnalysisForm');
    if (bulkForm) {
        bulkForm.addEventListener('submit', handleBulkAnalysis);
    }
    
    // Progress bar animations
    initProgressBars();
}

async function handleUrlAnalysis(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const analyzeBtn = document.getElementById('analyzeBtn');
    const progressDiv = document.getElementById('analysisProgress');
    
    // UI güncellemeleri
    analyzeBtn.disabled = true;
    analyzeBtn.textContent = '🔄 Analiz Ediliyor...';
    progressDiv.style.display = 'block';
    
    // Progress bar animasyonu başlat
    animateProgressBar(progressDiv.querySelector('.progress-fill'), 60000); // 60 saniye
    
    try {
        const response = await fetch('/reports/analyze-url', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('✅ Analiz başarıyla tamamlandı!', 'success');
            showAnalysisResult(result);
            
            // Formu temizle
            form.reset();
            
            // Sayfayı yenile (yeni raporu görmek için)
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(result.message || 'Analiz hatası');
        }
        
    } catch (error) {
        console.error('URL Analysis Error:', error);
        showNotification('❌ Analiz hatası: ' + error.message, 'error');
    } finally {
        // UI'ı eski haline getir
        analyzeBtn.disabled = false;
        analyzeBtn.textContent = '🚀 Analiz Et';
        progressDiv.style.display = 'none';
    }
}

async function handleBulkAnalysis(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const analyzeBtn = document.getElementById('bulkAnalyzeBtn');
    const progressDiv = document.getElementById('bulkProgress');
    const progressText = document.getElementById('bulkProgressText');
    
    // URL sayısını hesapla
    const urls = formData.get('urls').split('\n').filter(url => url.trim());
    const urlCount = urls.length;
    
    if (urlCount > 10) {
        showNotification('❌ Maksimum 10 URL analiz edilebilir', 'error');
        return;
    }
    
    // UI güncellemeleri
    analyzeBtn.disabled = true;
    analyzeBtn.textContent = '🔄 Toplu Analiz Ediliyor...';
    progressDiv.style.display = 'block';
    progressText.textContent = `${urlCount} URL analiz ediliyor... Bu işlem ${urlCount * 2} dakika sürebilir.`;
    
    // Progress bar animasyonu başlat (URL başına 2 dakika)
    animateProgressBar(progressDiv.querySelector('.progress-fill'), urlCount * 120000);
    
    try {
        const response = await fetch('/reports/analyze-bulk', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(`✅ ${urlCount} URL başarıyla analiz edildi!`, 'success');
            showBulkAnalysisResult(result);
            
            // Formu temizle
            form.reset();
            
            // Sayfayı yenile
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            throw new Error(result.message || 'Toplu analiz hatası');
        }
        
    } catch (error) {
        console.error('Bulk Analysis Error:', error);
        showNotification('❌ Toplu analiz hatası: ' + error.message, 'error');
    } finally {
        analyzeBtn.disabled = false;
        analyzeBtn.textContent = '📈 Toplu Analiz Et';
        progressDiv.style.display = 'none';
    }
}

function showAnalysisResult(result) {
    const resultHtml = `
        <div class="analysis-result">
            <h4>🎉 Analiz Tamamlandı!</h4>
            <div class="result-scores">
                <div class="score-item">
                    <span class="score-label">Lighthouse SEO:</span>
                    <span class="score-value ${getScoreClass(result.lighthouse_score)}">${result.lighthouse_score}/100</span>
                </div>
                <div class="score-item">
                    <span class="score-label">Özel SEO Skoru:</span>
                    <span class="score-value ${getScoreClass(result.seo_score)}">${result.seo_score}/100</span>
                </div>
                <div class="score-item">
                    <span class="score-label">Tespit Edilen Sorunlar:</span>
                    <span class="score-value ${result.issues_found > 0 ? 'error' : 'success'}">${result.issues_found}</span>
                </div>
            </div>
        </div>
    `;
    
    showNotification(resultHtml, 'info', 10000);
}

function showBulkAnalysisResult(result) {
    let resultHtml = `
        <div class="bulk-analysis-result">
            <h4>📊 Toplu Analiz Tamamlandı!</h4>
            <div class="bulk-results">
    `;
    
    result.results.forEach((item, index) => {
        resultHtml += `
            <div class="bulk-result-item">
                <div class="result-url">${new URL(item.url).hostname}</div>
                <div class="result-scores">
                    <span class="score ${getScoreClass(item.lighthouse_score)}">LH: ${item.lighthouse_score}</span>
                    <span class="score ${getScoreClass(item.seo_score)}">SEO: ${item.seo_score}</span>
                </div>
            </div>
        `;
    });
    
    resultHtml += `
            </div>
        </div>
    `;
    
    showNotification(resultHtml, 'info', 15000);
}

function getScoreClass(score) {
    if (score >= 80) return 'success';
    if (score >= 60) return 'warn';
    return 'error';
}

function animateProgressBar(progressElement, duration) {
    if (!progressElement) return;
    
    progressElement.style.width = '0%';
    progressElement.style.transition = `width ${duration}ms linear`;
    
    // Küçük bir gecikme ile animasyonu başlat
    setTimeout(() => {
        progressElement.style.width = '100%';
    }, 100);
}

function initProgressBars() {
    // CSS için progress bar stilleri ekle
    const style = document.createElement('style');
    style.textContent = `
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #22d3ee, #06b6d4);
            border-radius: 4px;
            transition: width 0.3s ease;
            width: 0%;
        }
        
        .analysis-result, .bulk-analysis-result {
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #22d3ee;
        }
        
        .result-scores {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .score-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .score-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        
        .score-value {
            font-size: 18px;
            font-weight: bold;
        }
        
        .score-value.success { color: #10b981; }
        .score-value.warn { color: #f59e0b; }
        .score-value.error { color: #ef4444; }
        
        .bulk-results {
            display: grid;
            gap: 10px;
            margin-top: 15px;
        }
        
        .bulk-result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        
        .result-url {
            font-weight: 500;
            color: #374151;
        }
        
        .result-scores .score {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        
        .result-scores .score.success { background: #10b981; }
        .result-scores .score.warn { background: #f59e0b; }
        .result-scores .score.error { background: #ef4444; }
        
        .help-text {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }
        
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .modal-close {
            cursor: pointer;
            font-size: 24px;
            color: #64748b;
        }
        
        .modal-close:hover {
            color: #374151;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }
    `;
    document.head.appendChild(style);
}

// Rapor detayları gösterme
async function showReportDetails(reportId) {
    const modal = document.getElementById('reportModal');
    const detailsDiv = document.getElementById('reportDetails');
    
    modal.style.display = 'flex';
    detailsDiv.innerHTML = '<div class="loading">Rapor detayları yükleniyor...</div>';
    
    try {
        const response = await fetch(`/reports/api/detail?id=${reportId}`);
        const data = await response.json();
        
        if (data.report) {
            detailsDiv.innerHTML = generateReportDetailsHtml(data);
        } else {
            throw new Error('Rapor bulunamadı');
        }
    } catch (error) {
        detailsDiv.innerHTML = `<div class="error">Hata: ${error.message}</div>`;
    }
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
}

function generateReportDetailsHtml(data) {
    const report = data.report;
    const lighthouse = data.lighthouse_data;
    
    let html = `
        <div class="report-detail">
            <div class="report-header">
                <h4>${report.label}</h4>
                <p class="report-meta">
                    <span>📅 ${new Date(report.created_at).toLocaleDateString('tr-TR')}</span>
                    ${report.url ? `<span>🌐 <a href="${report.url}" target="_blank">${report.url}</a></span>` : ''}
                </p>
            </div>
            
            <div class="score-grid">
                <div class="score-card">
                    <div class="score-title">Performans</div>
                    <div class="score-value ${getScoreClass(report.avg_speed)}">${report.avg_speed}/100</div>
                </div>
                <div class="score-card">
                    <div class="score-title">SEO</div>
                    <div class="score-value ${getScoreClass(report.seo_score)}">${report.seo_score}/100</div>
                </div>
                <div class="score-card">
                    <div class="score-title">Erişilebilirlik</div>
                    <div class="score-value ${getScoreClass(report.accessibility)}">${report.accessibility}/100</div>
                </div>
                <div class="score-card">
                    <div class="score-title">SEO Sorunları</div>
                    <div class="score-value ${report.seo_issues > 0 ? 'error' : 'success'}">${report.seo_issues || 0}</div>
                </div>
            </div>
    `;
    
    if (lighthouse && lighthouse.seo_analysis) {
        html += `
            <div class="seo-analysis">
                <h5>🔍 SEO Analiz Detayları</h5>
                
                ${lighthouse.seo_analysis.issues && lighthouse.seo_analysis.issues.length > 0 ? `
                    <div class="issues-section">
                        <h6>Tespit Edilen Sorunlar</h6>
                        ${lighthouse.seo_analysis.issues.map(issue => `
                            <div class="issue-item ${issue.type}">
                                <div class="issue-title">${issue.title}</div>
                                <div class="issue-description">${issue.description}</div>
                                <div class="issue-impact">Etki: ${issue.impact}</div>
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
                
                ${lighthouse.seo_analysis.recommendations && lighthouse.seo_analysis.recommendations.length > 0 ? `
                    <div class="recommendations-section">
                        <h6>Öneriler</h6>
                        <ul>
                            ${lighthouse.seo_analysis.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                        </ul>
                    </div>
                ` : ''}
            </div>
        `;
    }
    
    html += `
            <div class="report-actions">
                <a href="${report.file_path}" target="_blank" class="btn btn-primary">📄 Tam Raporu Görüntüle</a>
            </div>
        </div>
    `;
    
    return html;
}

function createParticleEffect(element) {
    for (let i = 0; i < 5; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.cssText = `
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--primary);
            border-radius: 50%;
            pointer-events: none;
            animation: particle-float 1s ease-out forwards;
        `;
        
        const rect = element.getBoundingClientRect();
        particle.style.left = Math.random() * rect.width + 'px';
        particle.style.top = Math.random() * rect.height + 'px';
        
        element.appendChild(particle);
        setTimeout(() => particle.remove(), 1000);
    }
}

function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = themeToggle?.querySelector('.theme-icon');
    
    if (!themeToggle) return;
    
    const savedTheme = localStorage.getItem('theme') || 'dark';
    setTheme(savedTheme);
    
    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        setTheme(newTheme);
        
        themeToggle.style.transform = 'scale(0.8)';
        setTimeout(() => {
            themeToggle.style.transform = '';
        }, 150);
        
        if (window.showNotification) {
            window.showNotification(
                `${newTheme === 'light' ? 'Açık' : 'Koyu'} tema aktif edildi`, 
                'success'
            );
        }
    });
    
    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        if (themeIcon) {
            themeIcon.textContent = theme === 'light' ? '🌙' : '☀️';
        }
        
        updateChartsForTheme(theme);
    }
    
    function updateChartsForTheme(theme) {
        const chartCanvas = document.getElementById('seoChart');
        if (chartCanvas && window.Chart) {
            const chart = Chart.getChart(chartCanvas);
            if (chart) {
                const gridColor = theme === 'light' ? '#e2e8f0' : '#1f2937';
                const tickColor = theme === 'light' ? '#64748b' : '#9ca3af';
                
                chart.options.scales.y.grid.color = gridColor;
                chart.options.scales.x.grid.color = gridColor;
                chart.options.scales.y.ticks.color = tickColor;
                chart.options.scales.x.ticks.color = tickColor;
                chart.options.plugins.legend.labels.color = theme === 'light' ? '#1e293b' : '#e5e7eb';
                
                chart.update();
            }
        }
    }
}

function initDragDrop() {
    const dragDropArea = document.getElementById('dragDropArea');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const removeFileBtn = document.getElementById('removeFile');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!dragDropArea) return;
    
    dragDropArea.addEventListener('click', () => {
        fileInput.click();
    });
    
    fileInput.addEventListener('change', handleFiles);
    
    dragDropArea.addEventListener('dragover', handleDragOver);
    dragDropArea.addEventListener('dragenter', handleDragEnter);
    dragDropArea.addEventListener('dragleave', handleDragLeave);
    dragDropArea.addEventListener('drop', handleDrop);
    
    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            clearFile();
        });
    }
    
    function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function handleDragEnter(e) {
        e.preventDefault();
        e.stopPropagation();
        dragDropArea.classList.add('drag-over');
    }
    
    function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        dragDropArea.classList.remove('drag-over');
    }
    
    function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        dragDropArea.classList.remove('drag-over');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            validateAndShowFile(file);
        }
    }
    
    function handleFiles(e) {
        const files = e.target.files;
        if (files.length > 0) {
            const file = files[0];
            validateAndShowFile(file);
        }
    }
    
    function validateAndShowFile(file) {
        if (!file.type.includes('json') && !file.name.endsWith('.json')) {
            if (window.showNotification) {
                window.showNotification('Lütfen sadece JSON dosyası seçin!', 'error');
            }
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            if (window.showNotification) {
                window.showNotification('Dosya boyutu 5MB\'dan küçük olmalı!', 'error');
            }
            return;
        }
        
        showFilePreview(file);
    }
    
    function showFilePreview(file) {
        const fileName = filePreview.querySelector('.file-name');
        const fileSize = filePreview.querySelector('.file-size');
        const dragContent = dragDropArea.querySelector('.drag-drop-content');
        
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        
        dragContent.style.display = 'none';
        filePreview.style.display = 'flex';
        
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }
        
        filePreview.style.animation = 'fadeInUp 0.3s ease-out';
        
        if (window.showNotification) {
            window.showNotification('Dosya başarıyla seçildi!', 'success');
        }
    }
    
    function clearFile() {
        const dragContent = dragDropArea.querySelector('.drag-drop-content');
        
        fileInput.value = '';
        dragContent.style.display = 'block';
        filePreview.style.display = 'none';
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

function initModernNavbar() {
    initUserDropdown();
    initMobileMenu();
    initNavbarScrollEffect();
}

function initUserDropdown() {
    const userDropdown = document.getElementById('userDropdown');
    const userTrigger = userDropdown?.querySelector('.user-trigger');
    
    if (!userDropdown || !userTrigger) return;
    
    userTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('open');
    });
    
    document.addEventListener('click', (e) => {
        if (!userDropdown.contains(e.target)) {
            userDropdown.classList.remove('open');
        }
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            userDropdown.classList.remove('open');
        }
    });
}

function initMobileMenu() {
    const mobileToggle = document.getElementById('mobileToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (!mobileToggle || !mobileMenu) return;
    
    mobileToggle.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.contains('open');
        
        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    });
    
    mobileMenu.addEventListener('click', (e) => {
        if (e.target === mobileMenu) {
            closeMobileMenu();
        }
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
            closeMobileMenu();
        }
    });
    
    const mobileNavLinks = mobileMenu.querySelectorAll('.mobile-nav-link');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (link.getAttribute('href')) {
                closeMobileMenu();
            }
        });
    });
    
    function openMobileMenu() {
        mobileToggle.classList.add('active');
        mobileMenu.classList.add('open');
        document.body.style.overflow = 'hidden';
        
        const menuItems = mobileMenu.querySelectorAll('.mobile-nav-link');
        menuItems.forEach((item, index) => {
            item.style.animation = `fadeInUp 0.3s ease-out ${index * 0.05}s both`;
        });
    }
    
    function closeMobileMenu() {
        mobileToggle.classList.remove('active');
        mobileMenu.classList.remove('open');
        document.body.style.overflow = '';
    }
}

function initNavbarScrollEffect() {
    const navbar = document.querySelector('.modern-navbar');
    if (!navbar) return;
    
    let lastScrollY = window.scrollY;
    let ticking = false;
    
    function updateNavbar() {
        const scrollY = window.scrollY;
        
        if (scrollY > 100) {
            navbar.style.background = 'rgba(11, 18, 32, 0.98)';
            navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.3)';
        } else {
            navbar.style.background = 'rgba(11, 18, 32, 0.95)';
            navbar.style.boxShadow = 'none';
        }
        
        if (scrollY > lastScrollY && scrollY > 200) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        lastScrollY = scrollY;
        ticking = false;
    }
    
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateNavbar);
            ticking = true;
        }
    }
    
    window.addEventListener('scroll', requestTick);
}

document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('div');
            ripple.style.cssText = `
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(34, 211, 238, 0.2);
                transform: translate(-50%, -50%);
                animation: navRipple 0.6s ease-out;
                pointer-events: none;
                z-index: 0;
            `;
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
});

const style = document.createElement('style');
style.textContent = `
    @keyframes navRipple {
        to {
            width: 100px;
            height: 100px;
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);