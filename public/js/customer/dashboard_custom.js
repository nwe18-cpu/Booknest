// 1. Chart heights animation on load
function initChartAnimations() {
    setTimeout(() => {
        document.querySelectorAll('.chart-bar-fill').forEach(fill => {
            const finalHeight = fill.getAttribute('data-height');
            fill.style.height = finalHeight;
        });
    }, 300);
    
    // Initialize Cozy Music UI title
    if (typeof updateMusicTitle === 'function') {
        updateMusicTitle();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChartAnimations);
} else {
    initChartAnimations();
}

// 2. Category filters
function filterCategory(catId, btn) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    const books = document.querySelectorAll('.book-container-3d, .browse-book-card');
    books.forEach(book => {
        const bookCatsStr = book.getAttribute('data-categories') || '';
        const bookCats = bookCatsStr ? bookCatsStr.split(',') : [];
        if (catId === 'all' || bookCats.includes(catId.toString())) {
            book.classList.remove('hide-card');
        } else {
            book.classList.add('hide-card');
        }
    });
}

// 3. Search filter
function filterSearch() {
    const query = document.getElementById('search-input').value.toLowerCase();
    const books = document.querySelectorAll('.book-container-3d, .browse-book-card');
    
    books.forEach(book => {
        const title = book.getAttribute('data-title') || '';
        const author = book.getAttribute('data-author') || '';
        
        if (title.includes(query) || author.includes(query)) {
            book.classList.remove('hide-card');
        } else {
            book.classList.add('hide-card');
        }
    });
}

