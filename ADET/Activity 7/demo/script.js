class WidgetDemo {
    constructor() {
        this.iframe = document.getElementById('weatherWidget');
        this.widthSlider = document.getElementById('widgetWidth');
        this.heightSlider = document.getElementById('widgetHeight');
        this.widthValue = document.getElementById('widthValue');
        this.heightValue = document.getElementById('heightValue');
        this.resetBtn = document.getElementById('resetBtn');
        this.copyCodeBtn = document.getElementById('copyCodeBtn');
        this.downloadBtn = document.getElementById('downloadBtn');
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.initTabs();
        this.setupSyntaxHighlighting();
    }

    bindEvents() {
        this.widthSlider.addEventListener('input', () => this.updateWidgetSize());
        this.heightSlider.addEventListener('input', () => this.updateWidgetSize());
        this.resetBtn.addEventListener('click', () => this.resetWidgetSize());
        
        this.copyCodeBtn.addEventListener('click', () => this.copyEmbedCode());
        this.downloadBtn.addEventListener('click', () => this.downloadWidgetFiles());
    }

    updateWidgetSize() {
        const width = this.widthSlider.value;
        const height = this.heightSlider.value;
        
        this.iframe.style.width = width + 'px';
        this.iframe.style.height = height + 'px';
        
        this.widthValue.textContent = width + 'px';
        this.heightValue.textContent = height + 'px';
        
        this.iframe.style.transition = 'all 0.3s ease';
    }

    resetWidgetSize() {
        this.widthSlider.value = 400;
        this.heightSlider.value = 500;
        this.updateWidgetSize();
        
        this.resetBtn.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.resetBtn.style.transform = 'scale(1)';
        }, 150);
    }

    initTabs() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.getAttribute('data-tab');
                
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                button.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });
    }

    setupSyntaxHighlighting() {
        if (typeof Prism !== 'undefined') {
            Prism.highlightAll();
        }
    }

    async copyEmbedCode() {
        const embedCode = `<iframe 
    src="widget/index.html" 
    width="400" 
    height="500"
    frameborder="0"
    scrolling="no"
    title="Weather Widget">
</iframe>`;

        try {
            await navigator.clipboard.writeText(embedCode);
            this.showNotification('Embed code copied to clipboard!', 'success');
        } catch (err) {
            this.fallbackCopyToClipboard(embedCode);
        }
    }

    fallbackCopyToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            this.showNotification('Embed code copied to clipboard!', 'success');
        } catch (err) {
            this.showNotification('Unable to copy to clipboard', 'error');
        }
        
        document.body.removeChild(textArea);
    }

    downloadWidgetFiles() {
        this.showNotification('In a real implementation, this would download the widget files as a ZIP archive.', 'info');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${this.getNotificationIcon(type)}"></i>
            <span>${message}</span>
        `;
        
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: this.getNotificationColor(type),
            color: 'white',
            padding: '15px 20px',
            borderRadius: '8px',
            boxShadow: '0 4px 20px rgba(0,0,0,0.15)',
            zIndex: '10000',
            display: 'flex',
            alignItems: 'center',
            gap: '10px',
            maxWidth: '300px',
            animation: 'slideInRight 0.3s ease'
        });
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            info: 'info-circle',
            warning: 'exclamation-triangle'
        };
        return icons[type] || 'info-circle';
    }

    getNotificationColor(type) {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            info: '#17a2b8',
            warning: '#ffc107'
        };
        return colors[type] || '#17a2b8';
    }
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

document.addEventListener('DOMContentLoaded', () => {
    new WidgetDemo();
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

document.getElementById('weatherWidget').addEventListener('load', function() {
    this.style.opacity = '0';
    this.style.transition = 'opacity 0.5s ease';
    setTimeout(() => {
        this.style.opacity = '1';
    }, 100);
});
