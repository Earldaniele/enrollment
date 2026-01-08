<!-- Notifications Modal -->
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationsModalLabel">
                    <i class="bi bi-bell me-2"></i>Notifications
                    <span id="unreadBadge" class="badge bg-danger ms-2" style="display: none;">0</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                <div id="notificationsLoading" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0 text-muted">Loading notifications...</p>
                </div>
                
                <div id="notificationsContent" class="list-group list-group-flush" style="display: none;">
                    <!-- Notifications will be loaded here dynamically -->
                </div>
                
                <div id="noNotifications" class="text-center py-4" style="display: none;">
                    <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-3 text-muted">No notifications yet</h6>
                    <p class="text-muted mb-0">You'll see notifications about your enrollment status here.</p>
                </div>
                
                <div id="notificationsError" class="alert alert-danger" style="display: none;">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <span>Failed to load notifications. Please try again.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-primary" id="refreshNotifications">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
                <button type="button" class="btn btn-primary" id="markAllRead" style="display: none;">
                    <i class="bi bi-check-all me-1"></i>Mark All as Read
                </button>
            </div>
        </div>
    </div>
</div>