// 4. Modal management
function openBookDetail(id, title, author, desc, price, stock, totalPages, colorClass, progressPercent, curPage, bookmarkedPage, pagesContent, image, pdfUrl, hasDownloaded, isWishlisted) {
    document.getElementById('modal-title').innerText = title;
    document.getElementById('modal-author').innerText = author;
    document.getElementById('modal-desc').innerText = desc;
    document.getElementById('modal-pages').innerText = totalPages;
    document.getElementById('modal-price').innerText = parseFloat(price).toLocaleString() + ' Ks'; // Translated to English "Ks" too
    document.getElementById('modal-progress').innerText = progressPercent + '%';
    
    // Set bookmark text in modal
    const bookmarkEl = document.getElementById('modal-bookmark');
    if (bookmarkEl) {
        if (bookmarkedPage && bookmarkedPage !== 'null' && bookmarkedPage !== '') {
            bookmarkEl.innerText = 'Page ' + bookmarkedPage;
            bookmarkEl.style.color = 'var(--brand-gold)';
            bookmarkEl.style.fontWeight = '700';
        } else {
            bookmarkEl.innerText = 'None';
            bookmarkEl.style.color = '';
            bookmarkEl.style.fontWeight = '';
        }
    }
    
    // Reapply coloring classes and image background
    const modalBook3D = document.getElementById('modal-book-3d');
    if (modalBook3D) {
        modalBook3D.className = 'book-3d ' + colorClass;
        const coverFront = modalBook3D.querySelector('.book-cover-front');
        const titleBox = modalBook3D.querySelector('.book-cover-title-box');
        const badge = modalBook3D.querySelector('.book-cover-badge');
        
        if (image) {
            coverFront.style.backgroundImage = "url('" + image + "')";
            coverFront.style.backgroundSize = "cover";
            coverFront.style.backgroundPosition = "center";
            if (titleBox) titleBox.style.display = 'none';
            if (badge) badge.style.display = 'none';
        } else {
            coverFront.style.backgroundImage = "";
            if (titleBox) titleBox.style.display = 'flex';
            if (badge) badge.style.display = 'flex';
        }
    }
    document.getElementById('modal-book-cover-title').innerText = title;
    document.getElementById('modal-book-cover-author').innerText = author;

    // Trigger button visibility based on whether the customer owns the book
    const readBtn = document.getElementById('btn-read-trigger');
    const resumeBtn = document.getElementById('btn-resume-bookmark');
    const buyBtn = document.getElementById('btn-add-to-cart-modal');
    const removeBtn = document.getElementById('btn-remove-library');
    
    if (hasDownloaded) {
        if (readBtn) readBtn.classList.remove('display-none');
        if (buyBtn) buyBtn.classList.add('display-none');
        if (removeBtn) {
            removeBtn.classList.remove('display-none');
            removeBtn.setAttribute('data-id', id);
        }
        
        readBtn.onclick = function() {
            closeBookDetail();
            openReader(id, title, author, totalPages, pagesContent, parseInt(curPage), pdfUrl, bookmarkedPage ? parseInt(bookmarkedPage) : null);
        };

        if (resumeBtn) {
            if (bookmarkedPage && bookmarkedPage !== 'null' && bookmarkedPage !== '') {
                resumeBtn.classList.remove('display-none');
                resumeBtn.onclick = function() {
                    closeBookDetail();
                    openReader(id, title, author, totalPages, pagesContent, parseInt(bookmarkedPage), pdfUrl, parseInt(bookmarkedPage));
                };
            } else {
                resumeBtn.classList.add('display-none');
            }
        }
    } else {
        if (readBtn) readBtn.classList.add('display-none');
        if (resumeBtn) resumeBtn.classList.add('display-none');
        if (removeBtn) removeBtn.classList.add('display-none');
        if (buyBtn) {
            buyBtn.classList.remove('display-none');
            buyBtn.setAttribute('data-id', id);
            if (parseInt(stock) > 0) {
                buyBtn.disabled = false;
                buyBtn.innerHTML = '<i class="fa-solid fa-cart-plus"></i> Buy Book';
            } else {
                buyBtn.disabled = true;
                buyBtn.innerHTML = '<i class="fa-solid fa-xmark"></i> Out of Stock';
            }
        }
    }

    // Set Wishlist button state in modal
    const wishlistBtn = document.getElementById('btn-wishlist-modal');
    if (wishlistBtn) {
        wishlistBtn.setAttribute('data-id', id);
        if (isWishlisted) {
            wishlistBtn.classList.add('active');
            wishlistBtn.innerHTML = '<i class="fa-solid fa-heart"></i> Wishlisted';
        } else {
            wishlistBtn.classList.remove('active');
            wishlistBtn.innerHTML = '<i class="fa-regular fa-heart"></i> Wishlist';
        }
    }

    document.getElementById('detail-modal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeBookDetail(e) {
    document.getElementById('detail-modal').classList.remove('active');
    document.body.style.overflow = '';
}

function openReviewsModal(id, title, author) {
    currentDetailBookId = id;
    document.getElementById('reviews-modal-title').innerText = title;
    document.getElementById('reviews-modal-author').innerText = 'By ' + author;
    
    resetReviewForm();
    loadBookReviews(id);
    
    const modal = document.getElementById('reviews-modal');
    if (modal) modal.classList.add('active');
}

function closeReviewsModal() {
    const modal = document.getElementById('reviews-modal');
    if (modal) modal.classList.remove('active');
}

function openReviewsModalFromDashboard() {
    const wishlistBtn = document.getElementById('btn-wishlist-modal');
    if (!wishlistBtn) return;
    
    const id = wishlistBtn.getAttribute('data-id');
    const title = document.getElementById('modal-title').innerText;
    const author = document.getElementById('modal-author').innerText.replace(/^By\s+/i, '');
    
    openReviewsModal(id, title, author);
}

function openBookDetailFromElement(el) {
    const id = el.getAttribute('data-id');
    const title = el.getAttribute('data-title-raw');
    const author = el.getAttribute('data-author-raw');
    const desc = el.getAttribute('data-desc');
    const price = el.getAttribute('data-price');
    const stock = el.getAttribute('data-stock');
    const totalPages = el.getAttribute('data-pages');
    const colorClass = el.getAttribute('data-color-class');
    const progressPercent = el.getAttribute('data-progress-percent');
    const curPage = el.getAttribute('data-current-page');
    const bookmarkedPage = el.getAttribute('data-bookmarked-page');
    const pagesContent = el.getAttribute('data-pages-content');
    const image = el.getAttribute('data-image');
    const pdfUrl = el.getAttribute('data-pdf-file');
    const hasDownloaded = el.getAttribute('data-downloaded') === 'true';
    const isWishlisted = el.getAttribute('data-wishlisted') === 'true';
    
    openBookDetail(id, title, author, desc, price, stock, totalPages, colorClass, progressPercent, curPage, bookmarkedPage, pagesContent, image, pdfUrl, hasDownloaded, isWishlisted);
}

// 5. HTML5 Audio Cozy Music Player
const cozyTracks = [
    { title: "Gymnopedie No. 1 (Piano)", file: "/audio/cozy-music-1.mp3" },
    { title: "Gymnopedie No. 3 (Piano)", file: "/audio/cozy-music-2.mp3" },
    { title: "Morning (Acoustic)", file: "/audio/cozy-music-3.mp3" },
    { title: "Windswept (Ambient)", file: "/audio/cozy-music-4.mp3" },
    { title: "Sovereign (Soft Ambient)", file: "/audio/cozy-music-5.mp3" }
];

let currentTrackIndex = 0;
let isMusicPlaying = false;
let isMusicLooping = true;
let cozyAudio = null;

function initCozyMusic() {
    if (cozyAudio) return;
    
    cozyAudio = new Audio();
    cozyAudio.src = cozyTracks[currentTrackIndex].file;
    cozyAudio.loop = isMusicLooping;
    
    // Set initial volume from slider
    const volumeSlider = document.getElementById('music-volume');
    if (volumeSlider) {
        cozyAudio.volume = volumeSlider.value;
    } else {
        cozyAudio.volume = 0.3;
    }

    // Ended listener for loop/playlist flow
    cozyAudio.addEventListener('ended', function() {
        if (!isMusicLooping) {
            playNextTrack(true); // Auto play next track
        } else {
            cozyAudio.play().catch(err => console.log("Audio play failed on end: ", err));
        }
    });

    // Update title in UI
    updateMusicTitle();
}

function updateMusicTitle() {
    const titleEl = document.getElementById('music-track-title');
    if (titleEl) {
        titleEl.textContent = cozyTracks[currentTrackIndex].title;
    }
}

function updateMusicUI() {
    const toggleBtn = document.getElementById('music-toggle');
    const loopBtn = document.getElementById('music-loop');
    const musicIcon = document.querySelector('.music-widget-header .music-icon');

    if (toggleBtn) {
        if (isMusicPlaying) {
            toggleBtn.innerHTML = '<i class="fa-solid fa-pause"></i> Pause';
            toggleBtn.classList.add('playing');
        } else {
            toggleBtn.innerHTML = '<i class="fa-solid fa-play"></i> Play';
            toggleBtn.classList.remove('playing');
        }
    }

    if (loopBtn) {
        if (isMusicLooping) {
            loopBtn.classList.add('active');
        } else {
            loopBtn.classList.remove('active');
        }
    }

    if (musicIcon) {
        if (isMusicPlaying) {
            musicIcon.classList.add('playing');
        } else {
            musicIcon.classList.remove('playing');
        }
    }
}

function toggleMusicSound() {
    initCozyMusic();
    
    if (isMusicPlaying) {
        cozyAudio.pause();
        isMusicPlaying = false;
    } else {
        cozyAudio.play().catch(err => console.log("Audio play failed: ", err));
        isMusicPlaying = true;
    }
    
    updateMusicUI();
}

function loadTrack(index, autoPlay = false) {
    initCozyMusic();
    currentTrackIndex = (index + cozyTracks.length) % cozyTracks.length;
    
    const wasPlaying = isMusicPlaying;
    cozyAudio.src = cozyTracks[currentTrackIndex].file;
    updateMusicTitle();
    
    if (wasPlaying || autoPlay) {
        cozyAudio.play().catch(err => console.log("Audio play failed on track load: ", err));
        isMusicPlaying = true;
    } else {
        isMusicPlaying = false;
    }
    updateMusicUI();
}

function playPrevTrack() {
    loadTrack(currentTrackIndex - 1);
}

function playNextTrack(autoPlay = false) {
    loadTrack(currentTrackIndex + 1, autoPlay);
}

function toggleMusicLoop() {
    initCozyMusic();
    isMusicLooping = !isMusicLooping;
    cozyAudio.loop = isMusicLooping;
    updateMusicUI();
}

function setMusicVolume(vol) {
    initCozyMusic();
    if (cozyAudio) {
        cozyAudio.volume = vol;
    }
}

// 6. SYNTHESIZED FLIP AUDIO SOUND
function playFlipSound() {
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        const ctx = new AudioContext();
        
        const osc = ctx.createOscillator();
        const gainNode = ctx.createGain();
        
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(140, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
        
        gainNode.gain.setValueAtTime(0.08, ctx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.22);
        
        osc.connect(gainNode);
        gainNode.connect(ctx.destination);
        
        osc.start();
        osc.stop(ctx.currentTime + 0.25);
    } catch (e) {
        console.error("Synthesized Audio error", e);
    }
}

// 7. E-BOOK READER FLOWS
let currentReaderBookId = null;
let readerPages = [];
let totalReaderPages = 0;
let activePagePointer = 1; // 1-indexed (left page is activePagePointer, right is activePagePointer+1)
let pdfDoc = null;
let isPdfMode = false;
let pdfUrlGlobal = null;
let pdfZoomLevel = 1.0;
let globalVirtualPages = [];
let currentBookmarkedPage = null;
let activeRenderTasks = {};

function buildVirtualPages() {
    if (!pdfDoc) return Promise.resolve();
    
    const promises = [];
    for (let p = 1; p <= pdfDoc.numPages; p++) {
        promises.push(pdfDoc.getPage(p));
    }
    
    return Promise.all(promises).then(pages => {
        const leftContainer = document.getElementById('page-l-content');
        const rect = leftContainer.getBoundingClientRect();
        const containerWidth = rect.width || 386;
        
        let virtualPages = [];
        let virtualPageNum = 1;
        
        pages.forEach((page, index) => {
            const pageNum = index + 1;
            const viewport = page.getViewport({ scale: 1.0 });
            
            // Detect landscape layout containing side-by-side sheets (width > height * 1.22)
            const isLandscape = viewport.width > viewport.height * 1.22;
            
            if (isLandscape) {
                // Left half of landscape page
                virtualPages.push({
                    pdfPageNum: pageNum,
                    isLeftHalf: true,
                    isRightHalf: false,
                    scale: null,
                    virtualPageNum: virtualPageNum++
                });
                // Right half of landscape page
                virtualPages.push({
                    pdfPageNum: pageNum,
                    isLeftHalf: false,
                    isRightHalf: true,
                    scale: null,
                    virtualPageNum: virtualPageNum++
                });
            } else {
                // Normal portrait page
                virtualPages.push({
                    pdfPageNum: pageNum,
                    isLeftHalf: false,
                    isRightHalf: false,
                    scale: null,
                    virtualPageNum: virtualPageNum++
                });
            }
        });
        
        globalVirtualPages = virtualPages;
        totalReaderPages = virtualPages.length;
    });
}

function getPartSuffix(pdfPageNum, offsetY) {
    const parts = globalVirtualPages.filter(vp => vp.pdfPageNum === pdfPageNum);
    if (parts.length <= 1) return "";
    const index = parts.findIndex(vp => vp.offsetY === offsetY);
    return ` (Part ${index + 1})`;
}

function openReader(id, name, author, totalPages, pagesContent, savedPage, pdfUrl, bookmarkedPage) {
    currentReaderBookId = id;
    currentBookmarkedPage = bookmarkedPage ? parseInt(bookmarkedPage) : null;
    activePagePointer = Math.max(1, parseInt(savedPage));
    
    // Auto-Play Music setting trigger
    if (typeof booknestSettings !== 'undefined' && booknestSettings.autoMusic && !isMusicPlaying) {
        initCozyMusic();
        if (typeof cozyAudio !== 'undefined' && cozyAudio) {
            cozyAudio.play().then(() => {
                isMusicPlaying = true;
                if (typeof updateMusicUI === 'function') updateMusicUI();
            }).catch(err => console.log("Auto audio play blocked/failed: ", err));
        }
    }

    document.getElementById('reader-book-name').innerText = name;
    document.getElementById('reader-book-author').innerText = author;

    // Fetch and populate reviews and comments stats dynamically
    const avgRatingSpan = document.getElementById('reader-avg-rating');
    const commentsCountSpan = document.getElementById('reader-comments-count');
    
    if (avgRatingSpan) avgRatingSpan.innerText = '0.0';
    if (commentsCountSpan) commentsCountSpan.innerText = '0';
    
    fetch(`/store/books/${id}/reviews`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (avgRatingSpan) avgRatingSpan.innerText = parseFloat(data.average_rating).toFixed(1);
                if (commentsCountSpan) commentsCountSpan.innerText = data.total_reviews;
            }
        })
        .catch(err => console.error("Failed to load reader book reviews: ", err));

    // Set Wattpad Left Sidebar: Author profile
    const authorAvatarImg = document.getElementById('reader-author-avatar');
    if (authorAvatarImg) {
        authorAvatarImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(author)}&background=f1e4d8&color=5c3a21&bold=true`;
    }
    const authorNameEl = document.getElementById('reader-author-name');
    if (authorNameEl) {
        authorNameEl.innerText = author;
    }

    // Set Wattpad Center Title
    const bookTitleEl = document.getElementById('reader-chapter-title');
    if (bookTitleEl) {
        bookTitleEl.innerText = name;
    }

    // Set Wattpad Right Sidebar: Recommended Books from Bookshelf
    const recBooksList = document.getElementById('reader-rec-books-list');
    if (recBooksList) {
        recBooksList.innerHTML = ''; // reset list
        
        // Find other books on the bookshelf
        const allBooksOnShelf = Array.from(document.querySelectorAll('.shelf-book-premium'));
        const otherBooks = allBooksOnShelf.filter(el => el.getAttribute('data-id') != id);
        
        // Take up to 4 books
        const selectedRecs = otherBooks.slice(0, 4);
        
        if (selectedRecs.length > 0) {
            selectedRecs.forEach(el => {
                const recId = el.getAttribute('data-id');
                const recTitle = el.getAttribute('data-title-raw');
                const recAuthor = el.getAttribute('data-author-raw');
                const recImage = el.getAttribute('data-image');
                const recColorClass = el.getAttribute('data-color-class') || 'book-color-1';
                
                const itemDiv = document.createElement('div');
                itemDiv.className = 'rec-book-item';
                itemDiv.setAttribute('onclick', `closeReader(); openBookDetailFromElement(document.querySelector('.shelf-book-premium[data-id="${recId}"]'));`);
                
                let coverStyle = '';
                if (recImage) {
                    coverStyle = `style="background-image: url('${recImage}');"`;
                }
                
                itemDiv.innerHTML = `
                    <div class="rec-book-cover ${recImage ? '' : recColorClass}" ${coverStyle}></div>
                    <div class="rec-book-meta">
                        <h5 class="rec-book-title" title="${recTitle}">${recTitle}</h5>
                        <p class="rec-book-author">By ${recAuthor}</p>
                    </div>
                `;
                recBooksList.appendChild(itemDiv);
            });
        } else {
            recBooksList.innerHTML = '<p class="text-mute" style="font-size:0.8rem;">No recommendations available.</p>';
        }
    }

    // Reset state
    pdfDoc = null;
    
    // Show text loader container by default
    document.querySelectorAll('.reader-text-container').forEach(c => c.style.display = 'block');
    document.querySelectorAll('.pdf-page-canvas').forEach(c => c.style.display = 'none');
    document.getElementById('page-l-content').querySelector('p').innerText = "Loading book contents...";

    const readerOverlay = document.getElementById('reader-overlay');
    const zoomControls = document.getElementById('reader-zoom-controls');

    if (pdfUrl) {
        isPdfMode = true;
        pdfUrlGlobal = pdfUrl;
        if (readerOverlay) readerOverlay.classList.add('pdf-mode');
        if (zoomControls) zoomControls.style.display = 'flex';
        pdfZoomLevel = 1.0;
        updateZoomUI();
        
        // Load PDF using PDF.js via ArrayBuffer to completely bypass download manager interception
        fetch(pdfUrlGlobal)
            .then(response => {
                if (!response.ok) throw new Error("Failed to fetch PDF data");
                return response.arrayBuffer();
            })
            .then(arrayBuffer => {
                const loadingTask = pdfjsLib.getDocument({ data: arrayBuffer });
                return loadingTask.promise;
            })
            .then(function(pdf) {
                pdfDoc = pdf;
                
                buildVirtualPages().then(() => {
                    if (activePagePointer > totalReaderPages) {
                        activePagePointer = 1;
                    } else {
                        let foundIndex = globalVirtualPages.findIndex(vp => vp.pdfPageNum >= activePagePointer);
                        activePagePointer = foundIndex !== -1 ? foundIndex + 1 : 1;
                    }
                    if (activePagePointer % 2 === 0) activePagePointer--;
                    renderReaderPages();
                });
            }).catch(err => {
                console.error("Error loading PDF: ", err);
                // Fallback to text mode
                isPdfMode = false;
                if (readerOverlay) readerOverlay.classList.remove('pdf-mode');
                if (zoomControls) zoomControls.style.display = 'none';
                totalReaderPages = parseInt(totalPages);
                readerPages = typeof pagesContent === 'string' ? JSON.parse(pagesContent) : pagesContent;
                renderReaderPages();
            });
    } else {
        isPdfMode = false;
        pdfUrlGlobal = null;
        if (readerOverlay) readerOverlay.classList.remove('pdf-mode');
        if (zoomControls) zoomControls.style.display = 'none';
        totalReaderPages = parseInt(totalPages);
        readerPages = typeof pagesContent === 'string' ? JSON.parse(pagesContent) : pagesContent;
        renderReaderPages();
    }

    if (readerOverlay) readerOverlay.classList.add('active');
}

function closeReader() {
    const readerOverlay = document.getElementById('reader-overlay');
    if (readerOverlay) {
        readerOverlay.classList.remove('active');
        readerOverlay.classList.remove('pdf-mode');
    }
    const zoomControls = document.getElementById('reader-zoom-controls');
    if (zoomControls) zoomControls.style.display = 'none';

    // Cancel active render tasks
    for (const canvasId in activeRenderTasks) {
        if (activeRenderTasks[canvasId]) {
            try {
                activeRenderTasks[canvasId].cancel();
            } catch (e) {}
        }
    }
    activeRenderTasks = {};

    // Clear and reset canvas memory dimensions to immediately reclaim GPU VRAM
    const canvases = ['canvas-page-l', 'canvas-page-r', 'canvas-page-flip-f', 'canvas-page-flip-b'];
    canvases.forEach(id => {
        const canvas = document.getElementById(id);
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            canvas.width = 0;
            canvas.height = 0;
            canvas.style.width = '0px';
            canvas.style.height = '0px';
        }
    });

    if (pdfDoc) {
        try {
            pdfDoc.cleanup();
            pdfDoc.destroy();
        } catch (e) {}
        pdfDoc = null;
    }

    // Auto reload page to reflect fresh statistics
    location.reload();
}

function zoomIn() {
    if (pdfZoomLevel < 4.0) {
        const currentVP = globalVirtualPages[activePagePointer - 1];
        const currentPhysicalPage = currentVP ? currentVP.pdfPageNum : 1;
        
        pdfZoomLevel = parseFloat((pdfZoomLevel + 0.2).toFixed(2));
        updateZoomUI();
        
        buildVirtualPages().then(() => {
            let foundIndex = globalVirtualPages.findIndex(vp => vp.pdfPageNum >= currentPhysicalPage);
            activePagePointer = foundIndex !== -1 ? foundIndex + 1 : 1;
            if (activePagePointer % 2 === 0) activePagePointer--;
            renderReaderPages();
        });
    }
}

function zoomOut() {
    if (pdfZoomLevel > 0.4) {
        const currentVP = globalVirtualPages[activePagePointer - 1];
        const currentPhysicalPage = currentVP ? currentVP.pdfPageNum : 1;
        
        pdfZoomLevel = parseFloat((pdfZoomLevel - 0.2).toFixed(2));
        updateZoomUI();
        
        buildVirtualPages().then(() => {
            let foundIndex = globalVirtualPages.findIndex(vp => vp.pdfPageNum >= currentPhysicalPage);
            activePagePointer = foundIndex !== -1 ? foundIndex + 1 : 1;
            if (activePagePointer % 2 === 0) activePagePointer--;
            renderReaderPages();
        });
    }
}

function zoomReset() {
    const currentVP = globalVirtualPages[activePagePointer - 1];
    const currentPhysicalPage = currentVP ? currentVP.pdfPageNum : 1;
    
    pdfZoomLevel = 1.0;
    updateZoomUI();
    
    buildVirtualPages().then(() => {
        let foundIndex = globalVirtualPages.findIndex(vp => vp.pdfPageNum >= currentPhysicalPage);
        activePagePointer = foundIndex !== -1 ? foundIndex + 1 : 1;
        if (activePagePointer % 2 === 0) activePagePointer--;
        renderReaderPages();
    });
}

function updateZoomUI() {
    const percentEl = document.getElementById('zoom-percent');
    if (percentEl) {
        percentEl.innerText = Math.round(pdfZoomLevel * 100) + '%';
    }
}

function renderPageOnCanvas(pageNum, canvasId, isLeftHalf = false, isRightHalf = false, customScale = null) {
    return new Promise((resolve, reject) => {
        // Cancel existing render task for this canvas if active
        if (activeRenderTasks[canvasId]) {
            try {
                activeRenderTasks[canvasId].cancel();
            } catch (e) {}
            activeRenderTasks[canvasId] = null;
        }

        if (!pdfDoc || pageNum < 1 || pageNum > pdfDoc.numPages) {
            // Clear canvas and reset width/height to release GPU buffer memory
            const canvas = document.getElementById(canvasId);
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                canvas.width = 0;
                canvas.height = 0;
                canvas.style.width = '0px';
                canvas.style.height = '0px';
            }
            resolve();
            return;
        }

        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            resolve();
            return;
        }

        const container = canvas.parentElement;
        const rect = container.getBoundingClientRect();
        
        pdfDoc.getPage(pageNum).then(function(page) {
            const ctx = canvas.getContext('2d');
            
            // Get original viewport scale
            let viewport = page.getViewport({ scale: 1.0 });
            
            // Use custom scale if provided, otherwise compute base scale
            let scale = customScale;
            if (!scale) {
                const containerWidth = rect.width || 450;
                if (isLeftHalf || isRightHalf) {
                    // Fit half the page width to the reading container width!
                    scale = (containerWidth / (viewport.width / 2)) * 0.96 * pdfZoomLevel;
                } else {
                    scale = (containerWidth / viewport.width) * 0.96 * pdfZoomLevel;
                }
            }
            
            viewport = page.getViewport({ scale: scale });

            // High DPI resolution scaling
            const dpr = window.devicePixelRatio || 1;
            
            if (isLeftHalf || isRightHalf) {
                canvas.width = (viewport.width / 2) * dpr;
                canvas.height = viewport.height * dpr;
                canvas.style.width = (viewport.width / 2) + 'px';
                canvas.style.height = viewport.height + 'px';
                
                ctx.scale(dpr, dpr);
                
                if (isRightHalf) {
                    ctx.translate(-viewport.width / 2, 0);
                }
            } else {
                canvas.width = viewport.width * dpr;
                canvas.height = viewport.height * dpr;
                canvas.style.width = viewport.width + 'px';
                canvas.style.height = viewport.height + 'px';
                ctx.scale(dpr, dpr);
            }
            
            // Apply relative flow layout mapping to allow vertical expansion
            canvas.style.position = 'relative';
            canvas.style.top = '0px';
            canvas.style.left = 'auto';
            canvas.style.transform = 'none';

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            const renderTask = page.render(renderContext);
            activeRenderTasks[canvasId] = renderTask;

            renderTask.promise.then(() => {
                // Free font and image resources from page object once drawing is complete
                try {
                    page.cleanup();
                } catch (e) {}
                activeRenderTasks[canvasId] = null;
                resolve();
            }).catch(err => {
                activeRenderTasks[canvasId] = null;
                if (err.name === 'RenderingCancelledException' || err.message === 'Rendering cancelled, closed or replaced.') {
                    // Catch cancellation gracefully without rejecting the promise chain
                    resolve();
                } else {
                    console.error("Render failed: ", err);
                    reject(err);
                }
            });
        }).catch(err => {
            console.error("Get page failed: ", err);
            reject(err);
        });
    });
}

function renderReaderPages() {
    if (isPdfMode) {
        // Hide text and show canvases
        document.querySelectorAll('.reader-text-container').forEach(c => c.style.display = 'none');
        document.querySelectorAll('.pdf-page-canvas').forEach(c => c.style.display = 'block');

        const physicalTotalPages = pdfDoc ? pdfDoc.numPages : totalReaderPages;

        // Draw left page
        const leftVP = globalVirtualPages[activePagePointer - 1];
        if (leftVP) {
            // Update Wattpad episode chapter & title
            const episodeNumEl = document.getElementById('reader-episode-num');
            if (episodeNumEl) {
                let label = `Page ${leftVP.pdfPageNum}`;
                if (leftVP.isLeftHalf) label += " (Left Half)";
                if (leftVP.isRightHalf) label += " (Right Half)";
                episodeNumEl.innerText = label;
            }
            
            renderPageOnCanvas(leftVP.pdfPageNum, 'canvas-page-l', leftVP.isLeftHalf, leftVP.isRightHalf, leftVP.scale);
        }

        // Controls enabling
        const step = 1;
        document.getElementById('btn-prev-page').disabled = (activePagePointer <= 1);
        document.getElementById('btn-next-page').disabled = (activePagePointer + step > totalReaderPages);

        // Progress calculate
        const currentPageRead = leftVP ? leftVP.pdfPageNum : 1;
        const progressPercent = Math.round((currentPageRead / physicalTotalPages) * 100);

        document.getElementById('page-indicator-text').innerText = `Page ${currentPageRead} of ${physicalTotalPages} (${progressPercent}%)`;
        document.getElementById('reader-progress-fill-bar').style.width = progressPercent + '%';

        // Save progress to database
        saveProgressToDatabase(currentReaderBookId, currentPageRead);

    } else {
        // Fallback text based mode
        document.querySelectorAll('.reader-text-container').forEach(c => c.style.display = 'block');
        document.querySelectorAll('.pdf-page-canvas').forEach(c => c.style.display = 'none');

        const leftPage = readerPages.find(p => p.page == activePagePointer);

        if (leftPage) {
            const episodeNumEl = document.getElementById('reader-episode-num');
            if (episodeNumEl) episodeNumEl.innerText = `Chapter ${leftPage.page}`;
            
            const chapterTitleEl = document.getElementById('reader-chapter-title');
            if (chapterTitleEl) chapterTitleEl.innerText = leftPage.title;

            document.getElementById('page-l-content').querySelector('.reader-text-container').innerHTML = `<p>${leftPage.content}</p>`;
        }

        const step = 1;
        document.getElementById('btn-prev-page').disabled = (activePagePointer <= 1);
        document.getElementById('btn-next-page').disabled = (activePagePointer + step > totalReaderPages);

        const currentPageRead = leftPage ? leftPage.page : 1;
        const progressPercent = Math.round((currentPageRead / totalReaderPages) * 100);

        document.getElementById('page-indicator-text').innerText = `Page ${currentPageRead} of ${totalReaderPages} (${progressPercent}%)`;
        document.getElementById('reader-progress-fill-bar').style.width = progressPercent + '%';

        saveProgressToDatabase(currentReaderBookId, currentPageRead);
    }

    // Refresh active page bookmark state and show/hide "Go to Bookmark" quick jump link
    let currentPageRead = 1;
    let isBookmarkedVisible = false;

    if (isPdfMode) {
        const leftVP = globalVirtualPages[activePagePointer - 1];
        currentPageRead = leftVP ? leftVP.pdfPageNum : 1;
        isBookmarkedVisible = currentBookmarkedPage && (leftVP && leftVP.pdfPageNum === currentBookmarkedPage);
    } else {
        const leftPage = readerPages.find(p => p.page == activePagePointer);
        currentPageRead = leftPage ? leftPage.page : 1;
        isBookmarkedVisible = currentBookmarkedPage && (leftPage && leftPage.page === currentBookmarkedPage);
    }

    const bookmarkBtn = document.getElementById('btn-reader-bookmark');
    if (bookmarkBtn) {
        if (currentBookmarkedPage && currentBookmarkedPage === currentPageRead) {
            bookmarkBtn.innerHTML = '<i class="fa-solid fa-bookmark"></i>';
            bookmarkBtn.classList.add('bookmarked');
        } else {
            bookmarkBtn.innerHTML = '<i class="fa-regular fa-bookmark"></i>';
            bookmarkBtn.classList.remove('bookmarked');
        }
    }

    const quickJumpEl = document.getElementById('bookmark-quick-jump');
    const pageNumEl = document.getElementById('bookmark-page-num');
    if (quickJumpEl) {
        if (currentBookmarkedPage && !isBookmarkedVisible) {
            quickJumpEl.style.display = 'inline-flex';
            if (pageNumEl) pageNumEl.innerText = currentBookmarkedPage;
        } else {
            quickJumpEl.style.display = 'none';
        }
    }
}

function toggleBookmark() {
    if (!currentReaderBookId) return;

    let currentPageRead = 1;
    if (isPdfMode) {
        const leftVP = globalVirtualPages[activePagePointer - 1];
        currentPageRead = leftVP ? leftVP.pdfPageNum : 1;
    } else {
        const leftPage = readerPages.find(p => p.page == activePagePointer);
        const rightPage = readerPages.find(p => p.page == activePagePointer + 1);
        currentPageRead = rightPage ? rightPage.page : (leftPage ? leftPage.page : 1);
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const readerOverlay = document.getElementById('reader-overlay');
    if (!readerOverlay) return;

    const bookmarkUrl = readerOverlay.getAttribute('data-bookmark-url');
    if (!bookmarkUrl) return;

    const bookmarkBtn = document.getElementById('btn-reader-bookmark');
    if (bookmarkBtn) {
        bookmarkBtn.disabled = true;
    }

    fetch(bookmarkUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            item_id: currentReaderBookId,
            page: currentPageRead
        })
    })
    .then(res => res.json())
    .then(data => {
        if (bookmarkBtn) {
            bookmarkBtn.disabled = false;
        }
        if (data.success) {
            currentBookmarkedPage = data.bookmarked_page ? parseInt(data.bookmarked_page) : null;
            renderReaderPages();

            const bookShelfEl = document.querySelector(`.shelf-book-premium[data-id="${currentReaderBookId}"]`);
            if (bookShelfEl) {
                bookShelfEl.setAttribute('data-bookmarked-page', currentBookmarkedPage !== null ? currentBookmarkedPage : '');
            }
        }
    })
    .catch(err => {
        if (bookmarkBtn) {
            bookmarkBtn.disabled = false;
        }
        console.error("Failed to toggle bookmark", err);
    });
}

function jumpToBookmark() {
    if (!currentBookmarkedPage) return;

    if (isPdfMode) {
        let foundIndex = globalVirtualPages.findIndex(vp => vp.pdfPageNum >= currentBookmarkedPage);
        activePagePointer = foundIndex !== -1 ? foundIndex + 1 : 1;
    } else {
        activePagePointer = currentBookmarkedPage;
    }

    renderReaderPages();
    playFlipSound();
}

function nextPage() {
    const step = 1;
    if (activePagePointer + step <= totalReaderPages) {
        const articleCard = document.querySelector('.reader-article-card');
        if (articleCard) articleCard.style.opacity = '0.35';

        setTimeout(() => {
            activePagePointer += step;
            renderReaderPages();
            if (articleCard) articleCard.style.opacity = '1';
            const viewport = document.querySelector('.reader-book-viewport');
            if (viewport) viewport.scrollTop = 0;
        }, 200);
    }
}

function prevPage() {
    const step = 1;
    if (activePagePointer - step >= 1) {
        const articleCard = document.querySelector('.reader-article-card');
        if (articleCard) articleCard.style.opacity = '0.35';

        setTimeout(() => {
            activePagePointer -= step;
            renderReaderPages();
            if (articleCard) articleCard.style.opacity = '1';
            const viewport = document.querySelector('.reader-book-viewport');
            if (viewport) viewport.scrollTop = 0;
        }, 200);
    }
}

function saveProgressToDatabase(bookId, pageNum) {
    // Check if autoSave is disabled by user settings
    if (typeof booknestSettings !== 'undefined' && !booknestSettings.autoSave) {
        console.log("Progress auto-save is disabled by user settings.");
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const readerOverlay = document.getElementById('reader-overlay');
    if (!readerOverlay) return;

    const progressUrl = readerOverlay.getAttribute('data-progress-url');
    if (!progressUrl) return;
    
    fetch(progressUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            item_id: bookId,
            current_page: pageNum
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log("Progress saved: Page", pageNum, "status:", data.completed);
    })
    .catch(err => {
        console.error("Failed to save progress", err);
    });
}

// Re-render PDF pages on window resize to fit the expanded layout dimensions
window.addEventListener('resize', function() {
    if (isPdfMode && pdfDoc && document.getElementById('reader-overlay').classList.contains('active')) {
        const currentVP = globalVirtualPages[activePagePointer - 1];
        const currentPhysicalPage = currentVP ? currentVP.pdfPageNum : 1;
        
        buildVirtualPages().then(() => {
            let foundIndex = globalVirtualPages.findIndex(vp => vp.pdfPageNum >= currentPhysicalPage);
            activePagePointer = foundIndex !== -1 ? foundIndex + 1 : 1;
            if (activePagePointer % 2 === 0) activePagePointer--;
            renderReaderPages();
        });
    }
});

// ==========================================================================
// BOOK RATINGS AND REVIEWS SYSTEM (COMMENT STYLE)
// ==========================================================================
let currentDetailBookId = null;
let currentFormRating = 0;

function setFormRating(rating) {
    currentFormRating = rating;
    document.getElementById('form-rating-value').value = rating;
    
    const stars = document.querySelectorAll('.star-rating-interactive .star-btn');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('fa-regular');
            star.classList.add('fa-solid');
        } else {
            star.classList.remove('fa-solid');
            star.classList.add('fa-regular');
        }
    });
}

function resetReviewForm() {
    currentFormRating = 0;
    const ratingInput = document.getElementById('form-rating-value');
    if (ratingInput) ratingInput.value = 0;
    
    const commentText = document.getElementById('form-comment-text');
    if (commentText) commentText.value = "";
    
    const stars = document.querySelectorAll('.star-rating-interactive .star-btn');
    stars.forEach(star => {
        star.classList.remove('fa-solid');
        star.classList.add('fa-regular');
    });
}

function loadBookReviews(bookId) {
    currentDetailBookId = bookId;
    resetReviewForm();
    
    const listContainer = document.getElementById('modal-reviews-list');
    if (listContainer) {
        listContainer.innerHTML = '<div class="reviews-loading-placeholder"><i class="fa-solid fa-circle-notch fa-spin"></i> Loading comments and reviews...</div>';
    }

    fetch(`/store/books/${bookId}/reviews`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;
            
            // 1. Render Summary Card
            const avgRatingEl = document.getElementById('reviews-avg-rating');
            if (avgRatingEl) avgRatingEl.innerText = parseFloat(data.average_rating).toFixed(1);
            
            const avgStarsEl = document.getElementById('reviews-avg-stars');
            if (avgStarsEl) {
                let starsHtml = "";
                const fullStars = Math.floor(data.average_rating);
                const hasHalf = (data.average_rating - fullStars) >= 0.3;
                for (let i = 1; i <= 5; i++) {
                    if (i <= fullStars) {
                        starsHtml += '<i class="fa-solid fa-star"></i>';
                    } else if (i === fullStars + 1 && hasHalf) {
                        starsHtml += '<i class="fa-solid fa-star-half-stroke"></i>';
                    } else {
                        starsHtml += '<i class="fa-regular fa-star"></i>';
                    }
                }
                avgStarsEl.innerHTML = starsHtml;
            }
            
            const totalCountEl = document.getElementById('reviews-total-count');
            if (totalCountEl) {
                totalCountEl.innerText = `Based on ${data.total_reviews} ${data.total_reviews === 1 ? 'review' : 'reviews'}`;
            }
            
            // 2. Render Progress Bars Distribution
            const barsContainer = document.getElementById('reviews-bars');
            if (barsContainer) {
                let barsHtml = "";
                for (let rating = 5; rating >= 1; rating--) {
                    const count = data.distribution[rating] || 0;
                    const percent = data.total_reviews > 0 ? Math.round((count / data.total_reviews) * 100) : 0;
                    barsHtml += `
                        <div class="star-bar-row">
                            <span class="bar-star-label">${rating} <i class="fa-solid fa-star"></i></span>
                            <div class="star-progress-track">
                                <div class="star-progress-fill" style="width: ${percent}%;"></div>
                            </div>
                            <span class="bar-percent-label">${percent}%</span>
                        </div>
                    `;
                }
                barsContainer.innerHTML = barsHtml;
            }
            
            // 3. Populate existing user review if already submitted
            if (data.user_review) {
                setFormRating(data.user_review.rating);
                const commentText = document.getElementById('form-comment-text');
                if (commentText) commentText.value = data.user_review.comment || "";
                
                const submitBtn = document.querySelector('.btn-submit-comment');
                if (submitBtn) {
                    submitBtn.innerHTML = 'Update Review <i class="fa-solid fa-rotate"></i>';
                }
            } else {
                const submitBtn = document.querySelector('.btn-submit-comment');
                if (submitBtn) {
                    submitBtn.innerHTML = 'Post Review <i class="fa-solid fa-paper-plane"></i>';
                }
            }
            
            // 4. Render Reviews Comments List
            if (listContainer) {
                if (data.reviews.length === 0) {
                    listContainer.innerHTML = `
                        <div class="empty-reviews-state">
                            <i class="fa-regular fa-comment-dots"></i>
                            <p>No reviews yet. Be the first to leave a comment review!</p>
                        </div>
                    `;
                } else {
                    let reviewsHtml = "";
                    data.reviews.forEach(rev => {
                        let stars = "";
                        for (let i = 1; i <= 5; i++) {
                            stars += i <= rev.rating 
                                ? '<i class="fa-solid fa-star"></i>' 
                                : '<i class="fa-regular fa-star"></i>';
                        }
                        
                        reviewsHtml += `
                            <div class="review-comment-card">
                                <img src="${rev.customer_image}" alt="avatar" class="comment-card-avatar">
                                <div class="comment-card-body">
                                    <div class="comment-card-header">
                                        <span class="comment-author-name">${rev.customer_name}</span>
                                        <div class="comment-stars">${stars}</div>
                                        <span class="comment-time">${rev.created_at_formatted}</span>
                                    </div>
                                    <div class="comment-card-text">${escapeHtml(rev.comment || '')}</div>
                                </div>
                            </div>
                        `;
                    });
                    listContainer.innerHTML = reviewsHtml;
                }
            }
        })
        .catch(err => {
            console.error("Failed to load reviews", err);
            if (listContainer) {
                listContainer.innerHTML = '<div class="reviews-error-placeholder"><i class="fa-solid fa-triangle-exclamation"></i> Failed to load reviews.</div>';
            }
        });
}

function submitReview() {
    if (!currentDetailBookId) return;
    
    const ratingVal = parseInt(document.getElementById('form-rating-value').value);
    if (ratingVal < 1 || ratingVal > 5) {
        alert("Please select a star rating!");
        return;
    }
    
    const commentText = document.getElementById('form-comment-text').value.trim();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const submitBtn = document.querySelector('.btn-submit-comment');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Posting...';
    
    fetch(`/store/books/${currentDetailBookId}/reviews`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            rating: ratingVal,
            comment: commentText
        })
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if (data.success) {
            loadBookReviews(currentDetailBookId);
        } else {
            alert(data.message || "Failed to submit review.");
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        console.error("Error submitting review", err);
        alert("An error occurred while submitting your review.");
    });
}

function escapeHtml(text) {
    if (!text) return "";
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function toggleWishlist(itemId, element) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/store/books/${itemId}/wishlist`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 401) {
            window.location.href = '/login';
            return;
        }
        return res.json();
    })
    .then(data => {
        if (!data) return;
        if (data.success) {
            const added = data.status === 'added';
            
            // Sync all wishlist representations on the dashboard popup list
            const bookEl = document.querySelector(`.wishlist-modal-item[data-wishlist-item-id="${itemId}"]`);
            if (bookEl && !added) {
                bookEl.style.opacity = '0';
                bookEl.style.transform = 'translateX(20px)';
                bookEl.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    bookEl.remove();
                    
                    // Check if list is empty
                    const list = document.getElementById('wishlist-items-list');
                    if (list && list.querySelectorAll('.wishlist-modal-item').length === 0) {
                        list.innerHTML = `
                            <div class="wishlist-empty-state">
                                <i class="fa-regular fa-heart"></i>
                                <p>Your wishlist is empty.</p>
                            </div>
                        `;
                    }
                }, 300);
            }

            // Sync other items if they have data-wishlisted attribute
            const elements = document.querySelectorAll(`[data-id="${itemId}"]`);
            elements.forEach(el => {
                el.setAttribute('data-wishlisted', added ? 'true' : 'false');
            });

            // Update badge count
            const badge = document.querySelector('.wishlist-badge');
            if (badge) {
                const currentCount = parseInt(badge.textContent) || 0;
                badge.textContent = added ? currentCount + 1 : Math.max(0, currentCount - 1);
            }

            // Update modal wishlist button if currently open
            const modalBtn = document.getElementById('btn-wishlist-modal');
            if (modalBtn && modalBtn.getAttribute('data-id') == itemId) {
                if (added) {
                    modalBtn.classList.add('active');
                    modalBtn.innerHTML = '<i class="fa-solid fa-heart"></i> Wishlisted';
                } else {
                    modalBtn.classList.remove('active');
                    modalBtn.innerHTML = '<i class="fa-regular fa-heart"></i> Wishlist';
                }
            }
        } else {
            alert(data.message || 'Failed to toggle wishlist');
        }
    })
    .catch(err => {
        console.error('Wishlist toggle failed', err);
    });
}

