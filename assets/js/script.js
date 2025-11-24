// Modern Dashboard 2025 JavaScript - Basic Version

// DOM Elements
const sidebarToggle = document.getElementById('sidebarToggle');
const mobileToggle = document.getElementById('mobileToggle');
const sidebar = document.getElementById('sidebar');
const themeToggle = document.getElementById('themeToggle');
const searchInput = document.querySelector('.search-box input');
const notificationBtn = document.querySelector('.notification-btn');

// Theme Management
class ThemeManager {
    constructor() {
        this.currentTheme = localStorage.getItem('theme') || 'dark';
        this.init();
    }

    init() {
        this.applyTheme(this.currentTheme);
        this.bindEvents();
    }

    bindEvents() {
        if (themeToggle) {
            themeToggle.addEventListener('click', () => this.toggleTheme());
        }
    }

    toggleTheme() {
        this.currentTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
        this.applyTheme(this.currentTheme);
        localStorage.setItem('theme', this.currentTheme);
    }

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
    }
}

// Sidebar Management
class SidebarManager {
    constructor() {
        this.isOpen = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.handleResize();
        window.addEventListener('resize', () => this.handleResize());
    }

    bindEvents() {
        // Toggle buttons
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => this.toggle());
        }
        
        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => this.toggle());
        }

        // Close sidebar when clicking outside on mobile
        link.addEventListener('click', (e) => {
            // e.preventDefault();  <-- Hapus atau beri komentar //
            
            // Cek jika linknya bukan '#' baru pindah
            if(link.getAttribute('href') === '#' || link.getAttribute('href') === '') {
                e.preventDefault();
            }
        
            this.setActiveNav(link);
            
            if (window.innerWidth <= 768) {
                this.close();
            }
        });
        // Navigation links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.setActiveNav(link);
                
                // Close sidebar on mobile after navigation
                if (window.innerWidth <= 768) {
                    this.close();
                }
            });
        });
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        if (sidebar) {
            sidebar.classList.add('active');
            this.isOpen = true;
            
            // Add overlay for mobile
            if (window.innerWidth <= 768) {
                this.createOverlay();
            }
        }
    }

    close() {
        if (sidebar) {
            sidebar.classList.remove('active');
            this.isOpen = false;
            this.removeOverlay();
        }
    }

    createOverlay() {
        if (!document.querySelector('.sidebar-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;
            
            document.body.appendChild(overlay);
            
            // Trigger animation
            setTimeout(() => {
                overlay.style.opacity = '1';
            }, 10);
            
            overlay.addEventListener('click', () => this.close());
        }
    }

    removeOverlay() {
        const overlay = document.querySelector('.sidebar-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.remove();
            }, 300);
        }
    }

    setActiveNav(activeLink) {
        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add active class to clicked item
        activeLink.closest('.nav-item').classList.add('active');
    }

    handleResize() {
        if (window.innerWidth > 768) {
            this.removeOverlay();
            if (sidebar) {
                sidebar.classList.remove('active');
            }
            this.isOpen = false;
        }
    }
}

// Search Functionality
class SearchManager {
    constructor() {
        this.init();
    }

