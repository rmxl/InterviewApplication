<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <!-- Dashboard Header -->
  <!-- Add this where you want the button to appear -->

  <div class="header-container">
    <div class="header">
      <i class="fas fa-tachometer-alt"></i> Dashboard
      <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="logout-btn">Logout</button>
  </form>
    </div>
  </div>
  


  <!-- Main Content Container -->
  <div class="dashboard-content">
    <!-- Calendar Container -->
    <div class="calendar-container">
      <div class="calendar-header">
        <button id="prevMonth" class="calendar-btn">
          <i class="fas fa-chevron-left"></i> Prev
        </button>
        <div class="calendar-controls">
          <select id="yearSelect" class="year-select"></select>
          <span id="monthDisplay" class="month-display"></span>
        </div>
        <button id="nextMonth" class="calendar-btn">
          Next <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      <table class="calendar">
        <thead>
          <tr>
            <th>Sun</th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
          </tr>
        </thead>
        <tbody id="calendarBody">
          <!-- Calendar days injected here via JS -->
        </tbody>
      </table>
    </div>

    <!-- Section Title with Date Filter and Show Available Slots Button -->
    <div class="section-title">
      <i class="fas fa-list-check"></i> Scheduled Assessments
      <span class="date-filter" id="currentDateFilter">All Pending</span>
      <button id="showSlotsBtn" class="modal-btn primary-btn">Show Available Slots</button>
      <form action="{{ route('generate.timeslots', ['backendGuy' => session('backendGuy')]) }}" method="POST" class="inline" id="generateTimeSlotsForm">
        @csrf
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" id="generateTimeSlotsBtn">
            Generate Time Slots
        </button>
    </form>
    
    <script>
    document.getElementById('generateTimeSlotsForm').addEventListener('submit', function(e) {
        const button = document.getElementById('generateTimeSlotsBtn');
        const buttonText = button.querySelector('.button-text');
        const loadingSpinner = button.querySelector('.loading-spinner');
    
        // Disable button and show loading state
        button.disabled = true;
        buttonText.classList.add('hidden');
        loadingSpinner.classList.remove('hidden');
    });
    </script>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
            {{ session('info') }}
        </div>
    @endif
    </div>
    

    <!-- Scheduled Assessments List -->
    <div class="schedule-container" id="assessmentsList">

      @if(count($schedules) == 0)
        <div class="empty-state">
          <i class="fas fa-calendar-check"></i>
          <p>No pending assessments at the moment</p>
          <p>Check back later for new appointments</p>
        </div>
      @endif
    </div>

    <!-- Instant Assessments List -->
    <div class="section-title">
      <i class="fas fa-list-check"></i> Instant Assessments
      <span class="date-filter" id="currentDateFilter">All Pending</span>
    </div>

    <div class="schedule-containter" id="instantAssessmentsList">
    </div>
  </div>
  


  <!-- Modal Popup for Available Time Slots & Editing -->
  <div class="modal" id="timeSlotModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalDateHeading">Available Time Slots</h3>
        <span class="close-modal" id="closeModal">&times;</span>
      </div>
      <div id="modalContent" class="modal-body">
      </div>
      <div class="modal-footer">
        <button id="closeModalBtn" class="modal-btn cancel-btn">Close</button>
        <button id="addTimeSlotBtn" class="modal-btn primary-btn">Add Time Slot</button>
      </div>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
  </div>

  <script>

      const HOST = '{{ config('app.host')}}';
      console.log(HOST);

      window.joinMeeting = async function(scheduleId) {
          try {
              const res = await fetch('/api/create-url', {
                  method: "POST",
                  headers: {
                      "Content-Type": "application/json"
                  },
                  body: JSON.stringify({ "assessment_request_id": scheduleId }),
              });

        if (!res.ok) {
          throw new Error(`HTTP error! Status: ${res.status}`);
        }

        const data = await res.json();
        console.log(data);

        // Fixed string concatenation
        if (data.url) {
          window.location.href = `/meet/${data.url}?assessment_request_id=${scheduleId}`;
        } else {
          console.error("URL not found in response");
        }
      } catch (error) {
        console.error("Error fetching token:", error);
      }
    };
    window.showUserInfo = function(user) {

  console.log(user);

  // Remove any existing user info modals
  const existingModal = document.getElementById('userInfoModal');
  if (existingModal) {
    existingModal.remove();
  }

  // Create modal element
  const userInfoModal = document.createElement('div');
  userInfoModal.className = 'modal';
  userInfoModal.id = 'userInfoModal';


  let jobType = 'UNDEFINED';
  function fetchJobType(userId) {
  return fetch(`/api/user-info/${userId}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to fetch user info');
      }
      return response.json();
    })
    .then(data => data.job_title || 'Not specified')
    .catch(error => {
      console.error('Error fetching user info:', error);
      return 'Error fetching job type';
    });
}



console.log("JOB TITLE_2  IS ", jobType);


  // Create modal content
  userInfoModal.innerHTML = `
    <div class="modal-content user-info-modal">
      <div class="modal-header">
        <h3><i class="fas fa-user-circle"></i> User Profile</h3>
        <span class="close-modal" id="closeUserInfoModal">&times;</span>
      </div>
      <div class="modal-body">
        <div class="user-info-container">
          <div class="user-info-field">
            <div class="field-label"><i class="fas fa-id-card"></i> Name</div>
            <div class="field-value">${user.name || 'Not provided'}</div>
          </div>
          <div class="user-info-field">
            <div class="field-label"><i class="fas fa-user"></i> Username</div>
            <div class="field-value">${user.username || 'Not provided'}</div>
          </div>
          <div class="user-info-field">
            <div class="field-label"><i class="fas fa-briefcase"></i> Experience Level</div>
            <div class="field-value">${user.experience_level || 'Not specified'}</div>
          </div>
          <div class="user-info-field">
            <div class="field-label"><i class="fas fa-utensils"></i> Job Type</div>
            <div class="field-value" id="jobTypeValue">Loading...</div>
          </div>
          ${user.email ? `
          <div class="user-info-field">
            <div class="field-label"><i class="fas fa-envelope"></i> Email</div>
            <div class="field-value">${user.email}</div>
          </div>` : ''}
          ${user.phone ? `
          <div class="user-info-field">
            <div class="field-label"><i class="fas fa-phone"></i> Phone</div>
            <div class="field-value">${user.phone}</div>
          </div>` : ''}
        </div>
      </div>
      <div class="modal-footer">
        <button id="closeUserInfoBtn" class="modal-btn primary-btn">Close</button>
      </div>
    </div>
  `;

  // Append modal to body
  document.body.appendChild(userInfoModal);


    // Fetch job type and update the modal
    fetchJobType(user.id).then(jobType => {
    console.log("JOB TITLE IS ", jobType);

    // Update the modal content with the fetched job title
    const jobTypeField = userInfoModal.querySelector('#jobTypeValue');
    if (jobTypeField) {
      jobTypeField.textContent = jobType;
    }
  });

  // Add some styling
  const style = document.createElement('style');
  style.textContent = `
    .user-info-modal {
      max-width: 500px;
    }

    .user-info-container {
      display: flex;
      flex-direction: column;
      gap: 15px;
      padding: 10px 0;
    }

    .user-info-field {
      display: flex;
      flex-direction: column;
      gap: 5px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }

    .user-info-field:last-child {
      border-bottom: none;
    }

    .field-label {
      font-size: 14px;
      color: #666;
      font-weight: 500;
    }

    .field-label i {
      width: 20px;
      text-align: center;
      margin-right: 5px;
      color: #4a6fa5;
    }

    .field-value {
      font-size: 16px;
      color: #333;
      font-weight: 500;
      padding-left: 25px;
    }
  `;
  document.head.appendChild(style);

  // Show the modal
  userInfoModal.style.display = 'block';

  // Add event listeners to close modal
  document.getElementById('closeUserInfoModal').addEventListener('click', () => {
    userInfoModal.remove();
  });

  document.getElementById('closeUserInfoBtn').addEventListener('click', () => {
    userInfoModal.remove();
  });

  // Close modal when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target === userInfoModal) {
      userInfoModal.remove();
    }
  });
};

      document.addEventListener('DOMContentLoaded', function() {
          // Global variables for calendar and assessments list
          const calendarBody = document.getElementById('calendarBody');
          const monthDisplay = document.getElementById('monthDisplay');
          const yearSelect = document.getElementById('yearSelect');
          const prevMonthBtn = document.getElementById('prevMonth');
          const nextMonthBtn = document.getElementById('nextMonth');
          const modal = document.getElementById('timeSlotModal');
          const closeModal = document.getElementById('closeModal');
          const closeModalBtn = document.getElementById('closeModalBtn');
          const addTimeSlotBtn = document.getElementById('addTimeSlotBtn');
          const modalDateHeading = document.getElementById('modalDateHeading');
          const modalContent = document.getElementById('modalContent');
          const assessmentsList = document.getElementById('assessmentsList');
          const instantAssessmentsList = document.getElementById('instantAssessmentsList');
          const currentDateFilter = document.getElementById('currentDateFilter');
          const showSlotsBtn = document.getElementById('showSlotsBtn');
          const loadingOverlay = document.getElementById('loadingOverlay');
          const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

          let currentDate = new Date();
          let currentYear = currentDate.getFullYear();
          let currentMonth = currentDate.getMonth();
          let selectedDateObj = null;
          let currentModalMode = 'view'; // 'view', 'add', 'edit'
          let editingSlotId = null;
          let currentLoggedInBackendGuyId = null;

          // Function to get the currently logged in backend guy ID
          async function getCurrentBackendGuyId() {
            try {
              const response = await fetch('/current-backend-guy', {
                method: 'GET',
                'X-CSRF-TOKEN': csrfToken,
                credentials: 'include'
              });
              if (!response.ok) {
                throw new Error('Failed to get current backend guy info');
              }
              const data = await response.json();
              return data.id;
            } catch (error) {
              console.error('Error fetching current backend guy:', error);
              return null;
            }
          }

          // Function to format date as YYYY-MM-DD
          function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
          }

          // Function to display date in user-friendly format
          function displayDate(date) {
            const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
          }

          // Show/hide loading overlay
          function showLoading() {
            loadingOverlay.style.display = 'flex';
          }

          function hideLoading() {
            loadingOverlay.style.display = 'none';
          }

          // Populate the year dropdown
          function populateYearSelect() {
            let startYear = currentYear - 2;
            let endYear = currentYear + 5;
            yearSelect.innerHTML = '';
            for (let y = startYear; y <= endYear; y++) {
              const option = document.createElement('option');
              option.value = y;
              option.textContent = y;
              if (y === currentYear) option.selected = true;
              yearSelect.appendChild(option);
            }
          }

          // Generate and display the calendar
          function generateCalendar(year, month) {
            calendarBody.innerHTML = '';
            const monthNames = ["January", "February", "March", "April", "May", "June",
                              "July", "August", "September", "October", "November", "December"];
            monthDisplay.textContent = monthNames[month];

            const firstDay = new Date(year, month, 1);
            const startingDay = firstDay.getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();

            // Fetch assessments data for this month to highlight dates with assessments
            const firstDayOfMonth = formatDate(new Date(year, month, 1));
            const lastDayOfMonth = formatDate(new Date(year, month + 1, 0));

            fetch(`/api/assessments/month?start=${firstDayOfMonth}&end=${lastDayOfMonth}&backend_guy_id=${currentLoggedInBackendGuyId}`)
              .then(response => response.json())
              .then(data => {
                // Create a map of dates with assessment counts
                const assessmentsMap = {};
                data.forEach(assessment => {
                  if (assessment.time_slot && assessment.time_slot.date) {
                    const dateKey = assessment.time_slot.date;
                    if (!assessmentsMap[dateKey]) {
                      assessmentsMap[dateKey] = 0;
                    }
                    assessmentsMap[dateKey]++;
                  } else {
                    console.warn('Assessment found without timeSlot or timeSlot.date:', assessment);
                  }
                });

                // Now generate the calendar with the assessment data
                let date = 1;
                for (let i = 0; i < 6; i++) {
                  if (date > daysInMonth) break;
                  const row = document.createElement('tr');
                  for (let j = 0; j < 7; j++) {
                    const cell = document.createElement('td');
                    if (i === 0 && j < startingDay) {
                      cell.textContent = '';
                    } else if (date > daysInMonth) {
                      cell.textContent = '';
                    } else {
                      cell.textContent = date;
                      if (year === today.getFullYear() && month === today.getMonth() && date === today.getDate()){
                        cell.classList.add('today');
                      }

                      // Format date for assessment lookup
                      const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;

                      if (assessmentsMap[formattedDate]) {
                        cell.classList.add('assessment');
                        cell.setAttribute('data-count', assessmentsMap[formattedDate]);
                      }

                      cell.setAttribute('data-year', year);
                      cell.setAttribute('data-month', month);
                      cell.setAttribute('data-date', date);

                      cell.addEventListener('click', (e) => {
                        const clickedCell = e.currentTarget;
                        const clickedYear = parseInt(clickedCell.getAttribute('data-year'));
                        const clickedMonth = parseInt(clickedCell.getAttribute('data-month'));
                        const clickedDate = parseInt(clickedCell.getAttribute('data-date'));
                        selectedDateObj = new Date(clickedYear, clickedMonth, clickedDate);
                        currentDateFilter.textContent = displayDate(selectedDateObj);
                        document.querySelectorAll('.calendar td.selected').forEach(el => el.classList.remove('selected'));
                        clickedCell.classList.add('selected');
                        console.log(clickedCell.classList);
                        updateAssessmentsList(clickedYear, clickedMonth, clickedDate);
                      });
                      date++;
                    }
                    row.appendChild(cell);
                  }
                  calendarBody.appendChild(row);
                }
              })
              .catch(error => {
                console.error('Error fetching assessment data for calendar:', error);
                generateCalendarWithoutAssessments(year, month, daysInMonth, startingDay, today);
              });
          }

          // Fallback calendar generation if fetching assessments fails
          function generateCalendarWithoutAssessments(year, month, daysInMonth, startingDay, today) {
            let date = 1;
            for (let i = 0; i < 6; i++) {
              if (date > daysInMonth) break;
              const row = document.createElement('tr');
              for (let j = 0; j < 7; j++) {
                const cell = document.createElement('td');
                if (i === 0 && j < startingDay) {
                  cell.textContent = '';
                } else if (date > daysInMonth) {
                  cell.textContent = '';
                } else {
                  cell.textContent = date;
                  if (year === today.getFullYear() && month === today.getMonth() && date === today.getDate()){
                    cell.classList.add('today');
                  }

                  cell.setAttribute('data-year', year);
                  cell.setAttribute('data-month', month);
                  cell.setAttribute('data-date', date);

                  cell.addEventListener('click', (e) => {
                    const clickedCell = e.currentTarget;
                    const clickedYear = parseInt(clickedCell.getAttribute('data-year'));
                    const clickedMonth = parseInt(clickedCell.getAttribute('data-month'));
                    const clickedDate = parseInt(clickedCell.getAttribute('data-date'));
                    selectedDateObj = new Date(clickedYear, clickedMonth, clickedDate);
                    currentDateFilter.textContent = displayDate(selectedDateObj);
                    document.querySelectorAll('.calendar td.selected').forEach(el => el.classList.remove('selected'));
                    clickedCell.classList.add('selected');
                    updateAssessmentsList(clickedYear, clickedMonth, clickedDate);
                  });
                  date++;
                }
                row.appendChild(cell);
              }
              calendarBody.appendChild(row);
            }
          }

          // Fetch assessments for selected date
          function updateAssessmentsList(year, month, date) {
            showLoading();
            const formattedDate = `${year}-${String(month+1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
            fetch(`/api/assessments/date/${formattedDate}?backend_guy_id=${currentLoggedInBackendGuyId}`)
              .then(response => response.json())
              .then(data => {
                if (data.length === 0) {
                  assessmentsList.innerHTML = `
                    <div class="empty-state">
                      <i class="fas fa-calendar-check"></i>
                      <p>No assessments scheduled for ${displayDate(new Date(year, month, date))}</p>
                    </div>
                  `;
                } else {
                  renderAssessmentsList(data);
                }
                hideLoading();
              })
              .catch(error => {
                console.error('Error fetching assessments:', error);
                assessmentsList.innerHTML = `
                  <div class="empty-state error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Failed to load assessment data</p>
                    <p>Please try again later</p>
                  </div>
                `;
                hideLoading();
              });
          }

          // Open modal for available time slots for the selected date
          function openModalForSelectedDate() {
            if (!selectedDateObj) {
              alert('Please select a date first.');
              return;
            }

            currentModalMode = 'view';
            const year = selectedDateObj.getFullYear();
            const month = selectedDateObj.getMonth();
            const date = selectedDateObj.getDate();
            const formattedDate = `${year}-${String(month+1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
            modalDateHeading.textContent = `Available Time Slots: ${displayDate(selectedDateObj)}`;
            modalContent.innerHTML = 'Loading...';
            showLoading();

            // Show the modal immediately so user sees something is happening
            modal.style.display = 'block';

            fetch(`/api/time-slots/date/${formattedDate}?backend_guy_id=${currentLoggedInBackendGuyId}`)
              .then(response => response.json())
              .then(data => {
                if (data.length === 0) {
                  modalContent.innerHTML = `
                    <div class="empty-state">
                      <i class="fas fa-clock"></i>
                      <p>No time slots available for this date</p>
                      <p>Add new time slots using the button below</p>
                    </div>
                  `;
                } else {
                  modalContent.innerHTML = `
                    <div class="time-slots-list">
                      ${data.map((slot, index) => `
                        <div class="time-slot-item" data-id="${slot.id}">
                          <div class="time-range">
                            <i class="fas fa-clock"></i>
                            ${slot.start_time || '--:--'}${slot.end_time ? ' - ' + slot.end_time : ''}
                          </div>
                          <div class="slot-status">
                            <span class="${slot.is_available ? 'status-available' : 'status-booked'}">
                              ${slot.is_available ? 'Available' : 'Unavailable'}
                            </span>
                          </div>
                          <div class="slot-actions">
                            <button class="edit-slot" data-id="${slot.id}">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete-slot" data-id="${slot.id}">
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      `).join('')}
                    </div>
                  `;

                  // Add event listeners to edit and delete buttons
                  document.querySelectorAll('.edit-slot').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                      const slotId = e.currentTarget.getAttribute('data-id');
                      editTimeSlot(slotId);
                    });
                  });

                  document.querySelectorAll('.delete-slot').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                      const slotId = e.currentTarget.getAttribute('data-id');
                      deleteTimeSlot(slotId);
                    });
                  });
                }

                // Reset modal state for view mode
                addTimeSlotBtn.style.display = 'block';
                closeModalBtn.textContent = 'Close';
                hideLoading();
              })
              .catch(error => {
                console.error('Error fetching time slots:', error);
                modalContent.innerHTML = `
                  <div class="empty-state error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Failed to load time slot data</p>
                    <p>Please try again later</p>
                  </div>
                `;
                hideLoading();
              });
          }

          // Function to edit a time slot
          async function editTimeSlot(slotId) {
            currentModalMode = 'edit';
            editingSlotId = slotId;
            modalDateHeading.textContent = `Edit Time Slot`;
            showLoading();

            try {
              // Fetch the time slot details
              const response = await fetch(`/api/time-slots/${slotId}`);
              if (!response.ok) {
                throw new Error('Failed to fetch time slot details');
              }

              const slot = await response.json();

              modalContent.innerHTML = `
                <form id="editSlotForm">
                  <div class="form-group">
                    <label for="editStartTime">Start Time</label>
                    <input type="time" id="editStartTime" name="start_time" value="${slot.start_time}" required>
                  </div>
                  ${slot.end_time ? `
                  <div class="form-group">
                    <label for="editEndTime">End Time</label>
                    <input type="time" id="editEndTime" name="end_time" value="${slot.end_time}">
                  </div>
                  ` : ''}
                  <div class="form-group">
                    <label for="editIsAvailable">Availability</label>
                    <select id="editIsAvailable" name="is_available">
                      <option value="1" ${slot.is_available ? 'selected' : ''}>Available</option>
                      <option value="0" ${!slot.is_available ? 'selected' : ''}>Not Available</option>
                    </select>
                  </div>
                  <button type="submit" class="modal-btn primary-btn">
                    <i class="fas fa-save"></i> Update Time Slot
                  </button>
                </form>
              `;

              // Update modal buttons for Edit mode
              addTimeSlotBtn.style.display = 'none';
              closeModalBtn.textContent = 'Cancel';

              document.getElementById('editSlotForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const data = {
                  backend_guy_id: currentLoggedInBackendGuyId,
                  start_time: formData.get('start_time'),
                  is_available: formData.get('is_available') === '1'
                };

                if (formData.get('end_time')) {
                  data.end_time = formData.get('end_time');
                }

                showLoading();

                try {
                  const response = await fetch(`/time-slots/${editingSlotId}`, {
                    method: 'PUT',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                  });

                  if (!response.ok) {
                    throw new Error('Failed to update time slot');
                  }

                  // Reset modal to show time slots
                  currentModalMode = 'view';
                  editingSlotId = null;
                  openModalForSelectedDate();
                } catch (error) {
                  console.error('Error updating time slot:', error);
                  alert('Failed to update time slot. Please try again.');
                  hideLoading();
                }
              });

              hideLoading();
            } catch (error) {
              console.error('Error preparing edit form:', error);
              modalContent.innerHTML = `
                <div class="empty-state error">
                  <i class="fas fa-exclamation-triangle"></i>
                  <p>Failed to load time slot details</p>
                  <p>Please try again later</p>
                </div>
              `;
              hideLoading();
            }
          }

          // Function to delete a time slot
          async function deleteTimeSlot(slotId) {
            if (confirm('Are you sure you want to delete this time slot?')) {
              showLoading();

              try {
                const response = await fetch(`/time-slots/${slotId}`, {
                  method: 'DELETE',
                  headers: {
                    'X-CSRF-TOKEN': csrfToken
                  }
                });

                if (!response.ok) {
                  throw new Error('Failed to delete time slot');
                }

                // Refresh the time slots list
                openModalForSelectedDate();
              } catch (error) {
                console.error('Error deleting time slot:', error);
                alert('Failed to delete time slot. Please try again.');
                hideLoading();
              }
            }
          }

          // Function to add a new time slot
          async function addNewTimeSlot() {
            if (!selectedDateObj) {
              alert('Please select a date first.');
              return;
            }

            currentModalMode = 'add';
            modalDateHeading.textContent = `Add New Time Slot: ${displayDate(selectedDateObj)}`;

            modalContent.innerHTML = `
              <form id="addSlotForm">
                <div class="form-group">
                  <label for="startTime">Start Time</label>
                  <input type="time" id="startTime" name="start_time" required>
                </div>
                <div class="form-group">
                  <label for="isAvailable">Availability</label>
                  <select id="isAvailable" name="is_available">
                    <option value="1" selected>Available</option>
                    <option value="0">Not Available</option>
                  </select>
                </div>
                <button type="submit" class="modal-btn primary-btn">
                  <i class="fas fa-check"></i> Save Time Slot
                </button>
              </form>
            `;

            // Update modal buttons for Add mode
            addTimeSlotBtn.style.display = 'none';
            closeModalBtn.textContent = 'Cancel';

            document.getElementById('addSlotForm').addEventListener('submit', async (e) => {
              e.preventDefault();
              const formData = new FormData(e.target);
              const data = {
                backend_guy_id: currentLoggedInBackendGuyId,
                date: formatDate(selectedDateObj),
                start_time: formData.get('start_time'),
                is_available: formData.get('is_available') === '1'
              };

              showLoading();

              try {
                const response = await fetch('/time-slots-add', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                  },
                  body: JSON.stringify(data)
                });

                if (!response.ok) {
                  throw new Error('Failed to add time slot');
                }

                // Reset modal to show time slots
                currentModalMode = 'view';
                openModalForSelectedDate();
              } catch (error) {
                console.error('Error adding time slot:', error);
                alert('Failed to add time slot. Please try again.');
                hideLoading();
              }
            });

            hideLoading();
          }

          // Reset to show all pending assessments for current backend guy
          function resetDateFilter() {
            currentDateFilter.textContent = 'All Pending';
            document.querySelectorAll('.calendar td.selected').forEach(el => el.classList.remove('selected'));
            selectedDateObj = null;
            updateAssessmentsListAll();
          }

          // Fetch and display pending assessments for current backend guy
          function updateAssessmentsListAll() {
            showLoading();
            fetch(`/api/assessments/pending?backend_guy_id=${currentLoggedInBackendGuyId}`)
              .then(response => {
                if (!response.ok) {
                  throw new Error('Failed to fetch assessments');
                }
                return response.json();
              })
              .then(data => {
                  //filter only for scheduled
                  console.log(data.pending_assessments);
                  data.scheduled_assessments = data.pending_assessments.filter(assessment => assessment.assessment_type === 'scheduled');
                  renderAssessmentsList(data.scheduled_assessments);
                  data.instant_assessments = data.pending_assessments.filter(assessment => assessment.assessment_type === 'instant');
                  renderInstantAssessmentsList(data.instant_assessments);

                hideLoading();
              })
              .catch(error => {
                console.error('Error fetching assessments:', error);
                assessmentsList.innerHTML = `
                  <div class="empty-state error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Failed to load assessment data</p>
                    <p>Please try again later</p>
                  </div>
                `;
                hideLoading();
              });
          }

          function renderInstantAssessmentsList(data){
            instantAssessmentsList.innerHTML = '';

            if(data.length === 0) {
              instantAssessmentsList.innerHTML = `
                <div class="empty-state">
                  <i class="fas fa-calendar-check"></i>
                  <p>No instant assessments scheduled currently.</p>
                </div>
              `;
              return;
            }

        data.forEach((schedule, index) => {
          const scheduleItem = document.createElement('div');
          scheduleItem.className = 'schedule-item';
          scheduleItem.style.animationDelay = `${index * 0.1}s`;

          scheduleItem.innerHTML = `
            <div class="schedule-bubble index">${schedule.id}</div>
            <div class="name-container">
              <div class="name">Instant Interview with ${schedule.user.name}</div>
              <div class="name-subtitle">
                <i class="fas fa-briefcase" style="font-size: 12px;"></i>
                ${schedule.user.experience_level}
              </div>
            </div>
            <div class="button join-btn" onClick="joinMeeting(${schedule.id})">
              <a onclick="joinMeeting(${schedule.id})">
                JOIN
              </a>
            </div>
          `;
          instantAssessmentsList.appendChild(scheduleItem);
        });
          }
          // Render the assessments list
      function renderAssessmentsList(data) {
        assessmentsList.innerHTML = '';
        const now = new Date();
        // const currentTime = now.toTimeString().slice(0,5);  // HH:MM format
        //get HH:MM:SS\
        const currentTime = now.toTimeString().slice(0,8);  // HH:MM:SS format
        const currentDate = now.toISOString().slice(0,10);  // YYYY-MM-DD format

        if(data.length === 0) {
          assessmentsList.innerHTML = `
                    <div class="empty-state">
                      <i class="fas fa-calendar-check"></i>
                      <p>No assessments scheduled currently.</p>
                    </div>
                  `;
          return;
        }

        data.forEach((schedule, index) => {
          const scheduleItem = document.createElement('div');
          scheduleItem.className = 'schedule-item';
          scheduleItem.style.animationDelay = `${index * 0.1}s`;

          // Determine if join button should be active, by checking if current date and time is less than the scheduled date and time
          const isJoinable = (schedule.time_slot.date === currentDate && schedule.time_slot.start_time < currentTime) || (schedule.time_slot.date < currentDate);

            console.log(schedule.time_slot.start_time +  ' ' + currentTime);

          scheduleItem.innerHTML = `
            <div class="schedule-bubble index">${schedule.id}</div>
            <div class="name-container">
              <div class="name">Interview with ${schedule.user.name}</div>
              <div class="name-subtitle">
                <i class="fas fa-briefcase" style="font-size: 12px;"></i>
                ${schedule.user.experience_level}
              </div>
            </div>
            <div class="schedule-bubble date">
              <i class="fas fa-calendar-alt icon"></i>
              ${schedule.time_slot.date}
            </div>
            <div class="schedule-bubble time">
              <i class="fas fa-clock icon"></i>
              ${schedule.time_slot.start_time || '<span class="pending-time">--:--</span>'}
            </div>
            <div class="button join-btn ${!isJoinable ? 'disabled' : ''}" onClick="${isJoinable ? `joinMeeting(${schedule.id})` : 'return false;'}">
              <a onclick="${isJoinable ? `joinMeeting(${schedule.id})` : 'return false;'}">
                JOIN
              </a>
            </div>
          `;
          assessmentsList.appendChild(scheduleItem);
        });
      }

          // Event Listeners
          showSlotsBtn.addEventListener('click', () => {
            openModalForSelectedDate();
          });

          closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
            // Reset modal state
            currentModalMode = 'view';
            editingSlotId = null;
            addTimeSlotBtn.style.display = 'block';
            closeModalBtn.textContent = 'Close';
          });

          closeModalBtn.addEventListener('click', () => {
            if (currentModalMode === 'view') {
              modal.style.display = 'none';
            } else if (currentModalMode === 'add' || currentModalMode === 'edit') {
              // If in add or edit mode, return to view mode
              currentModalMode = 'view';
              editingSlotId = null;
              openModalForSelectedDate();
            }
          });

          addTimeSlotBtn.addEventListener('click', () => {
            addNewTimeSlot();
          });

          window.addEventListener('click', (e) => {
            if (e.target === modal) {
              modal.style.display = 'none';
              // Reset modal state
              currentModalMode = 'view';
              editingSlotId = null;
              addTimeSlotBtn.style.display = 'block';
              closeModalBtn.textContent = 'Close';
            }
          });

          currentDateFilter.addEventListener('click', () => {
            resetDateFilter();
          });

          // Initialize the dashboard
          async function initDashboard() {
            showLoading();
            try {
              // Get the current backend guy ID
              currentLoggedInBackendGuyId = await getCurrentBackendGuyId();

              if (!currentLoggedInBackendGuyId) {
                throw new Error('Could not get current backend guy ID');
              }

              populateYearSelect();
              generateCalendar(currentYear, currentMonth);
              updateAssessmentsListAll();

              yearSelect.addEventListener('change', () => {
                currentYear = parseInt(yearSelect.value);
                generateCalendar(currentYear, currentMonth);
              });

              prevMonthBtn.addEventListener('click', () => {
                currentMonth--;
                if (currentMonth < 0) {
                  currentMonth = 11;
                  currentYear--;
                  yearSelect.value = currentYear;
                }
                generateCalendar(currentYear, currentMonth);
              });

              nextMonthBtn.addEventListener('click', () => {
                currentMonth++;
                if (currentMonth > 11) {
                  currentMonth = 0;
                  currentYear++;
                  yearSelect.value = currentYear;
                }
                generateCalendar(currentYear, currentMonth);
              });

              hideLoading();
            } catch (error) {
              console.error('Error initializing dashboard:', error);

              document.body.innerHTML = `
                <div class="empty-state error" style="height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                  <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 20px;"></i>
                  <p>Failed to initialize dashboard</p>
                  <p>Please try again later or contact support</p>
                  <button onclick="location.reload()" class="primary-btn" style="margin-top: 20px;">
                    <i class="fas fa-sync"></i> Retry
                  </button>
                </div>
              `;
              hideLoading();
              window.location.replace('/');
            }
          }
          initDashboard();
        });
  </script>
</body>
</html>