function toggleWishlistFromModal(button) {
    const itemId = button.getAttribute('data-id');
    if (itemId) {
        toggleWishlist(itemId, null);
    }
}

function addToCart(itemId, quantity = 1) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/store/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            item_id: itemId,
            quantity: quantity
        })
    })
    .then(res => {
        if (res.status === 401) {
            window.location.href = '/login';
            return;
        }
        return res.json();
    })
    .then(data => {
        if (!data) return;
        if (data.success) {
            // Update global cart badge
            const badge = document.getElementById('global-cart-count');
            if (badge) {
                badge.innerText = data.total_quantity;
                badge.style.display = 'flex';
            }

            // Open the shopping cart drawer
            if (typeof window.openBooknestCart === 'function') {
                window.openBooknestCart();
            }

            // Fetch cart drawer data if drawer body is present
            const drawerBody = document.getElementById('cart-drawer-body');
            if (drawerBody) {
                fetch('/store/cart/data')
                    .then(res => res.json())
                    .then(cartData => {
                        // Render Cart Drawer
                        const totalEl = document.getElementById('cart-total-amount');
                        if (cartData.items && Object.keys(cartData.items).length > 0) {
                            let html = '';
                            for (const [id, item] of Object.entries(cartData.items)) {
                                html += `
                                    <div class="cart-item">
                                        <div class="cart-item-book-container">
                                            <div class="cart-item-book">
                                                <div class="cart-item-cover ${item.image ? 'has-cover-image' : item.cover_class}" ${item.image ? `style="background-image: url('${item.image}'); background-size: cover; background-position: center;"` : ''}>
                                                    ${item.image ? '' : `<span class="cart-item-cover-text">${escapeHtml(item.name)}</span>`}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cart-item-details">
                                            <div class="cart-item-title" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</div>
                                            <div class="cart-item-author">By ${escapeHtml(item.author || 'Unknown')}</div>
                                            <div class="cart-item-price">${parseFloat(item.price).toLocaleString()} Ks</div>
                                        </div>
                                    </div>
                                `;
                            }
                            drawerBody.innerHTML = html;
                            if (totalEl) totalEl.innerText = parseFloat(cartData.total_amount).toLocaleString() + ' Ks';
                        }
                    });
            }
        } else {
            alert(data.message || 'Failed to add item to cart');
        }
    })
    .catch(err => {
        console.error('Add to cart failed', err);
    });
}