    init() {
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.performSearch(e.target.value);
                }
            });
        }
    }

    handleSearch(query) {
        // Debounce search
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            if (query.length > 2) {
                this.showSearchSuggestions(query);
            } else {
                this.hideSearchSuggestions();
            }
        }, 300);
    }

    showSearchSuggestions(query) {
        // Mock search suggestions
        const suggestions = [
            'Dashboard Analytics',
            'User Management',
            'Order History',
            'Revenue Reports',
            'Product Catalog'
        ].filter(item => item.toLowerCase().includes(query.toLowerCase()));

        this.renderSuggestions(suggestions);
    }

    renderSuggestions(suggestions) {
        let dropdown = document.querySelector('.search-dropdown');
        
        if (!dropdown) {
            dropdown = document.createElement('div');
            dropdown.className = 'search-dropdown';
            dropdown.style.cssText = `
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--bg-glass);
                backdrop-filter: blur(20px);
                border: 1px solid var(--border-color);
                border-radius: var(--radius-md);
                margin-top: 0.5rem;
                z-index: 1000;
                max-height: 200px;
                overflow-y: auto;
            `;
            
            searchInput.parentElement.appendChild(dropdown);
        }

        dropdown.innerHTML = suggestions.map(suggestion => `
            <div class="search-suggestion" style="
                padding: 0.75rem 1rem;
                cursor: pointer;
                transition: var(--transition-fast);
                border-bottom: 1px solid var(--border-color);
            ">
                <i class="fas fa-search" style="margin-right: 0.5rem; color: var(--text-secondary);"></i>
                ${suggestion}
            </div>
        `).join('');

        // Add click events
        dropdown.querySelectorAll('.search-suggestion').forEach(item => {
            item.addEventListener('click', () => {
                searchInput.value = item.textContent.trim();
                this.hideSearchSuggestions();
                this.performSearch(searchInput.value);
            });
            
            item.addEventListener('mouseenter', () => {
                item.style.background = 'var(--bg-glass-hover)';
            });
            
            item.addEventListener('mouseleave', () => {
                item.style.background = 'transparent';
            });
        });
    }

    hideSearchSuggestions() {
        const dropdown = document.querySelector('.search-dropdown');
        if (dropdown) {
            dropdown.remove();
        }
    }

    performSearch(query) {
        console.log('Performing search for:', query);
        // Implement actual search functionality here
        this.hideSearchSuggestions();
    }
}

// Notification Manager
class NotificationManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Close notification dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const notificationWrapper = document.querySelector('.notification-wrapper');
            const notificationToggle = document.getElementById('notification-toggle');
            
            if (notificationWrapper && !notificationWrapper.contains(e.target)) {
                if (notificationToggle) {
                    notificationToggle.checked = false;
                }
            }
        });
        
        // Close notification when clicking on notification items
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', () => {
                const notificationToggle = document.getElementById('notification-toggle');
                if (notificationToggle) {
                    notificationToggle.checked = false;
                }
                
                // Optional: Add mark as read functionality here
                const title = item.querySelector('.notification-title');
                if (title) {
                    console.log('Notification clicked:', title.textContent);
                }
            });
        });
        
        // Close user menu when notification is opened
        const notificationToggle = document.getElementById('notification-toggle');
        if (notificationToggle) {
            notificationToggle.addEventListener('change', () => {
                if (notificationToggle.checked) {
                    const userMenu = document.querySelector('.user-menu');
                    if (userMenu) {
                        userMenu.classList.remove('active');
                    }
                }
            });
        }
    }
}

// Animation Manager
class AnimationManager {
    constructor() {
        this.init();
    }

    init() {
        this.observeElements();
        this.animateCounters();
        this.animateProgressBars();
    }

    observeElements() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, { threshold: 0.1 });

        // Observe cards and sections
        document.querySelectorAll('.stat-card, .chart-card, .activity-card').forEach(el => {
            observer.observe(el);
        });
    }

    animateCounters() {
        const counters = document.querySelectorAll('.stat-value');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
            const prefix = counter.textContent.replace(/[0-9,]/g, '');
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = prefix + Math.floor(current).toLocaleString();
            }, 20);
        });
    }

    animateProgressBars() {
        const progressBars = document.querySelectorAll('.progress-fill');
        
        progressBars.forEach(bar => {
            const width = bar.style.width || bar.getAttribute('data-width') || '0%';
            bar.style.width = '0%';
            
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });
    }
}

// Chart Manager (Mock implementation for Basic Version)
class ChartManager {
    constructor() {
        this.init();
    }

    init() {
        // Check if Chart.js is available
        if (typeof Chart !== 'undefined') {
            this.initializeCharts();
        } else {
            this.createMockCharts();
        }
    }

