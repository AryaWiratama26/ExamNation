// manage_exam.js

document.addEventListener('DOMContentLoaded', function() {
    // Constants
    const ITEMS_PER_PAGE = 10;
    
    // DOM Elements
    const examTable = document.getElementById('examTable');
    const tableBody = examTable.querySelector('tbody');
    const searchInput = document.getElementById('searchInput');
    const filterDuration = document.getElementById('filterDuration');
    const pagination = document.getElementById('pagination');
    const deleteModal = document.getElementById('deleteModal');
    const confirmDelete = document.getElementById('confirmDelete');
    const cancelDelete = document.getElementById('cancelDelete');
    const closeModal = document.querySelector('.close-modal');
    
    // Store exam data and current state
    let allExams = Array.from(tableBody.querySelectorAll('tr')).map(row => {
        const titleCell = row.querySelector('td[data-label="Judul"]');
        const descriptionCell = row.querySelector('td[data-label="Deskripsi"]');
        const durationCell = row.querySelector('td[data-label="Durasi"]');
        const actionCell = row.querySelector('td[data-label="Aksi"]');
        
        return {
            element: row,
            title: titleCell.textContent.trim(),
            description: descriptionCell.textContent.trim(),
            duration: parseInt(durationCell.textContent.trim().replace(' menit', '')),
            actions: actionCell.innerHTML
        };
    });
    
    let currentPage = 1;
    let filteredExams = [...allExams];
    let sortColumn = '';
    let sortDirection = 'asc';
    let examToDelete = null;
    
    // Utility function to get base URL from the page
    function getBaseUrl() {
        const baseUrl = window.location.origin + '/';
        console.log('Base URL detected:', baseUrl);
        return baseUrl;
    }
    
    // Store base URL for use in JS
    const baseUrl = getBaseUrl();
    
    // Initialize the page
    function init() {
        setupEventListeners();
        renderTable();
        setupSortingIndicators();
        updatePagination();
    }
    
    // Set up event listeners
    function setupEventListeners() {
        // Search and filter
        searchInput.addEventListener('input', handleSearch);
        filterDuration.addEventListener('change', handleFilter);
        
        // Table sorting
        const sortableHeaders = examTable.querySelectorAll('th[data-sort]');
        sortableHeaders.forEach(header => {
            header.addEventListener('click', () => handleSort(header.getAttribute('data-sort')));
        });
        
        // Delete exam modal - need to use event delegation since rows can be rerendered
        tableBody.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-exam');
            if (deleteBtn) {
                e.preventDefault();
                examToDelete = deleteBtn.getAttribute('data-id');
                console.log('Delete button clicked, exam ID:', examToDelete);
                showModal();
            }
        });
        
        // Modal controls
        closeModal.addEventListener('click', hideModal);
        cancelDelete.addEventListener('click', hideModal);
        confirmDelete.addEventListener('click', confirmDeleteExam);
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                hideModal();
            }
        });
    }
    
    // Handle search input
    function handleSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        applyFiltersAndSort();
        currentPage = 1;
        renderTable();
        updatePagination();
    }
    
    // Handle duration filter
    function handleFilter() {
        applyFiltersAndSort();
        currentPage = 1;
        renderTable();
        updatePagination();
    }
    
    // Apply all active filters and sorting
    function applyFiltersAndSort() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const durationFilter = filterDuration.value;
        
        filteredExams = allExams.filter(exam => {
            const matchesSearch = 
                exam.title.toLowerCase().includes(searchTerm) || 
                exam.description.toLowerCase().includes(searchTerm);
                
            const matchesDuration = !durationFilter || exam.duration === parseInt(durationFilter);
            
            return matchesSearch && matchesDuration;
        });
        
        if (sortColumn) {
            sortExams();
        }
    }
    
    // Handle column sorting
    function handleSort(column) {
        if (sortColumn === column) {
            // Toggle direction if already sorting by this column
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortColumn = column;
            sortDirection = 'asc';
        }
        
        setupSortingIndicators();
        sortExams();
        renderTable();
    }
    
    // Set up sorting indicators in table headers
    function setupSortingIndicators() {
        const headers = examTable.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.classList.remove('asc', 'desc');
            
            if (header.getAttribute('data-sort') === sortColumn) {
                header.classList.add(sortDirection);
            }
        });
    }
    
    // Sort exams based on current column and direction
    function sortExams() {
        filteredExams.sort((a, b) => {
            let valA, valB;
            
            if (sortColumn === 'title') {
                valA = a.title;
                valB = b.title;
            } else if (sortColumn === 'duration') {
                valA = a.duration;
                valB = b.duration;
            }
            
            if (typeof valA === 'string') {
                if (sortDirection === 'asc') {
                    return valA.localeCompare(valB);
                } else {
                    return valB.localeCompare(valA);
                }
            } else {
                if (sortDirection === 'asc') {
                    return valA - valB;
                } else {
                    return valB - valA;
                }
            }
        });
    }
    
    // Render table with current page of filtered exams
    function renderTable() {
        // Clear table body
        tableBody.innerHTML = '';
        
        const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
        const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, filteredExams.length);
        
        if (filteredExams.length === 0) {
            const noDataRow = document.createElement('tr');
            noDataRow.innerHTML = `
                <td colspan="4" style="text-align: center; padding: 20px;">
                    <p>Tidak ada ujian yang sesuai dengan pencarian Anda.</p>
                </td>
            `;
            tableBody.appendChild(noDataRow);
            return;
        }
        
        // Add visible exams
        for (let i = startIndex; i < endIndex; i++) {
            const exam = filteredExams[i];
            const row = document.createElement('tr');
            
            row.innerHTML = `
                <td data-label="Judul">${exam.title}</td>
                <td data-label="Deskripsi">${exam.description}</td>
                <td data-label="Durasi">${exam.duration} menit</td>
                <td data-label="Aksi">${exam.actions}</td>
            `;
            
            tableBody.appendChild(row);
        }
    }
    
    // Update pagination controls
    function updatePagination() {
        const totalPages = Math.ceil(filteredExams.length / ITEMS_PER_PAGE);
        
        let paginationHTML = '<ul class="pagination">';
        
        // Previous button
        paginationHTML += `
            <li class="pagination-item">
                <a href="#" class="pagination-link ${currentPage === 1 ? 'disabled' : ''}" 
                   data-page="${currentPage - 1}" ${currentPage === 1 ? 'aria-disabled="true"' : ''}>
                   &laquo;
                </a>
            </li>
        `;
        
        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(currentPage - Math.floor(maxVisiblePages / 2), 1);
        let endPage = Math.min(startPage + maxVisiblePages - 1, totalPages);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(endPage - maxVisiblePages + 1, 1);
        }
        
        // Show ellipsis for first pages if needed
        if (startPage > 1) {
            paginationHTML += `
                <li class="pagination-item">
                    <a href="#" class="pagination-link" data-page="1">1</a>
                </li>
                ${startPage > 2 ? '<li class="pagination-item"><span class="pagination-ellipsis">...</span></li>' : ''}
            `;
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="pagination-item">
                    <a href="#" class="pagination-link ${i === currentPage ? 'active' : ''}" data-page="${i}">
                        ${i}
                    </a>
                </li>
            `;
        }
        
        // Show ellipsis for last pages if needed
        if (endPage < totalPages) {
            paginationHTML += `
                ${endPage < totalPages - 1 ? '<li class="pagination-item"><span class="pagination-ellipsis">...</span></li>' : ''}
                <li class="pagination-item">
                    <a href="#" class="pagination-link" data-page="${totalPages}">${totalPages}</a>
                </li>
            `;
        }
        
        // Next button
        paginationHTML += `
            <li class="pagination-item">
                <a href="#" class="pagination-link ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}" 
                   data-page="${currentPage + 1}" ${currentPage === totalPages || totalPages === 0 ? 'aria-disabled="true"' : ''}>
                   &raquo;
                </a>
            </li>
        `;
        
        paginationHTML += '</ul>';
        pagination.innerHTML = paginationHTML;
        
        // Add event listeners to pagination links
        pagination.querySelectorAll('.pagination-link:not(.disabled)').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const pageNum = parseInt(this.getAttribute('data-page'));
                currentPage = pageNum;
                renderTable();
                updatePagination();
                // Scroll back to top of table
                examTable.scrollIntoView({ behavior: 'smooth' });
            });
        });
    }
    
    // Show delete confirmation modal
    function showModal() {
        deleteModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    // Hide delete confirmation modal
    function hideModal() {
        deleteModal.classList.remove('show');
        document.body.style.overflow = '';
        examToDelete = null;
    }
    
    // Handle confirm delete
    function confirmDeleteExam() {
        if (examToDelete) {
            console.log('Confirming delete for exam ID:', examToDelete);
            // Pastikan URL terbentuk dengan benar
            const deleteUrl = `${baseUrl}admin/delete_exam/${examToDelete}`;
            console.log('Redirecting to:', deleteUrl);
            window.location.href = deleteUrl;
        } else {
            console.error('No exam ID to delete');
        }
        hideModal();
    }
    
    // Export some functions and variables for potential external use
    window.ExamManager = {
        refresh: function() {
            applyFiltersAndSort();
            renderTable();
            updatePagination();
        },
        resetFilters: function() {
            searchInput.value = '';
            filterDuration.value = '';
            currentPage = 1;
            filteredExams = [...allExams];
            sortColumn = '';
            sortDirection = 'asc';
            setupSortingIndicators();
            renderTable();
            updatePagination();
        }
    };
    
    // Initialize the page
    init();
});