function addToCartFromDashboardModal(button) {
    const itemId = button.getAttribute('data-id');
    if (itemId) {
        closeBookDetail();
        addToCart(itemId, 1);
    }
}

// Wishlist Modal popup triggers
function openWishlistModal() {
    const modal = document.getElementById('wishlist-modal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeWishlistModal() {
    const modal = document.getElementById('wishlist-modal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function openBookFromWishlist(el) {
    closeWishlistModal();
    openBookDetailFromElement(el);
}

// Global click event listener to show loading spinner for all "Add to Cart" actions
document.addEventListener('click', function(e) {
    const btn = e.target.closest('button, a');
    if (!btn) return;
    
    const onclickAttr = btn.getAttribute('onclick') || '';
    if (onclickAttr.includes('addToCart') || btn.classList.contains('btn-add-to-cart') || btn.classList.contains('btn-buy-book') || btn.classList.contains('btn-wishlist-buy')) {
        if (btn.classList.contains('btn-loading') || btn.disabled) return;
        
        btn.classList.add('btn-loading');
        const originalHtml = btn.innerHTML;
        btn.style.pointerEvents = 'none';
        
        // Check if button is an icon-only card button or text button
        if (btn.classList.contains('btn-card-cart') || btn.classList.contains('btn-wishlist-buy') || btn.tagName === 'A') {
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
        } else {
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Adding...';
        }
        
        // Restore button state after a safe timeout (1.2s) when server response has returned
        setTimeout(() => {
            btn.classList.remove('btn-loading');
            btn.style.pointerEvents = '';
            btn.innerHTML = originalHtml;
        }, 1200);
    }
});

// ==========================================================================
// APP SETTINGS STATE MANAGEMENT
// ==========================================================================
const settingsModal = document.getElementById('settings-modal');
const settingsPreviewBox = document.getElementById('settings-preview-box');

// Default Settings structure
let booknestSettings = {
    theme: 'light',
    fontSize: 'md',
    fontStyle: 'sans',
    autoMusic: true,
    autoSave: true
};

// Load settings from LocalStorage on initialization
function initAppSettings() {
    let saved = null;
    try {
        saved = localStorage.getItem('booknest_settings');
    } catch (e) {
        console.warn("localStorage is not accessible, using default settings.", e);
    }
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            if (parsed && typeof parsed === 'object') {
                booknestSettings = Object.assign({}, booknestSettings, parsed);
            }
        } catch (e) {
            console.error("Failed to parse saved settings, resetting defaults.", e);
        }
    }
    applySettingsToDOM();
}