    createMockCharts() {
        // Create simple mock charts for basic version
        const chartContainers = document.querySelectorAll('.chart-container');
        
        chartContainers.forEach((container, index) => {
            const canvas = document.createElement('canvas');
            canvas.style.cssText = `
                width: 100%;
                height: 100%;
                background: linear-gradient(45deg, var(--primary-gradient));
                border-radius: var(--radius-md);
                opacity: 0.1;
            `;
            
            const placeholder = document.createElement('div');
            placeholder.style.cssText = `
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                text-align: center;
                color: var(--text-secondary);
            `;
            placeholder.innerHTML = `
                <i class="fas fa-chart-${index === 0 ? 'line' : 'pie'}" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                <div>Chart Placeholder</div>
                <div style="font-size: 0.8rem; margin-top: 0.25rem;">Add Chart.js for interactive charts</div>
            `;
            
            container.style.position = 'relative';
            container.appendChild(canvas);
            container.appendChild(placeholder);
        });
    }

    initializeCharts() {
        // Initialize actual charts if Chart.js is available
        this.createRevenueChart();
        this.createTrafficChart();
    }

    createRevenueChart() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [12000, 19000, 15000, 25000, 22000, 30000],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#a0a0a0'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#a0a0a0'
                        }
                    }
                }
            }
        });
    }

    createTrafficChart() {
        const ctx = document.getElementById('trafficChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Direct', 'Social', 'Referral', 'Email'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: [
                        '#667eea',
                        '#f093fb',
                        '#4facfe',
                        '#43e97b'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#a0a0a0',
                            padding: 20
                        }
                    }
                }
            }
        });
    }
}

// User Menu Management
class UserMenuManager {
    constructor() {
        this.userMenuToggle = document.querySelector('.user-menu-toggle');
        this.userMenu = document.querySelector('.user-menu');
        this.init();
    }

    init() {
        if (this.userMenuToggle) {
            this.userMenuToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleMenu();
            });
        }

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (this.userMenu && !this.userMenu.contains(e.target)) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        // Close notification dropdown if open
        const notificationDropdown = document.querySelector('.notification-dropdown');
        if (notificationDropdown) {
            notificationDropdown.remove();
        }
        
        if (this.userMenu) {
            this.userMenu.classList.toggle('active');
        }
    }

    closeMenu() {
        if (this.userMenu) {
            this.userMenu.classList.remove('active');
        }
    }
}

// Initialize Dashboard
class Dashboard {
    constructor() {
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeComponents());
        } else {
            this.initializeComponents();
        }
    }

    initializeComponents() {
        // Initialize all managers
        this.themeManager = new ThemeManager();
        this.sidebarManager = new SidebarManager();
        this.searchManager = new SearchManager();
        this.notificationManager = new NotificationManager();
        this.userMenuManager = new UserMenuManager();
        this.animationManager = new AnimationManager();
        this.chartManager = new ChartManager();

        // Add loading states
        this.removeLoadingStates();
        
        console.log('Dashboard initialized successfully!');
    }

    removeLoadingStates() {
        // Remove loading classes after initialization
        setTimeout(() => {
            document.querySelectorAll('.loading').forEach(el => {
                el.classList.remove('loading');
            });
        }, 1000);
    }
}

