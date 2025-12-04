"use strict";

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

  window.kanbanManager = window.kanbanManager || {};

  let drawerMode = 'edit';
  let currentBoardId = null;

  window.openDrawer = function(mode, boardId = null, taskId = null) {
    drawerMode = mode;
    currentBoardId = boardId;
    
    const drawer = document.getElementById('kanban-update-item-sidebar');
    const title = drawer.querySelector('.offcanvas-title');
    const updateBtn = drawer.querySelector('#update-task-btn');
    const deleteBtn = drawer.querySelector('#delete-task-btn');
    
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
      document.getElementById('attachments-count').textContent = '0';
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

  function transformBoardsData(boards) {
    return boards.map(board => ({
      id: board.id,
      title: board.title,
      item: board.tasks.map(task => transformTaskData(task))
    }));
  }

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
      eid: task.id,
      badge: task.label || '',
      'badge-text': task.label || '',
      'due-date': task.due_date || '',
      assigned: undefined, // Hidden - undefined prevents avatar rendering
      members: assignedNames,
      attachments: task.attachments_count || 0,
      comments: task.comments_count || 0,
      priority: task.priority || 'medium',
      issues: task.active_issues_count || 0
    };
  }

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

    renderAttachments(task.attachments || []);
  }

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

      // Build extra content: comment HTML or attachment link
      let extraHtml = '';
      if (activity.comment) {
        // comment may contain HTML from Quill
        extraHtml = `
          <div class="mt-2 border rounded p-2 bg-light">
            ${activity.comment}
          </div>
        `;
      } else if (activity.attachment && activity.attachment.file_name) {
        const filePath = activity.attachment.file_path ? `/storage/${activity.attachment.file_path}` : '#';
        extraHtml = `
          <div class="mt-2">
            <a href="${filePath}" target="_blank" class="text-decoration-none">
              <i class="ti ti-paperclip me-1"></i>${activity.attachment.file_name}
            </a>
          </div>
        `;
      }

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
            ${extraHtml}
          </div>
        </div>
      `;
    }).join('');
  }

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

      // Do not show success messages for move operations to avoid noisy UX.
      if (!response.ok || !result.success) {
        // Log and reload after a short delay to revert inconsistent state
        console.error('Move task failed:', result.message || 'Unknown error');
        setTimeout(() => location.reload(), 700);
      }
    } catch (error) {
      console.error('Error moving task:', error);
      // On network or unexpected error, reload to keep UI consistent
      setTimeout(() => location.reload(), 700);
    }
  };

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

  window.addComment = async function(comment) {
    if (!currentTaskId || !comment) return;

    // Send comment and optimistically update UI
    try {
      const postBtn = document.getElementById('post-comment-btn');
      if (postBtn) {
        postBtn.disabled = true;
        postBtn.innerHTML = 'Posting...';
      }

      const response = await fetch(`/workspaces/${workspaceId}/kanban/tasks/${currentTaskId}/comment`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ comment })
      });

      const result = await response.json();

      if (response.ok && result.success) {
        // Clear editor if Quill present
        if (typeof Quill !== 'undefined') {
          const editorEl = document.querySelector('#comment-editor');
          const quill = Quill.find ? Quill.find(editorEl) : null;
          if (quill) quill.setContents([]);
        }

        // Switch to Activity tab and refresh activities so the new comment appears there
        const activityTabBtn = document.querySelector('[data-bs-target="#tab-activity"]');
        if (activityTabBtn) {
          try {
            new bootstrap.Tab(activityTabBtn).show();
          } catch (e) {
            // fallback: click
            activityTabBtn.click();
          }
        }

        loadTaskActivities(currentTaskId);
      } else {
        throw new Error(result.message || 'Failed to add comment');
      }
    } catch (error) {
      console.error('Error adding comment:', error);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: error.message || 'Failed to add comment',
          customClass: { confirmButton: 'btn btn-primary' },
          buttonsStyling: false
        });
      }
    } finally {
      const postBtn = document.getElementById('post-comment-btn');
      if (postBtn) {
        postBtn.disabled = false;
        postBtn.innerHTML = 'Post';
      }
    }
  };

  window.uploadAttachment = async function(file) {
    if (!currentTaskId || !file) return;

    const progressContainer = document.getElementById('upload-progress');
    const progressBar = document.getElementById('upload-progress-bar');
    if (progressContainer) progressContainer.style.display = 'block';
    if (progressBar) progressBar.style.width = '0%';

    try {
      await new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/workspaces/${workspaceId}/kanban/tasks/${currentTaskId}/attach`);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
        xhr.responseType = 'json';

        xhr.upload.onprogress = function(e) {
          if (e.lengthComputable && progressBar) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
          }
        };

        xhr.onload = function() {
          if (xhr.status >= 200 && xhr.status < 300 && xhr.response && xhr.response.success) {
            resolve(xhr.response);
          } else {
            reject(new Error((xhr.response && xhr.response.message) || 'Failed to upload file'));
          }
        };

        xhr.onerror = function() {
          reject(new Error('Network error while uploading file'));
        };

        const fd = new FormData();
        fd.append('file', file);
        xhr.send(fd);
      });

      // Refresh attachments
      loadTaskDetails(currentTaskId);
    } catch (error) {
      console.error('Error uploading file:', error);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: error.message || 'Failed to upload file',
          customClass: { confirmButton: 'btn btn-primary' },
          buttonsStyling: false
        });
      }
    } finally {
      if (progressContainer) progressContainer.style.display = 'none';
      if (progressBar) progressBar.style.width = '0%';
    }
  };

  window.deleteAttachment = async function(attachmentId) {
    if (!attachmentId) return;

    const confirmResult = await Swal.fire({
      icon: 'warning',
      title: 'Are you sure?',
      text: 'This will permanently remove the attachment.',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      customClass: { confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-label-secondary' },
      buttonsStyling: false
    });

    if (!confirmResult.isConfirmed) return;

    try {
      const response = await fetch(`/workspaces/${workspaceId}/kanban/attachments/${attachmentId}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });

      const result = await response.json();

      if (response.ok && result.success) {
        // Refresh attachments
        loadTaskDetails(currentTaskId);
      } else {
        throw new Error(result.message || 'Failed to delete attachment');
      }
    } catch (error) {
      console.error('Error deleting attachment:', error);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: error.message || 'Failed to delete attachment',
          customClass: { confirmButton: 'btn btn-primary' },
          buttonsStyling: false
        });
      }
    }
  };

  window.kanbanManager.deleteAttachment = window.deleteAttachment;

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

  document.addEventListener('DOMContentLoaded', function() {
    // Primary button click (create or update based on mode)
    const updateBtn = document.getElementById('update-task-btn');
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
    const deleteBtn = document.getElementById('delete-task-btn');
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

    // Post comment button
    const postBtn = document.getElementById('post-comment-btn');
    if (postBtn) {
      postBtn.addEventListener('click', function(e) {
        e.preventDefault();
        // Get comment content from Quill if available
        let comment = '';
        if (typeof Quill !== 'undefined') {
          const editorEl = document.querySelector('#comment-editor');
          const quill = Quill.find ? Quill.find(editorEl) : null;
          if (quill) {
            comment = quill.root.innerHTML.trim();
            if (quill.getText().trim().length === 0) return; // don't post empty
          }
        }

        if (comment) addComment(comment);
      });
    }

    // Hook into jKanban drag events
    // Wait for jKanban to initialize
    setTimeout(function() {
      const MOVE_DEBOUNCE_MS = 600; // delay to wait for moves to settle
      const pendingMoves = new Map(); // taskId -> { boardId, position, timeoutId }
      const lastSentMove = new Map(); // taskId -> 'boardId:position'

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

                    if (!taskId || !boardId) return;

                    const moveKey = `${boardId}:${position}`;
                    // If we've already sent this exact move, ignore
                    if (lastSentMove.get(taskId) === moveKey) return;

                    // If there's an existing pending move for this task, clear it
                    const existing = pendingMoves.get(taskId);
                    if (existing && existing.timeoutId) {
                      clearTimeout(existing.timeoutId);
                    }

                    // Schedule a debounced move
                    const timeoutId = setTimeout(() => {
                      // Execute the move and record as last sent
                      handleTaskMove(taskId, boardId, position);
                      lastSentMove.set(taskId, moveKey);
                      pendingMoves.delete(taskId);
                    }, MOVE_DEBOUNCE_MS);

                    pendingMoves.set(taskId, { boardId, position, timeoutId });
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