// Open settings modal
function openAppSettingsModal() {
    const modal = document.getElementById('settings-modal') || settingsModal;
    if (modal) {
        // Load settings values into Form fields
        // 1. Theme active button state
        document.querySelectorAll('.theme-opt-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.classList.contains(`theme-${booknestSettings.theme}`)) {
                btn.classList.add('active');
            }
        });
        
        // 2. Select dropdowns
        const fontSizeSel = document.getElementById('setting-font-size');
        if (fontSizeSel) fontSizeSel.value = booknestSettings.fontSize;
        
        const fontStyleSel = document.getElementById('setting-font-style');
        if (fontStyleSel) fontStyleSel.value = booknestSettings.fontStyle;
        
        // 3. Toggle Checkboxes
        const autoMusicChk = document.getElementById('setting-auto-music');
        if (autoMusicChk) autoMusicChk.checked = booknestSettings.autoMusic;
        
        const autoSaveChk = document.getElementById('setting-auto-save');
        if (autoSaveChk) autoSaveChk.checked = booknestSettings.autoSave;
        
        // 4. Update preview
        updateSettingsPreview();
        
        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

// Close settings modal
function closeAppSettingsModal() {
    const modal = document.getElementById('settings-modal') || settingsModal;
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Set active theme option value inside modal
function setSettingTheme(themeName) {
    booknestSettings.theme = themeName;
    document.querySelectorAll('.theme-opt-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.classList.contains(`theme-${themeName}`)) {
            btn.classList.add('active');
        }
    });
    updateSettingsPreview();
}

