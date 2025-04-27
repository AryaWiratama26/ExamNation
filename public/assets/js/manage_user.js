document.addEventListener('DOMContentLoaded', function() {
    // Modal handling
    const modal = document.getElementById('deleteModal');
    const closeBtn = document.getElementsByClassName('close')[0];
    const cancelBtn = document.getElementById('cancelDelete');
    const confirmBtn = document.getElementById('confirmDelete');
    const userName = document.getElementById('userName');
    
    // Get all delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    // Add click event to all delete buttons
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            // Set the user name in the modal
            userName.textContent = name;
            
            // Set the delete URL
            confirmBtn.href = baseUrl + 'admin/delete_user/' + userId;
            
            // Show the modal
            modal.style.display = 'flex';
        });
    });
    
    // Close modal when X is clicked
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Close modal when Cancel button is clicked
    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('userTable');
    const rows = table.getElementsByTagName('tr');
    
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        
        // Start from index 1 to skip table header row
        for (let i = 1; i < rows.length; i++) {
            const nameColumn = rows[i].getElementsByTagName('td')[0];
            const emailColumn = rows[i].getElementsByTagName('td')[1];
            const roleColumn = rows[i].getElementsByTagName('td')[2];
            
            if (nameColumn && emailColumn && roleColumn) {
                const nameValue = nameColumn.textContent || nameColumn.innerText;
                const emailValue = emailColumn.textContent || emailColumn.innerText;
                const roleValue = roleColumn.textContent || roleColumn.innerText;
                
                if (
                    nameValue.toLowerCase().indexOf(filter) > -1 || 
                    emailValue.toLowerCase().indexOf(filter) > -1 || 
                    roleValue.toLowerCase().indexOf(filter) > -1
                ) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
        
        // Check if there are any visible rows
        checkEmptyTable();
    });
    
    // Check if table is empty after filtering
    function checkEmptyTable() {
        let hasVisibleRows = false;
        
        // Start from index 1 to skip table header row
        for (let i = 1; i < rows.length; i++) {
            if (rows[i].style.display !== 'none') {
                hasVisibleRows = true;
                break;
            }
        }
        
        // If no visible rows, show empty message
        const tbody = table.querySelector('tbody');
        const existingEmptyRow = tbody.querySelector('.empty-row');
        
        if (!hasVisibleRows) {
            if (!existingEmptyRow) {
                const emptyRow = document.createElement('tr');
                emptyRow.className = 'empty-row';
                emptyRow.innerHTML = `
                    <td colspan="4" class="empty-state">
                        <i class="fas fa-search"></i>
                        <p>Tidak ada pengguna yang ditemukan</p>
                    </td>
                `;
                tbody.appendChild(emptyRow);
            }
        } else if (existingEmptyRow) {
            tbody.removeChild(existingEmptyRow);
        }
    }
    
    // Table sorting
    const headers = document.querySelectorAll('th[data-sort]');
    
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-sort');
            const currentIsAscending = this.classList.contains('asc');
            
            // Remove sorting classes from all headers
            headers.forEach(h => {
                h.classList.remove('asc', 'desc');
            });
            
            // Add appropriate class for this header
            this.classList.add(currentIsAscending ? 'desc' : 'asc');
            
            // Get all rows to sort (skip header)
            const rowsArray = Array.from(rows).slice(1);
            
            // Sort the rows
            rowsArray.sort((a, b) => {
                const aValue = a.querySelector(`td[data-label="${getColumnLabel(column)}"]`).textContent.trim();
                const bValue = b.querySelector(`td[data-label="${getColumnLabel(column)}"]`).textContent.trim();
                
                if (currentIsAscending) {
                    return bValue.localeCompare(aValue);
                } else {
                    return aValue.localeCompare(bValue);
                }
            });
            
            // Remove all rows except header from table
            while (table.rows.length > 1) {
                table.deleteRow(1);
            }
            
            // Add sorted rows back to the table
            const tbody = table.querySelector('tbody');
            rowsArray.forEach(row => {
                tbody.appendChild(row);
            });
        });
    });
    
    // Helper function to get column label based on sort attribute
    function getColumnLabel(column) {
        switch(column) {
            case 'name': return 'Nama';
            case 'email': return 'Email';
            case 'role': return 'Role';
            default: return column;
        }
    }
    
    // Pagination
    const rowsPerPage = 5;
    let currentPage = 1;
    
    function setupPagination() {
        const rowCount = Array.from(rows).slice(1).filter(row => 
            row.style.display !== 'none' && !row.classList.contains('empty-row')
        ).length;
        
        const pageCount = Math.ceil(rowCount / rowsPerPage);
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';
        
        // Only show pagination if more than one page
        if (pageCount <= 1) {
            pagination.style.display = 'none';
            showPage(1);
            return;
        }
        
        pagination.style.display = 'flex';
        
        // Previous button
        const prevButton = document.createElement('a');
        prevButton.className = 'page-item';
        prevButton.innerHTML = '<i class="fas fa-angle-left"></i>';
        prevButton.addEventListener('click', function() {
            if (currentPage > 1) {
                navigateToPage(currentPage - 1);
            }
        });
        pagination.appendChild(prevButton);
        
        // Page buttons
        for (let i = 1; i <= pageCount; i++) {
            const pageButton = document.createElement('a');
            pageButton.className = 'page-item' + (i === currentPage ? ' active' : '');
            pageButton.textContent = i;
            pageButton.addEventListener('click', function() {
                navigateToPage(i);
            });
            pagination.appendChild(pageButton);
        }
        
        // Next button
        const nextButton = document.createElement('a');
        nextButton.className = 'page-item';
        nextButton.innerHTML = '<i class="fas fa-angle-right"></i>';
        nextButton.addEventListener('click', function() {
            if (currentPage < pageCount) {
                navigateToPage(currentPage + 1);
            }
        });
        pagination.appendChild(nextButton);
        
        showPage(currentPage);
    }
    
    function navigateToPage(pageNum) {
        currentPage = pageNum;
        
        // Update active class
        const pageButtons = document.querySelectorAll('.page-item');
        pageButtons.forEach((button, index) => {
            // Skip first and last buttons (prev/next)
            if (index === 0 || index === pageButtons.length - 1) return;
            
            if (index === currentPage) {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        });
        
        showPage(currentPage);
    }
    
    function showPage(pageNum) {
        const visibleRows = Array.from(rows).slice(1).filter(row => 
            row.style.display !== 'none' && !row.classList.contains('empty-row')
        );
        
        const startIndex = (pageNum - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        
        visibleRows.forEach((row, index) => {
            if (index >= startIndex && index < endIndex) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Initial pagination setup
    setupPagination();
    
    // Re-run pagination when search changes
    searchInput.addEventListener('keyup', setupPagination);
    
    // Check for URL parameters for status messages
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        const results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
    
    const status = getUrlParameter('status');
    const message = getUrlParameter('message');
    
    if (status && message) {
        // Create status message element
        const statusElement = document.createElement('div');
        statusElement.className = `status-message status-${status}`;
        statusElement.innerHTML = `
            <i class="fas fa-${status === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        
        // Insert before the card
        const card = document.querySelector('.card');
        card.parentNode.insertBefore(statusElement, card);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            statusElement.style.opacity = '0';
            setTimeout(() => {
                statusElement.remove();
            }, 300);
        }, 5000);
    }
    
    let baseUrl = window.location.origin + '/';
    if (typeof BASE_URL !== 'undefined') {
        baseUrl = BASE_URL;
    }
});