// --- LOGIKA RESPONSIVE SIDEBAR ---
document.addEventListener('DOMContentLoaded', function() {

    // 1. DEFINISI ELEMEN
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobileToggle');
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    // -------------------------------------------------
    // FIX SIDEBAR MOBILE (1X KLIK PASTI JALAN)
    // -------------------------------------------------
    if (mobileToggle && sidebar) {
        
        // Hapus event lama jika ada (pembersihan)
        mobileToggle.onclick = null; 

        mobileToggle.addEventListener('click', function(e) {
            // MATIKAN SEMUA GANGGUAN
            e.preventDefault(); 
            e.stopPropagation(); 
            e.stopImmediatePropagation(); 

            // SAKLAR: BUKA / TUTUP
            // Kami gunakan toggle class 'active'
            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            } else {
                sidebar.classList.add('active');
            }
        });

        // Klik di Luar Sidebar -> Menutup Sidebar
        document.addEventListener('click', function(e) {
            // Hanya jalan jika sidebar sedang TERBUKA
            if (sidebar.classList.contains('active')) {
                // Jika yang diklik BUKAN Sidebar DAN BUKAN Tombol Hamburger
                if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
        
        // Klik Tombol X (Close) di dalam Sidebar
        const sidebarClose = document.getElementById('sidebarToggle');
        if(sidebarClose) {
            sidebarClose.addEventListener('click', function(){
                sidebar.classList.remove('active');
            });
        }
    }

    // -------------------------------------------------
    // LOGIKA DARK MODE
    // -------------------------------------------------
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    updateIcon(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }

    function updateIcon(theme) {
        const icon = themeToggle ? themeToggle.querySelector('i') : null;
        if (icon) {
            icon.className = (theme === 'dark') ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    // -------------------------------------------------
    // HAPUS LOADING SCREEN
    // -------------------------------------------------
    const loadingScreen = document.getElementById('loadingScreen');
    if (loadingScreen) {
        loadingScreen.style.opacity = '0';
        setTimeout(() => { loadingScreen.style.display = 'none'; }, 300);
    }

});

document.addEventListener('DOMContentLoaded', function() {

    // 1. DEFINISI ELEMEN
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobileToggle'); // Tombol Garis Tiga
    const sidebarToggle = document.getElementById('sidebarToggle'); // Tombol X
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    // -------------------------------------------------
    // 2. LOGIKA TOMBOL HAMBURGER (BUKA/TUTUP)
    // -------------------------------------------------
    if (mobileToggle && sidebar) {
        // Bersihkan event lama
        mobileToggle.onclick = null; 

        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault(); 
            e.stopPropagation(); 
            e.stopImmediatePropagation(); // Matikan gangguan luar

            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            } else {
                sidebar.classList.add('active');
            }
        });
    }

    // -------------------------------------------------
    // 3. LOGIKA TOMBOL X (CLOSE) - PERBAIKAN DISINI
    // -------------------------------------------------
    if (sidebarToggle && sidebar) {
        // Bersihkan event lama
        sidebarToggle.onclick = null;

        sidebarToggle.addEventListener('click', function(e) {
            // Matikan gangguan agar langsung eksekusi
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // Paksa Tutup
            sidebar.classList.remove('active');
        });
    }

    // -------------------------------------------------
    // 4. LOGIKA KLIK DI LUAR SIDEBAR (AUTO CLOSE)
    // -------------------------------------------------
    document.addEventListener('click', function(e) {
        // Hanya jalan jika sidebar sedang TERBUKA dan di Layar Kecil
        if (window.innerWidth <= 1200 && sidebar.classList.contains('active')) {
            // Jika yang diklik BUKAN Sidebar, BUKAN Hamburger, dan BUKAN Tombol X
            if (!sidebar.contains(e.target) && 
                !mobileToggle.contains(e.target) && 
                !sidebarToggle.contains(e.target)) {
                
                sidebar.classList.remove('active');
            }
        }
    });

    // -------------------------------------------------
    // 5. LOGIKA DARK MODE
    // -------------------------------------------------
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    updateIcon(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }

    function updateIcon(theme) {
        const icon = themeToggle ? themeToggle.querySelector('i') : null;
        if (icon) {
            icon.className = (theme === 'dark') ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    // -------------------------------------------------
    // 6. HAPUS LOADING SCREEN (Mencegah Macet)
    // -------------------------------------------------
    const loadingScreen = document.getElementById('loadingScreen');
    if (loadingScreen) {
        loadingScreen.style.opacity = '0';
        setTimeout(() => { loadingScreen.style.display = 'none'; }, 300);
    }

});

// Start the dashboard
const dashboard = new Dashboard();