// Update the live preview box state dynamically
function updateSettingsPreview() {
    const previewBox = document.getElementById('settings-preview-box') || settingsPreviewBox;
    if (previewBox) {
        // Read form state
        const fontSizeSel = document.getElementById('setting-font-size');
        const fontStyleSel = document.getElementById('setting-font-style');
        
        const fontSize = fontSizeSel ? fontSizeSel.value : 'md';
        const fontStyle = fontStyleSel ? fontStyleSel.value : 'sans';
        const theme = booknestSettings.theme || 'light';
        
        // Apply Preview Styles
        previewBox.className = 'settings-preview-box'; // reset classes
        
        // Theme styles preview
        if (theme === 'dark') {
            previewBox.style.backgroundColor = '#162c26';
            previewBox.style.color = '#FAF9F5';
            previewBox.style.borderColor = '#CCA353';
        } else if (theme === 'sepia') {
            previewBox.style.backgroundColor = '#efe7d5';
            previewBox.style.color = '#4c3c2a';
            previewBox.style.borderColor = '#c7ba9d';
        } else {
            // Light
            previewBox.style.backgroundColor = '#FFFFFF';
            previewBox.style.color = '#122521';
            previewBox.style.borderColor = '#e4decb';
        }
        
        // Font size preview style
        let sizeVal = '0.92rem';
        if (fontSize === 'sm') sizeVal = '0.82rem';
        else if (fontSize === 'lg') sizeVal = '1.15rem';
        else if (fontSize === 'xl') sizeVal = '1.35rem';
        previewBox.querySelector('.preview-text').style.fontSize = sizeVal;
        
        // Font style preview style
        let fontVal = "'Inter', sans-serif";
        if (fontStyle === 'serif') fontVal = "'Georgia', serif";
        else if (fontStyle === 'mono') fontVal = "'Courier New', monospace";
        previewBox.style.fontFamily = fontVal;
    }
}

