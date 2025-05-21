// Tab switching detection and warning system
let tabSwitchCount = 0;
const maxWarnings = 3;
let warnings = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables from the page
    const examId = document.querySelector('input[name="exam_id"]').value;
    
    // Handle tab visibility change
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            tabSwitchCount++;
            warnings++;
            
            // Log the activity
            logActivity('Tab switched - Warning ' + warnings + ' of ' + maxWarnings);
            
            // Show warning
            Swal.fire({
                title: 'Peringatan!',
                text: `Anda telah berpindah tab! Ini adalah peringatan ke-${warnings} dari ${maxWarnings}. Tindakan ini akan dicatat.`,
                icon: 'warning',
                confirmButtonText: 'Saya Mengerti'
            });
            
            // If max warnings reached
            if (warnings >= maxWarnings) {
                Swal.fire({
                    title: 'Peringatan Serius!',
                    text: 'Anda telah mencapai batas maksimal peringatan. Hal ini akan dilaporkan ke admin.',
                    icon: 'error',
                    confirmButtonText: 'Saya Mengerti'
                });
                
                logActivity('Maximum tab switch warnings reached');
            }
        }
    });
    
    // Function to log activity to server
    function logActivity(activity) {
        fetch('/exam/logActivity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `exam_id=${examId}&activity=${encodeURIComponent(activity)}`
        })
        .catch(error => console.error('Error logging activity:', error));
    }
    
    // Prevent right-click
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Prevent keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Prevent Alt+Tab
        if (e.altKey) {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+... shortcuts
        if (e.ctrlKey) {
            e.preventDefault();
            return false;
        }
    });
}); 