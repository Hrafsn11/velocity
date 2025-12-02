/**
 * App Kanban Custom - Laravel Backend Integration
 * This file extends the existing app-kanban.js to work with Laravel backend API
 */

'use strict';

(function () {
  const workspaceElement = document.querySelector('.app-kanban');
  if (!workspaceElement) return;

  const workspaceId = workspaceElement.getAttribute('data-workspace-id');
  if (!workspaceId) {
    console.error('Workspace ID not found');
    return;
  }

  const assetsPath = document.querySelector('html').getAttribute('data-assets-path');
  let currentTaskId = null;

  // Create global kanbanManager object for onclick handlers
  window.kanbanManager = window.kanbanManager || {};

  // Drawer mode state
  let drawerMode = 'edit'; // 'create' or 'edit'
  let currentBoardId = null;

  // Function to open drawer in create or edit mode
  window.openDrawer = function(mode, boardId = null, taskId = null) {
    drawerMode = mode;
    currentBoardId = boardId;
    
    const drawer = document.getElementById('kanban-update-item-sidebar');
    const title = drawer.querySelector('.offcanvas-title');
    const updateBtn = drawer.querySelector('.btn-primary');
    const deleteBtn = drawer.querySelector('.btn-label-danger');
    
    if (mode === 'create') {
      title.textContent = 'Create New Task';
      updateBtn.innerHTML = '<i class="ti ti-plus me-1"></i> Create Task';
      deleteBtn.style.display = 'none';
      
      // Reset form
      document.getElementById('current-task-id').value = '';
      document.getElementById('title').value = '';
      document.getElementById('description').value = '';
      document.getElementById('priority').value = 'medium';
      document.getElementById('label').value = '';
      document.getElementById('due-date').value = '';
      if (document.getElementById('assignees')) {
        document.getElementById('assignees').value = null;
      }
      document.getElementById('attachments-list').innerHTML = '<small class="text-muted">No attachments</small>';
      document.getElementById('comments-list').innerHTML = '<small class="text-muted">No comments yet</small>';
      document.getElementById('attachments-count').textContent = '0';
      document.getElementById('comments-count').textContent = '0';
      document.getElementById('activity-timeline').innerHTML = '<p class="text-muted text-center py-4"><i class="ti ti-timeline-event ti-lg d-block mb-2"></i>No activity yet</p>';
      
      // Reinitialize Select2 for create mode
      setTimeout(() => {
        if (typeof $.fn.select2 !== 'undefined') {
          // Destroy if already initialized
          if ($('#priority').hasClass('select2-hidden-accessible')) {
            $('#priority').select2('destroy');
          }
          if ($('#label').hasClass('select2-hidden-accessible')) {
            $('#label').select2('destroy');
          }
          if ($('#assignees').length && $('#assignees').hasClass('select2-hidden-accessible')) {
            $('#assignees').select2('destroy');
          }
          
          // Initialize fresh
          $('#priority').select2({
            dropdownParent: $('#kanban-update-item-sidebar'),
            minimumResultsForSearch: Infinity
          });
          $('#label').select2({
            dropdownParent: $('#kanban-update-item-sidebar'),
            minimumResultsForSearch: Infinity
          });
          if ($('#assignees').length) {
            $('#assignees').select2({
              dropdownParent: $('#kanban-update-item-sidebar'),
              placeholder: 'Select assignees',
              allowClear: true
            });
          }
        }
      }, 100);
      
    } else if (mode === 'edit' && taskId) {
      title.textContent = 'Task Details';
      updateBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Update Task';
      deleteBtn.style.display = '';
      
      // Reinitialize Select2 for edit mode (same as create mode)
      setTimeout(() => {
        if (typeof $.fn.select2 !== 'undefined') {
          // Destroy if already initialized
          if ($('#priority').hasClass('select2-hidden-accessible')) {
            $('#priority').select2('destroy');
          }
          if ($('#label').hasClass('select2-hidden-accessible')) {
            $('#label').select2('destroy');
          }
          if ($('#assignees').length && $('#assignees').hasClass('select2-hidden-accessible')) {
            $('#assignees').select2('destroy');
          }
          
          // Initialize fresh (same config as create mode)
          $('#priority').select2({
            dropdownParent: $('#kanban-update-item-sidebar'),
            minimumResultsForSearch: Infinity
          });
          $('#label').select2({
            dropdownParent: $('#kanban-update-item-sidebar'),
            minimumResultsForSearch: Infinity
          });
          if ($('#assignees').length) {
            $('#assignees').select2({
              dropdownParent: $('#kanban-update-item-sidebar'),
              placeholder: 'Select assignees',
              allowClear: true
            });
          }
        }
        
        // Load task details after Select2 is initialized
        window.loadTaskDetails(taskId);
      }, 100);
    }
    
    // Show drawer
    const bsOffcanvas = new bootstrap.Offcanvas(drawer);
    bsOffcanvas.show();
  };

  // Override the default kanban data fetch
  window.kanbanDataFetch = async function() {
    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/boards`, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });

      if (!response.ok) {
        throw new Error('Failed to fetch boards');
      }

      const result = await response.json();
      
      if (result.success) {
        // Transform Laravel API data to jKanban format
        return transformBoardsData(result.data);
      }
      
      return [];
    } catch (error) {
      console.error('Error fetching kanban data:', error);
      return [];
    }
  };

  // Transform Laravel board data to jKanban format
  function transformBoardsData(boards) {
    return boards.map(board => ({
      id: board.id,
      title: board.title,
      item: board.tasks.map(task => transformTaskData(task))
    }));
  }

  // Transform Laravel task data to jKanban item format
  function transformTaskData(task) {
    const assignedImages = task.assignees.map(a => {
      // Extract filename from avatar URL or use default
      const avatar = a.avatar || 'default-avatar.png';
      return avatar.split('/').pop();
    }).join(',');

    const assignedNames = task.assignees.map(a => a.name).join(',');

    return {
      id: task.id,
      title: `<span class="kanban-text">${task.title}</span>`,
      'data-eid': task.id,
      'data-badge': task.label || '',
      'data-badge-text': task.label || '',
      'data-due-date': task.due_date || '',
      'data-assigned': assignedImages,
      'data-members': assignedNames,
      'data-attachments': task.attachments_count || 0,
      'data-comments': task.comments_count || 0,
      'data-priority': task.priority || 'medium'
    };
  }

  // Load task details when clicked
  window.loadTaskDetails = async function(taskId) {
    try {
      currentTaskId = taskId;
      document.getElementById('current-task-id').value = taskId;

      const response = await fetch(`/workspaces/${workspaceId}/kanban/tasks/${taskId}`, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });

      if (!response.ok) {
        throw new Error('Failed to fetch task details');
      }

      const result = await response.json();
      
      if (result.success) {
        populateTaskDrawer(result.data);
        loadTaskActivities(taskId);
      }
    } catch (error) {
      console.error('Error loading task details:', error);
      showToast('Failed to load task details', 'error');
    }
  };

  // Populate drawer with task data
  function populateTaskDrawer(task) {
    // Set current board ID from task
    currentBoardId = task.board_id;
    
    // Fill form fields
    document.getElementById('title').value = task.title || '';
    
    // Fill description
    const descriptionField = document.getElementById('description');
    if (descriptionField) {
      descriptionField.value = task.description || '';
    }
    
    if (task.due_date) {
      const dueDateInput = document.getElementById('due-date');
      if (dueDateInput && dueDateInput._flatpickr) {
        dueDateInput._flatpickr.setDate(task.due_date);
      }
    }

    // Set Select2 values with slight delay to ensure Select2 is ready
    setTimeout(() => {
      // Set priority using Select2
      if (task.priority) {
        $('#priority').val(task.priority).trigger('change');
      } else {
        $('#priority').val('medium').trigger('change');
      }

      // Set label using Select2
      if (task.label) {
        $('#label').val(task.label).trigger('change');
      } else {
        $('#label').val('').trigger('change');
      }

      // Set assignees using Select2 multi-select
      if ($('#assignees').length && task.assignees) {
        const assigneeIds = task.assignees.map(a => a.employee_id);
        $('#assignees').val(assigneeIds).trigger('change');
      }
    }, 50);

    // Render attachments
    renderAttachments(task.attachments || []);

    // Render comments
    renderComments(task.comments || []);
  }

  // Render attachments list
  function renderAttachments(attachments) {
    const attachmentsList = document.getElementById('attachments-list');
    const attachmentsCount = document.getElementById('attachments-count');
    
    if (attachmentsCount) {
      attachmentsCount.textContent = attachments.length;
      attachmentsCount.className = attachments.length > 0 ? 'badge bg-label-primary ms-1' : 'badge bg-label-secondary ms-1';
    }

    if (!attachmentsList) return;

    if (attachments.length === 0) {
      attachmentsList.innerHTML = '<small class="text-muted">No attachments</small>';
      return;
    }

    attachmentsList.innerHTML = attachments.map(att => `
      <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2">
        <div class="d-flex align-items-center gap-2 flex-grow-1">
          <i class="ti ti-file text-muted"></i>
          <div class="flex-grow-1">
            <a href="/storage/${att.file_path}" target="_blank" class="text-heading fw-medium">
              ${att.file_name}
            </a>
            <small class="text-muted d-block">${att.file_size_human}</small>
          </div>
        </div>
        <button class="btn btn-sm btn-icon btn-text-danger" onclick="kanbanManager.deleteAttachment('${att.attachment_id}')" title="Delete attachment">
          <i class="ti ti-trash"></i>
        </button>
      </div>
    `).join('');
  }

  // Render comments list
  function renderComments(comments) {
    const commentsList = document.getElementById('comments-list');
    const commentsCount = document.getElementById('comments-count');
    
    if (commentsCount) {
      commentsCount.textContent = comments.length;
      commentsCount.className = comments.length > 0 ? 'badge bg-label-primary ms-1' : 'badge bg-label-secondary ms-1';
    }

    if (!commentsList) return;

    if (comments.length === 0) {
      commentsList.innerHTML = '<small class="text-muted">No comments yet</small>';
      return;
    }

    commentsList.innerHTML = comments.map(comment => {
      const avatarHtml = comment.user.avatar_url 
        ? `<img src="${comment.user.avatar_url}" alt="${comment.user.name}" class="rounded-circle">`
        : `<span class="avatar-initial rounded-circle bg-label-primary">${comment.user.name.charAt(0)}</span>`;

      return `
        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
          <div class="avatar avatar-sm flex-shrink-0">
            ${avatarHtml}
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <h6 class="mb-0">${comment.user.name}</h6>
              <small class="text-muted">${formatDateTime(comment.created_at)}</small>
            </div>
            <p class="mb-0">${comment.comment}</p>
          </div>
        </div>
      `;
    }).join('');
  }

  // Format date time helper
  function formatDateTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
    
    return date.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  // Load task activities
  async function loadTaskActivities(taskId) {
    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/tasks/${taskId}/activities`, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });

      if (!response.ok) {
        throw new Error('Failed to fetch activities');
      }

      const result = await response.json();
      
      if (result.success) {
        renderActivities(result.data);
      }
    } catch (error) {
      console.error('Error loading activities:', error);
    }
  }

  // Render activities in timeline
  function renderActivities(activities) {
    const timeline = document.getElementById('activity-timeline');
    if (!timeline) return;

    if (!activities || activities.length === 0) {
      timeline.innerHTML = '<p class="text-muted text-center py-4">No activity yet</p>';
      return;
    }

    timeline.innerHTML = activities.map(activity => {
      const avatarHtml = activity.user.avatar 
        ? `<img src="${activity.user.avatar}" alt="${activity.user.name}" class="rounded-circle">`
        : `<span class="avatar-initial rounded-circle bg-label-primary">${activity.user.initials}</span>`;

      return `
        <div class="media mb-4 d-flex align-items-start">
          <div class="avatar avatar-sm me-3 flex-shrink-0">
            ${avatarHtml}
          </div>
          <div class="media-body flex-grow-1">
            <p class="mb-1">
              <i class="${activity.icon} text-${activity.color} me-1"></i>
              <strong>${activity.user.name}</strong> ${activity.description}
            </p>
            <small class="text-muted">${activity.time}</small>
          </div>
        </div>
      `;
    }).join('');
  }

  // Create new task
  window.createTask = async function() {
    const assigneesSelect = document.getElementById('assignees');
    const assigneesValue = assigneesSelect ? $('#assignees').val() : [];
    
    const formData = {
      board_id: currentBoardId,
      title: document.getElementById('title')?.value,
      description: document.getElementById('description')?.value || null,
      priority: document.getElementById('priority')?.value || 'medium',
      label: document.getElementById('label')?.value || null,
      due_date: document.getElementById('due-date')?.value || null,
      assignees: assigneesValue && assigneesValue.length > 0 ? assigneesValue : null,
    };

    if (!formData.title || !formData.board_id) {
      Swal.fire({
        icon: 'warning',
        title: 'Validation Error',
        text: 'Please enter task title',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
      return;
    }

    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/tasks`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
      });

      const result = await response.json();

      if (response.ok && result.success) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Task created successfully',
          timer: 1500,
          showConfirmButton: false
        });

        // Close drawer
        const drawer = bootstrap.Offcanvas.getInstance(document.getElementById('kanban-update-item-sidebar'));
        if (drawer) drawer.hide();

        // Reload kanban
        setTimeout(() => location.reload(), 1500);
      } else {
        throw new Error(result.message || 'Failed to create task');
      }
    } catch (error) {
      console.error('Create task error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: error.message || 'Failed to create task',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    }
  };

  // Update task when form is submitted
  window.updateTask = async function() {
    if (!currentTaskId) return;

    const assigneesSelect = document.getElementById('assignees');
    const assigneesValue = assigneesSelect ? $('#assignees').val() : [];

    const formData = {
      board_id: currentBoardId,
      title: document.getElementById('title').value,
      description: document.getElementById('description')?.value || null,
      due_date: document.getElementById('due-date').value,
      label: document.getElementById('label').value,
      priority: document.getElementById('priority').value,
      assignees: assigneesValue && assigneesValue.length > 0 ? assigneesValue : null,
    };

    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/tasks/${currentTaskId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
      });

      const result = await response.json();

      if (response.ok && result.success) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Task updated successfully',
          timer: 1500,
          showConfirmButton: false
        });
        
        setTimeout(() => location.reload(), 1500);
      } else {
        throw new Error(result.message || 'Failed to update task');
      }
    } catch (error) {
      console.error('Error updating task:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: error.message || 'Failed to update task',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    }
  };

  // Delete task
  window.deleteTask = async function() {
    if (!currentTaskId) return;

    const result = await Swal.fire({
      icon: 'warning',
      title: 'Are you sure?',
      text: 'You won\'t be able to revert this!',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel',
      customClass: {
        confirmButton: 'btn btn-danger me-2',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    });

    if (!result.isConfirmed) return;

    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/tasks/${currentTaskId}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });

      const data = await response.json();
      
      if (response.ok && data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: 'Task has been deleted',
          timer: 1500,
          showConfirmButton: false
        });
        
        // Close drawer and reload
        const drawer = bootstrap.Offcanvas.getInstance(document.getElementById('kanban-update-item-sidebar'));
        if (drawer) drawer.hide();
        
        setTimeout(() => location.reload(), 1500);
      } else {
        throw new Error(data.message || 'Failed to delete task');
      }
    } catch (error) {
      console.error('Error deleting task:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: error.message || 'Failed to delete task',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    }
  };

  // Handle task drag and drop
  window.handleTaskMove = async function(taskId, boardId, position) {
    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/tasks/${taskId}/move`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          board_id: boardId,
          position: position
        })
      });

      const result = await response.json();

      if (response.ok && result.success) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Task moved successfully',
          timer: 1500,
          showConfirmButton: false
        });
      } else {
        throw new Error(result.message || 'Failed to move task');
      }
    } catch (error) {
      console.error('Error moving task:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: error.message || 'Failed to move task',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
      // Reload to revert changes
      setTimeout(() => location.reload(), 1500);
    }
  };

  // Add new board
  window.addBoard = async function(title) {
    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/boards`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          workspace_id: workspaceId,
          title: title,
          color: '#6366f1'
        })
      });

      const result = await response.json();

      if (response.ok && result.success) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Board created successfully',
          timer: 1500,
          showConfirmButton: false
        });
        
        setTimeout(() => location.reload(), 1500);
      } else {
        throw new Error(result.message || 'Failed to create board');
      }
    } catch (error) {
      console.error('Error creating board:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: error.message || 'Failed to create board',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    }
  };

  // Add comment to task
  window.addComment = async function(comment) {
    if (!currentTaskId || !comment) return;

    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/tasks/${currentTaskId}/comment`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ comment })
      });

      if (!response.ok) {
        throw new Error('Failed to add comment');
      }

      const result = await response.json();
      
      if (result.success) {
        showToast('Comment added successfully', 'success');
        loadTaskActivities(currentTaskId);
      }
    } catch (error) {
      console.error('Error adding comment:', error);
      showToast('Failed to add comment', 'error');
    }
  };

  // Upload attachment
  window.uploadAttachment = async function(file) {
    if (!currentTaskId || !file) return;

    const formData = new FormData();
    formData.append('file', file);

    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/tasks/${currentTaskId}/attach`, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
      });

      if (!response.ok) {
        throw new Error('Failed to upload attachment');
      }

      const result = await response.json();
      
      if (result.success) {
        showToast('File uploaded successfully', 'success');
        loadTaskDetails(currentTaskId);
      }
    } catch (error) {
      console.error('Error uploading file:', error);
      showToast('Failed to upload file', 'error');
    }
  };

  // Delete attachment
  window.deleteAttachment = async function(attachmentId) {
    if (!confirm('Are you sure you want to delete this attachment?')) return;

    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/attachments/${attachmentId}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });

      if (!response.ok) {
        throw new Error('Failed to delete attachment');
      }

      const result = await response.json();
      
      if (result.success) {
        showToast('Attachment deleted successfully', 'success');
        loadTaskDetails(currentTaskId);
      }
    } catch (error) {
      console.error('Error deleting attachment:', error);
      showToast('Failed to delete attachment', 'error');
    }
  };

  // Expose to global kanbanManager
  window.kanbanManager.deleteAttachment = window.deleteAttachment;

  // Toast notification helper
  function showToast(message, type = 'success') {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type === 'success' ? 'success' : 'error',
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    } else {
      console.log(`${type.toUpperCase()}: ${message}`);
    }
  }

  // Event listeners
  document.addEventListener('DOMContentLoaded', function() {
    // Primary button click (create or update based on mode)
    const updateBtn = document.querySelector('.kanban-update-item-sidebar .btn-primary');
    if (updateBtn) {
      updateBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (drawerMode === 'create') {
          createTask();
        } else {
          updateTask();
        }
      });
    }

    // Delete button click
    const deleteBtn = document.querySelector('.kanban-update-item-sidebar .btn-label-danger');
    if (deleteBtn) {
      deleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        deleteTask();
      });
    }

    // Attachment upload
    const attachmentInput = document.getElementById('attachments');
    if (attachmentInput) {
      attachmentInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          uploadAttachment(file);
          e.target.value = '';
        }
      });
    }

    // Hook into jKanban drag events
    // Wait for jKanban to initialize
    setTimeout(function() {
      const kanbanBoards = document.querySelectorAll('.kanban-board');
      kanbanBoards.forEach(board => {
        const boardElement = board.querySelector('.kanban-drag');
        if (boardElement) {
          // MutationObserver to detect when tasks are moved
          const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
              if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(function(node) {
                  if (node.classList && node.classList.contains('kanban-item')) {
                    const taskId = node.getAttribute('data-eid');
                    const boardId = board.getAttribute('data-id');
                    const items = board.querySelectorAll('.kanban-item');
                    const position = Array.from(items).indexOf(node);
                    
                    if (taskId && boardId) {
                      handleTaskMove(taskId, boardId, position);
                    }
                  }
                });
              }
            });
          });

          observer.observe(boardElement, {
            childList: true,
            subtree: false
          });
        }
      });
    }, 1000);
  });

})();