// Save all settings and apply them to the Reader Overlay
function saveAppSettings() {
    const fontSizeSel = document.getElementById('setting-font-size');
    const fontStyleSel = document.getElementById('setting-font-style');
    const autoMusicChk = document.getElementById('setting-auto-music');
    const autoSaveChk = document.getElementById('setting-auto-save');
    
    booknestSettings.fontSize = fontSizeSel ? fontSizeSel.value : 'md';
    booknestSettings.fontStyle = fontStyleSel ? fontStyleSel.value : 'sans';
    booknestSettings.autoMusic = autoMusicChk ? autoMusicChk.checked : true;
    booknestSettings.autoSave = autoSaveChk ? autoSaveChk.checked : true;
    
    // Save to LocalStorage
    try {
        localStorage.setItem('booknest_settings', JSON.stringify(booknestSettings));
    } catch (e) {
        console.warn("localStorage is not accessible, settings not saved.", e);
    }
    
    // Apply changes to Reader Overlay in real-time
    applySettingsToDOM();

    // Dynamically play/pause ambient music based on toggled settings
    if (!booknestSettings.autoMusic) {
        if (cozyAudio && isMusicPlaying) {
            cozyAudio.pause();
            isMusicPlaying = false;
            if (typeof updateMusicUI === 'function') updateMusicUI();
        }
    } else {
        const readerOverlay = document.getElementById('reader-overlay');
        if (readerOverlay && readerOverlay.classList.contains('active') && !isMusicPlaying) {
            initCozyMusic();
            if (cozyAudio) {
                cozyAudio.play().then(() => {
                    isMusicPlaying = true;
                    if (typeof updateMusicUI === 'function') updateMusicUI();
                }).catch(err => console.log("Audio play failed: ", err));
            }
        }
    }
    
    // Close modal
    closeAppSettingsModal();
}

