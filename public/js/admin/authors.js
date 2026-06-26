document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Authors Instant Search Filter
    const searchInput = document.getElementById('author-search');
    const authorCards = document.querySelectorAll('.author-item-card');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        authorCards.forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            if (name.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // 2. Active Author Navigation
    const emptyState = document.getElementById('empty-state');
    const activeWorkspace = document.getElementById('active-workspace');
    const authorHeaderAvatar = document.getElementById('author-header-avatar');
    const activeAuthorTitle = document.getElementById('active-author-title');
    const activeAuthorSubtitle = document.getElementById('active-author-subtitle');
    const editAuthorBtn = document.getElementById('edit-author-btn');
    const deleteAuthorForm = document.getElementById('delete-author-form');
    const creatorCardTitle = document.getElementById('creator-card-title');
    const formAuthorId = document.getElementById('form-author-id');
    const booksDeckList = document.getElementById('books-deck-list');
    
    let activeAuthorCard = null;

    authorCards.forEach(card => {
        card.addEventListener('click', function() {
            const authorId = this.getAttribute('data-id');
            const authorName = this.getAttribute('data-name');
            const booksCount = this.getAttribute('data-books-count');
            
            // Toggle active styling
            if (activeAuthorCard) {
                activeAuthorCard.classList.remove('active');
            }
            this.classList.add('active');
            activeAuthorCard = this;

            // Load Author details in Workspace
            emptyState.style.display = 'none';
            activeWorkspace.style.display = 'flex';
            
            // Set Header Avatar
            const imgEl = this.querySelector('img');
            if (imgEl) {
                authorHeaderAvatar.innerHTML = `<img src="${imgEl.src}" alt="${authorName}" class="active-author-photo">`;
            } else {
                authorHeaderAvatar.innerHTML = `<div class="active-author-no-photo" style="background:#DCD6BC; color:#4C2D17; font-weight:bold;">${authorName.substring(0, 1).toUpperCase()}</div>`;
            }

            activeAuthorTitle.innerText = authorName;
            activeAuthorSubtitle.innerText = `${booksCount} ${booksCount == 1 ? 'book' : 'books'} in catalog`;
            
            // Update edit profile link
            editAuthorBtn.href = `/admin/authors/${authorId}/edit`;
            
            // Update delete profile action
            if (deleteAuthorForm) {
                deleteAuthorForm.action = `/admin/authors/${authorId}`;
            }
            
            // Set Creator Card Header
            creatorCardTitle.innerHTML = `<i class="fa-solid fa-plus-circle"></i> Add New Book for ${authorName}`;
            formAuthorId.value = authorId;

            // Load Existing Books
            loadBooks(authorId);
            
            // Reset Book Creator Form
            resetForm();
        });
    });

    // Load books list via AJAX
    function loadBooks(authorId) {
        booksDeckList.innerHTML = '<div class="table-empty-state" style="padding: 20px 0;"><i class="fa-solid fa-spinner fa-spin"></i> Loading books...</div>';
        
        fetch(`/admin/authors/${authorId}/books`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderBooksList(data.books);
                } else {
                    booksDeckList.innerHTML = '<div class="table-empty-state" style="padding: 20px 0; color:#C84B31;">Failed to load books.</div>';
                }
            })
            .catch(err => {
                console.error(err);
                booksDeckList.innerHTML = '<div class="table-empty-state" style="padding: 20px 0; color:#C84B31;">Failed to load books due to server error.</div>';
            });
    }

    // Render list of book cards
    function renderBooksList(books) {
        if (!books || books.length === 0) {
            booksDeckList.innerHTML = '<div class="table-empty-state" style="padding: 25px 0;">📭 No books registered under this author yet.</div>';
            return;
        }

        booksDeckList.innerHTML = '';
        books.forEach(book => {
            const card = createBookCardHTML(book);
            booksDeckList.appendChild(card);
        });
    }

    function createBookCardHTML(book) {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'book-card-item';
        itemDiv.setAttribute('data-id', book.id);
        
        const coverSrc = book.image ? `/storage/${book.image}` : '/images/default-book.png';
        const pdfBadge = book.pdf_file ? `<span class="badge-pdf-attached"><i class="fa-solid fa-file-circle-check"></i> PDF Attached</span>` : '';
        
        let classificationBadges = '';
        if (book.classifications && book.classifications.length > 0) {
            book.classifications.forEach(cl => {
                classificationBadges += `<span class="badge-tag-classification classification-${cl.color}">${cl.name}</span>`;
            });
        }

        itemDiv.innerHTML = `
            <div class="book-card-details">
                <img src="${coverSrc}" alt="${book.name}" class="book-card-cover">
                <div class="book-card-meta">
                    <h5>${book.name}</h5>
                    <p>${book.pages} pages &nbsp; ${classificationBadges} &nbsp; ${pdfBadge}</p>
                </div>
            </div>
            <div class="book-card-right-section" style="display: flex; align-items: center; gap: 20px;">
                <div class="book-card-stats">
                    <span class="book-card-price">${parseFloat(book.price).toLocaleString()} Ks</span>
                    <span class="staff-status-badge ${book.stock_quantity < 5 ? 'badge-stock-danger' : ''}" style="padding:4px 8px; border-radius:6px; font-weight:600;">
                        ${book.stock_quantity} units
                    </span>
                </div>
                <div class="book-card-actions-wrapper" style="display: flex; gap: 8px;">
                    <button type="button" class="btn-action-icon btn-action-edit" title="Edit Book">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn-action-icon btn-action-delete" title="Delete Book">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        `;

        const editBtn = itemDiv.querySelector('.btn-action-edit');
        const deleteBtn = itemDiv.querySelector('.btn-action-delete');

        editBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            loadBookIntoEditor(book);
        });

        deleteBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            confirmDeleteBook(book.id);
        });

        return itemDiv;
    }

    // 3. Dropzones File Upload Preview Setup
    const fileCover = document.getElementById('file-cover');
    const filePdf = document.getElementById('file-pdf');
    
    const coverZoneContent = document.getElementById('cover-zone-content');
    const pdfZoneContent = document.getElementById('pdf-zone-content');

    fileCover.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                coverZoneContent.innerHTML = `
                    <img src="${e.target.result}" class="dropzone-preview-thumbnail" alt="Cover Preview">
                    <div class="dropzone-text" style="margin-top: 5px; color:#2D6A4F;"><i class="fa-solid fa-circle-check"></i> Image Loaded</div>
                `;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    filePdf.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            pdfZoneContent.innerHTML = `
                <i class="fa-solid fa-file-circle-check dropzone-success-icon"></i>
                <div class="dropzone-text" style="color:#2D6A4F;">PDF File Selected</div>
                <div class="dropzone-subtext">${this.files[0].name.substring(0, 20)}...</div>
            `;
        }
    });

    // 4. Expandable Synopsis Tray toggle
    const synopsisToggle = document.getElementById('synopsis-toggle');
    const synopsisWrapper = document.getElementById('synopsis-wrapper');

    synopsisToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        synopsisWrapper.classList.toggle('expanded');
    });

    // 5. AJAX Form Submission & Book Editing Controls
    const bookCreatorForm = document.getElementById('book-creator-form');
    const submitBookBtn = document.getElementById('submit-book-btn');
    const cancelEditBookBtn = document.getElementById('cancel-edit-book-btn');

    if (cancelEditBookBtn) {
        cancelEditBookBtn.addEventListener('click', function() {
            resetForm();
        });
    }

    function loadBookIntoEditor(book) {
        document.getElementById('form-book-id').value = book.id;
        document.getElementById('book-name').value = book.name;
        document.getElementById('book-price').value = Math.round(book.price);
        document.getElementById('book-stock').value = book.stock_quantity;
        document.getElementById('book-pages').value = book.pages || 250;

        const synopsisTextarea = bookCreatorForm.querySelector('textarea[name="description"]');
        if (synopsisTextarea) {
            synopsisTextarea.value = book.description || '';
        }

        if (book.description && book.description.trim() !== '') {
            synopsisToggle.classList.add('active');
            synopsisWrapper.classList.add('expanded');
        } else {
            synopsisToggle.classList.remove('active');
            synopsisWrapper.classList.remove('expanded');
        }

        // Uncheck all classification checkboxes first
        const checkboxes = document.querySelectorAll('#parent-classifications-list input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });

        // Check the classifications associated with the book
        if (book.classifications && book.classifications.length > 0) {
            book.classifications.forEach(cl => {
                const cb = document.querySelector(`#parent-classifications-list input[type="checkbox"][value="${cl.id}"]`);
                if (cb) {
                    cb.checked = true;
                }
            });
        }

        // Set Cover Preview
        const coverSrc = book.image ? `/storage/${book.image}` : '/images/default-book.png';
        coverZoneContent.innerHTML = `
            <img src="${coverSrc}" class="dropzone-preview-thumbnail" alt="Cover Preview">
            <div class="dropzone-text" style="margin-top: 5px; color:#2A6F97;"><i class="fa-solid fa-image"></i> Existing Cover</div>
        `;

        // Set PDF Preview
        if (book.pdf_file) {
            const fileName = book.pdf_file.substring(book.pdf_file.lastIndexOf('/') + 1);
            pdfZoneContent.innerHTML = `
                <i class="fa-solid fa-file-circle-check dropzone-success-icon" style="color:#2A6F97;"></i>
                <div class="dropzone-text" style="color:#2A6F97;">PDF Attached</div>
                <div class="dropzone-subtext">${fileName.substring(0, 20)}...</div>
            `;
        } else {
            pdfZoneContent.innerHTML = `
                <i class="fa-solid fa-file-pdf dropzone-icon"></i>
                <div class="dropzone-text">PDF Book Content</div>
                <div class="dropzone-subtext">Drop or click (PDF format)</div>
            `;
        }

        // Update card highlight, headers and buttons
        creatorCardTitle.innerHTML = `<i class="fa-solid fa-pen-to-square"></i> Edit Book: ${book.name}`;
        if (cancelEditBookBtn) {
            cancelEditBookBtn.style.display = 'inline-block';
        }
        submitBookBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Update Book';

        const creatorCard = document.querySelector('.creator-card');
        if (creatorCard) {
            creatorCard.classList.add('editing-mode');
            creatorCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function confirmDeleteBook(bookId) {
        if (!confirm('Are you sure you want to delete this book? This action cannot be undone.')) {
            return;
        }

        fetch(`/admin/catalog/destroy/${bookId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Book deleted successfully!');

                const card = booksDeckList.querySelector(`.book-card-item[data-id="${bookId}"]`);
                if (card) {
                    card.remove();
                }

                if (booksDeckList.children.length === 0) {
                    booksDeckList.innerHTML = '<div class="table-empty-state" style="padding: 25px 0;">📭 No books registered under this author yet.</div>';
                }

                const activeCard = activeAuthorCard;
                if (activeCard) {
                    const countBadge = activeCard.querySelector('.books-count-badge');
                    let currentCount = Math.max(0, parseInt(countBadge.innerText) - 1);
                    countBadge.innerText = currentCount;
                    activeCard.setAttribute('data-books-count', currentCount);
                    activeAuthorSubtitle.innerText = `${currentCount} ${currentCount == 1 ? 'book' : 'books'} in catalog`;
                }

                const formBookId = document.getElementById('form-book-id').value;
                if (formBookId == bookId) {
                    resetForm();
                }
            } else {
                alert('Failed to delete book: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error deleting book: ' + err.message);
        });
    }

    bookCreatorForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Check file sizes before submission to prevent server-side crashes due to post_max_size
        const coverFile = fileCover.files[0];
        const pdfFile = filePdf.files[0];

        if (coverFile && coverFile.size > 2 * 1024 * 1024) {
            alert('Cannot save: Cover image size exceeds 2MB limit.');
            return;
        }

        if (pdfFile && pdfFile.size > 100 * 1024 * 1024) {
            alert('Cannot save: PDF file size exceeds 100MB limit.');
            return;
        }

        const bookId = document.getElementById('form-book-id').value;
        const isEdit = !!bookId;
        const url = isEdit ? `/admin/catalog/update/${bookId}` : '/admin/catalog/store';

        // Show loading state
        submitBookBtn.disabled = true;
        submitBookBtn.innerHTML = isEdit ? '<i class="fa-solid fa-spinner fa-spin"></i> Updating Book...' : '<i class="fa-solid fa-spinner fa-spin"></i> Saving to Deck...';

        const formData = new FormData(this);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async res => {
            if (!res.ok) {
                let errMsg = '';
                const contentType = res.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const rawText = await res.text().catch(() => '');
                    let errData;
                    try {
                        errData = JSON.parse(rawText);
                    } catch(e) {
                        errMsg = `JSON Parse Error (Status: ${res.status})\n\nRaw Response: ${rawText.substring(0, 400)}`;
                        throw new Error(errMsg);
                    }
                    errMsg = errData.message || 'Form validation error';
                    if (errData.errors) {
                        const validationErrors = Object.values(errData.errors).flat().join('\n');
                        errMsg += '\n\n' + validationErrors;
                    } else {
                        errMsg += '\n\nResponse details: ' + JSON.stringify(errData);
                    }
                } else {
                    const text = await res.text().catch(() => '');
                    const titleMatch = text.match(/<title>(.*?)<\/title>/i);
                    const errorTitle = titleMatch ? titleMatch[1] : `HTTP Error ${res.status}`;
                    errMsg = `${errorTitle} (Status: ${res.status})\n\n${text.substring(0, 300)}`;
                }
                throw new Error(errMsg);
            }
            // Since we need to read the body as JSON, and it was successful:
            const rawText = await res.text().catch(() => '{}');
            try {
                return JSON.parse(rawText);
            } catch(e) {
                throw new Error(`Invalid JSON response: ${rawText.substring(0, 200)}`);
            }
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Book saved successfully!');
                
                const newBookCard = createBookCardHTML(data.book);
                
                if (isEdit) {
                    const existingCard = booksDeckList.querySelector(`.book-card-item[data-id="${data.book.id}"]`);
                    if (existingCard) {
                        booksDeckList.replaceChild(newBookCard, existingCard);
                        newBookCard.classList.add('slide-into-deck');
                    }
                } else {
                    newBookCard.classList.add('slide-into-deck');
                    // Remove empty state if present
                    const emptyMsg = booksDeckList.querySelector('.table-empty-state');
                    if (emptyMsg) {
                        booksDeckList.innerHTML = '';
                    }
                    booksDeckList.insertBefore(newBookCard, booksDeckList.firstChild);

                    // Update books count in sidebar and header
                    const activeCard = activeAuthorCard;
                    if (activeCard) {
                        const countBadge = activeCard.querySelector('.books-count-badge');
                        let currentCount = parseInt(countBadge.innerText) + 1;
                        countBadge.innerText = currentCount;
                        activeCard.setAttribute('data-books-count', currentCount);
                        activeAuthorSubtitle.innerText = `${currentCount} ${currentCount == 1 ? 'book' : 'books'} in catalog`;
                    }
                }

                // Reset Creator Form
                resetForm();
            } else {
                alert('Failed to save book: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error saving book: ' + err.message);
        })
        .finally(() => {
            submitBookBtn.disabled = false;
            submitBookBtn.innerHTML = isEdit ? '<i class="fa-solid fa-paper-plane"></i> Update Book' : '<i class="fa-solid fa-paper-plane"></i> Save to Deck';
        });
    });

    function resetForm() {
        bookCreatorForm.reset();
        document.getElementById('form-book-id').value = '';
        
        const creatorCard = document.querySelector('.creator-card');
        if (creatorCard) {
            creatorCard.classList.remove('editing-mode');
        }

        const authorName = activeAuthorTitle ? activeAuthorTitle.innerText : 'Author';
        creatorCardTitle.innerHTML = `<i class="fa-solid fa-plus-circle"></i> Add New Book for ${authorName}`;

        if (cancelEditBookBtn) {
            cancelEditBookBtn.style.display = 'none';
        }
        submitBookBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Save to Deck';
        
        // Reset File upload zones
        coverZoneContent.innerHTML = `
            <i class="fa-solid fa-image dropzone-icon"></i>
            <div class="dropzone-text">Book Cover Image</div>
            <div class="dropzone-subtext">Drop or click (PNG, JPG)</div>
        `;
        
        pdfZoneContent.innerHTML = `
            <i class="fa-solid fa-file-pdf dropzone-icon"></i>
            <div class="dropzone-text">PDF Book Content</div>
            <div class="dropzone-subtext">Drop or click (PDF format)</div>
        `;

        // Collapse synopsis tray
        synopsisToggle.classList.remove('active');
        synopsisWrapper.classList.remove('expanded');
    }

    // Success notification toast
    function showNotification(msg) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-success';
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.style.boxShadow = '0 4px 15px rgba(0,0,0,0.15)';
        toast.style.animation = 'slideIntoDeck 0.3s ease';
        toast.innerHTML = `<i class="fa-solid fa-circle-check"></i> ${msg}`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s ease';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    // ----------------------------------------------------
    // INLINE CLASSIFICATIONS CRUD HANDLERS
    // ----------------------------------------------------
    const classificationModal = document.getElementById('classification-modal');
    const modalTbody = document.getElementById('modal-classifications-tbody');
    const modalForm = document.getElementById('modal-classification-form');
    const modalClassId = document.getElementById('modal-class-id');
    const modalClassName = document.getElementById('modal-class-name');
    const modalClassStatusGroup = document.getElementById('modal-class-status-group');
    const modalClassStatus = document.getElementById('modal-class-status');
    const modalCancelEditBtn = document.getElementById('modal-cancel-edit-btn');
    const modalSubmitBtn = document.getElementById('modal-submit-btn');
    const modalFormTitle = document.getElementById('modal-form-title');

    const classificationIcons = {
        'Fiction': 'fa-feather',
        'Non-Fiction': 'fa-brain',
        'Translation': 'fa-language',
        'Literature': 'fa-feather',
        'Science': 'fa-brain'
    };

    window.openClassificationModal = function() {
        classificationModal.classList.remove('display-none');
        loadModalClassifications();
    };

    window.closeClassificationModal = function() {
        classificationModal.classList.add('display-none');
        resetModalForm();
    };

    function loadModalClassifications() {
        modalTbody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding: 20px 0;"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</td></tr>';
        
        fetch('/admin/classifications', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                rebuildModalTable(data.classifications);
                rebuildParentCheckboxes(data.classifications);
            } else {
                modalTbody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding: 20px 0; color:var(--accent-red);">Failed to load classifications.</td></tr>';
            }
        })
        .catch(err => {
            console.error(err);
            modalTbody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding: 20px 0; color:var(--accent-red);">Error loading classifications.</td></tr>';
        });
    }

    function rebuildModalTable(classifications) {
        if (!classifications || classifications.length === 0) {
            modalTbody.innerHTML = '<tr><td colspan="3" class="table-empty-state" style="padding: 20px 0;">📭 No classifications found.</td></tr>';
            return;
        }

        modalTbody.innerHTML = '';
        classifications.forEach(cl => {
            const tr = document.createElement('tr');
            
            // Name badge
            const tdName = document.createElement('td');
            tdName.innerHTML = `<span class="badge-tag-classification classification-${cl.color}">${cl.name}</span>`;
            
            // Status badge
            const tdStatus = document.createElement('td');
            const statusClass = cl.status === 'active' ? '' : 'badge-stock-danger';
            tdStatus.innerHTML = `<span class="staff-status-badge ${statusClass}" style="padding:4px 8px; border-radius:6px; font-weight:600;">${cl.status.charAt(0).toUpperCase() + cl.status.slice(1)}</span>`;
            
            // Action buttons (Icon only)
            const tdActions = document.createElement('td');
            tdActions.style.textAlign = 'right';
            tdActions.innerHTML = `
                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" onclick="editClassification(${cl.id}, '${cl.name.replace(/'/g, "\\'")}', '${cl.color}', '${cl.status}')" class="btn-csv-export" style="padding: 6px 10px; font-size: 0.85rem;" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" onclick="deleteClassification(${cl.id})" class="btn-csv-export" style="background: rgba(200, 75, 49, 0.08); color: #C84B31; border: 1px solid rgba(200, 75, 49, 0.15); padding: 6px 10px; font-size: 0.85rem; cursor: pointer;" title="Delete">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            
            tr.appendChild(tdName);
            tr.appendChild(tdStatus);
            tr.appendChild(tdActions);
            modalTbody.appendChild(tr);
        });
    }

    function rebuildParentCheckboxes(classifications) {
        const parentList = document.getElementById('parent-classifications-list');
        if (!parentList) return;

        // Remember currently checked IDs
        const checkedIds = new Set(
            Array.from(parentList.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value)
        );

        parentList.innerHTML = '';
        
        const activeClassifications = classifications.filter(cl => cl.status === 'active');
        
        if (activeClassifications.length === 0) {
            parentList.innerHTML = '<span class="text-author-muted" style="font-size:0.85rem;">No active classifications available. Add one using +</span>';
            return;
        }

        activeClassifications.forEach(cl => {
            const label = document.createElement('label');
            label.className = 'checkbox-pill-label';
            label.style.cursor = 'pointer';
            label.style.userSelect = 'none';
            
            const isChecked = checkedIds.has(String(cl.id)) ? 'checked' : '';
            const icon = classificationIcons[cl.name] || 'fa-tag';
            
            label.innerHTML = `
                <input type="checkbox" name="classifications[]" value="${cl.id}" style="display: none;" ${isChecked}>
                <span class="pill-badge-design badge-classification-${cl.color}">
                    <i class="fa-solid ${icon}"></i> ${cl.name}
                </span>
            `;
            parentList.appendChild(label);
        });
    }

    window.editClassification = function(id, name, color, status) {
        modalClassId.value = id;
        modalClassName.value = name;
        
        const radio = modalForm.querySelector(`input[name="modal_color"][value="${color}"]`);
        if (radio) radio.checked = true;
        
        modalClassStatus.value = status;
        modalClassStatusGroup.classList.remove('display-none');
        
        modalFormTitle.innerHTML = `<i class="fa-solid fa-pen-to-square"></i> Edit Classification`;
        modalCancelEditBtn.style.display = 'inline-block';
        modalSubmitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Update';
    };

    window.resetModalForm = function() {
        modalClassId.value = '';
        modalForm.reset();
        
        const defaultRadio = modalForm.querySelector('input[name="modal_color"][value="green"]');
        if (defaultRadio) defaultRadio.checked = true;
        
        modalClassStatusGroup.classList.add('display-none');
        modalFormTitle.innerHTML = `<i class="fa-solid fa-circle-plus"></i> Add New Classification`;
        modalCancelEditBtn.style.display = 'none';
        modalSubmitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Save';
    };

    modalForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = modalClassId.value;
        const nameVal = modalClassName.value.trim();
        const colorRadio = modalForm.querySelector('input[name="modal_color"]:checked');
        const colorVal = colorRadio ? colorRadio.value : 'green';
        const statusVal = modalClassStatus.value;
        
        if (!nameVal) return;
        
        const isEdit = !!id;
        const url = isEdit ? `/admin/classifications/${id}` : '/admin/classifications';
        const method = isEdit ? 'PUT' : 'POST';
        
        modalSubmitBtn.disabled = true;
        modalSubmitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                name: nameVal,
                color: colorVal,
                status: statusVal
            })
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(errData => {
                    throw new Error(errData.message || 'Validation error');
                });
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Saved successfully!');
                resetModalForm();
                loadModalClassifications();
            } else {
                alert('Error: ' + (data.message || 'Failed to save classification.'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error: ' + err.message);
        })
        .finally(() => {
            modalSubmitBtn.disabled = false;
            modalSubmitBtn.innerHTML = isEdit ? '<i class="fa-solid fa-paper-plane"></i> Update' : '<i class="fa-solid fa-paper-plane"></i> Save';
        });
    });

    window.deleteClassification = function(id) {
        if (!confirm('Are you sure you want to delete this classification? Books linked to this tag will be detached.')) {
            return;
        }
        
        fetch(`/admin/classifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Deleted successfully!');
                if (modalClassId.value == id) {
                    resetModalForm();
                }
                loadModalClassifications();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete classification.'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to delete classification due to network or server error.');
        });
    };

});