// Bind settings class states directly onto the reader overlay container
function applySettingsToDOM() {
    const readerOverlay = document.getElementById('reader-overlay');
    if (readerOverlay) {
        // Reset classes
        readerOverlay.classList.remove(
            'reader-theme-light', 'reader-theme-dark', 'reader-theme-sepia',
            'reader-font-sm', 'reader-font-md', 'reader-font-lg', 'reader-font-xl',
            'reader-font-serif', 'reader-font-sans', 'reader-font-mono'
        );
        
        // Append settings classes
        readerOverlay.classList.add(`reader-theme-${booknestSettings.theme}`);
        readerOverlay.classList.add(`reader-font-${booknestSettings.fontSize}`);
        readerOverlay.classList.add(`reader-font-${booknestSettings.fontStyle}`);
    }
}

// Run settings initialization on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAppSettings);
} else {
    initAppSettings();
}

// Remove downloaded book from customer's library dashboard
function removeBookFromLibraryTrigger() {
    const removeBtn = document.getElementById('btn-remove-library');
    if (!removeBtn) return;
    
    const id = removeBtn.getAttribute('data-id');
    const title = document.getElementById('modal-title').innerText;
    
    const confirmModal = document.getElementById('confirm-delete-modal');
    const confirmTitle = document.getElementById('confirm-book-title');
    const confirmActionBtn = document.getElementById('btn-confirm-delete-action');
    
    if (confirmModal && confirmTitle && confirmActionBtn) {
        confirmTitle.innerText = `"${title}"`;
        
        // Bind the actual delete call to the confirm button inside the modal
        confirmActionBtn.onclick = function() {
            // Disable buttons inside the modal & show spinner loading
            confirmActionBtn.disabled = true;
            confirmActionBtn.style.pointerEvents = 'none';
            confirmActionBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Removing...';
            
            const cancelBtn = confirmModal.querySelector('.btn-confirm-cancel');
            if (cancelBtn) {
                cancelBtn.disabled = true;
                cancelBtn.style.pointerEvents = 'none';
            }
            
            // Fetch request
            fetch(`/customer/library/remove/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Successful deletion: refresh page to update bookshelf
                    location.reload();
                } else {
                    alert(data.message || 'Failed to remove book.');
                    closeConfirmDeleteModal();
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred. Please try again.');
                closeConfirmDeleteModal();
            });
        };
        
        // Show confirm modal
        confirmModal.classList.add('active');
    }
}

function closeConfirmDeleteModal() {
    const confirmModal = document.getElementById('confirm-delete-modal');
    if (confirmModal) {
        confirmModal.classList.remove('active');
        
        // Reset button states inside
        const confirmActionBtn = document.getElementById('btn-confirm-delete-action');
        if (confirmActionBtn) {
            confirmActionBtn.disabled = false;
            confirmActionBtn.style.pointerEvents = '';
            confirmActionBtn.innerHTML = 'Remove';
        }
        
        const cancelBtn = confirmModal.querySelector('.btn-confirm-cancel');
        if (cancelBtn) {
            cancelBtn.disabled = false;
            cancelBtn.style.pointerEvents = '';
        }
    }
}
