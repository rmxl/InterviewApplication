<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="/agora-box-logo.svg" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <title>Video Interview with Rating Panel</title>
  <link rel="stylesheet" href="{{ asset('css/meet.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/agora-rtc-sdk-ng@4.23.2/AgoraRTC_N-production.min.js"></script>
</head>

<body>

  <div class="main-container">
    <div class="video-section">
      <div class="video-container">
        <div class="video-display">
          <div id="full-screen-video" class="placeholder-video"></div>
          <div id="remote-video-container"></div>
          <div id="local-video-container">
            <div id="local-video"></div>
          </div>
        </div>
      </div>

      <div class="controls-container">
        <div class="video-controls">
          <button class="control-button media-active" id="mic-toggle" title="Toggle Microphone">
            <i data-lucide="mic"></i>
          </button>
          <button class="control-button media-active" id="video-toggle" title="Toggle Camera">
            <i data-lucide="video"></i>
          </button>
        </div>

        <div class="bottom-leave-button-container">
          <div class="no-show-container">
            <input type="checkbox" id="no-show-checkbox" class="no-show-checkbox">
            <label for="no-show-checkbox" class="no-show-label">Mark as No Show</label>
          </div>
          <button class="bottom-leave-button" id="leave-channel">LEAVE MEETING</button>
        </div>
      </div>
    </div>

    <div class="question-panel">
      <div class="panel-header">
        Q U E S T I O N &nbsp; R A T I N G &nbsp; P A N E L
      </div>
      <div class="question-list" id="questionList">
        @foreach ($questions as $question)
      <div class="question-item" data-id="{{ $question['id'] }}">
        <div class="question-header">
        <div class="question-text">Q. {{ $question['text'] }}</div>
        <button class="question-toggle">
          <i class="fas fa-chevron-down"></i>
        </button>
        </div>
        <div class="answer-container">
        <div class="answer-text">{{ $question['body'] ?? 'No answer provided.' }}</div>
        </div>
        <div class="rating-container">
        <div class="rating-input">
          <div class="stars-container">
          <div class="stars-inner" style="width: {{ ($question['rating'] / 10) * 100 }}%"></div>
          </div>
          <input type="range" min="1" max="10" value="{{ $question['rating'] ?? 5 }}" class="rating-slider"
          id="rating-{{ $question['id'] }}">
          <div class="rating-value">{{ $question['rating'] ?? '?' }}/10</div>
        </div>
        </div>
      </div>
    @endforeach
      </div>
    </div>
  </div>

  <script>
    // Agora config
    const appid = '{{config('app.agora_app_id')}}';
    const HOST = '{{config('app.host')}}';

    const cameraVideoPreset = '360p_7'
    const audioConfigPreset = 'music_standard'

    // Create the Agora Client
    const client = AgoraRTC.createClient({
      codec: 'vp9',
      mode: 'live',
      role: 'host'
    })

    const localTracks = {
      camera: {
        audio: null,
        video: null
      },
      screen: {
        audio: null,
        video: null
      }
    }

    const localTrackActive = {
      audio: false,
      video: false,
      screen: false
    }

    let remoteUsers = {}                // Container for the remote streams
    let mainStreamUid = null            // Reference for video in the full screen view

    const Loglevel = {
      DEBUG: 0,
      INFO: 1,
      WARNING: 2,
      ERROR: 3,
      NONE: 4
    }

    AgoraRTC.enableLogUpload()                       // Auto upload logs to Agora
    AgoraRTC.setLogLevel(Loglevel.ERROR)             // Set Loglevel

    // helper function to quickly get dom elements
    function getById(divID) {
      return document.getElementById(divID)
    }

    // Function to add the recording button
    function addRecordingButton() {
      const controlsContainer = document.querySelector('.video-controls');

      const recordButton = document.createElement('button');
      recordButton.className = 'control-button';
      recordButton.id = 'record-toggle';
      recordButton.title = 'Toggle Recording';

      const recordIcon = document.createElement('i');
      recordIcon.setAttribute('data-lucide', 'video');
      recordButton.appendChild(recordIcon);

      controlsContainer.appendChild(recordButton);

      // Initialize Lucide icon for the new button
      lucide.createIcons();

      // Add event listener for the recording button
      recordButton.addEventListener('click', toggleRecording);
    }
    // Function to add the user info button
    function addUserInfoButton() {
      const controlsContainer = document.querySelector('.video-controls');

      const userInfoButton = document.createElement('button');
      userInfoButton.className = 'control-button';
      userInfoButton.id = 'user-info-btn';
      userInfoButton.title = 'View User Information';

      const userInfoIcon = document.createElement('i');
      userInfoIcon.setAttribute('data-lucide', 'user');
      userInfoButton.appendChild(userInfoIcon);

      controlsContainer.appendChild(userInfoButton);

      // Initialize Lucide icon for the new button
      lucide.createIcons();

      // Add event listener for the user info button
      userInfoButton.addEventListener('click', showUserInfo);
    }

    // Function to show user information
    // Function to show user information
    async function showUserInfo() {
      try {
        // Show loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'loading-indicator';
        loadingIndicator.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(loadingIndicator);

        // Get the assessment request ID from the URL parameter
        const url = new URL(window.location.href);
        const assessmentRequestId = url.searchParams.get('assessment_request_id');

        if (!assessmentRequestId) {
          throw new Error('Assessment request ID not found');
        }

        // First get the user ID associated with this assessment
        const assessmentResponse = await fetch(HOST + `/api/assessment-requests/${assessmentRequestId}`);

        if (!assessmentResponse.ok) {
          throw new Error('Failed to fetch assessment information');
        }

        const assessmentData = await assessmentResponse.json();
        const userId = assessmentData.user_id;

        if (!userId) {
          throw new Error('User ID not found for this assessment');
        }

        // Now fetch the user information using the route you've defined
        const userResponse = await fetch(HOST + `/api/user-info/${userId}`);

        if (!userResponse.ok) {
          throw new Error('Failed to fetch user information');
        }

        // Your API returns the user data directly, not wrapped in a 'user' object
        const user = await userResponse.json();
        console.log('User data:', user);

        // Remove loading indicator
        loadingIndicator.remove();

        // Create modal for user information
        const modal = document.createElement('div');
        modal.className = 'info-modal';
        modal.id = 'userInfoModal';

        // Determine job type display based on jobType_Id
        let jobType = user.job_title;
        modal.innerHTML = `
      <div class="info-modal-content">
        <div class="info-modal-header">
          <h3>User Information</h3>
          <button class="info-modal-close">&times;</button>
        </div>
        <div class="info-modal-body">
          <div class="info-item">
            <div class="info-label">Name:</div>
            <div class="info-value">${user.name || 'Not provided'}</div>
          </div>
          <div class="info-item">
            <div class="info-label">Username:</div>
            <div class="info-value">${user.username || 'Not provided'}</div>
          </div>
          <div class="info-item">
            <div class="info-label">Experience Level:</div>
            <div class="info-value">${user.experience_level || 'Not specified'}</div>
          </div>
          <div class="info-item">
            <div class="info-label">Job Type:</div>
            <div class="info-value">${jobType}</div>
          </div>
        </div>
      </div>
    `;

        document.body.appendChild(modal);

        // Add event listener to close button
        const closeButton = modal.querySelector('.info-modal-close');
        closeButton.addEventListener('click', () => {
          modal.remove();
        });

        // Close when clicking outside the modal content
        modal.addEventListener('click', function (event) {
          if (event.target === modal) {
            modal.remove();
          }
        });

      } catch (error) {
        console.error('Error showing user info:', error);

        // Remove loading indicator if it exists
        const loadingIndicator = document.querySelector('.loading-indicator');
        if (loadingIndicator) {
          loadingIndicator.remove();
        }

        // Show error message
        alert(`Failed to load user information: ${error.message}`);
      }
    }


    // Listen for page loaded event
    document.addEventListener('DOMContentLoaded', () => {
      console.log('page-loaded')
      lucide.createIcons();
      addAgoraEventListeners()                          // Add the Agora Event Listeners
      addLocalMediaControlListeners()                   // Add listeners to local media buttons
      addRecordingButton(); // Add the recording button
      addUserInfoButton();       // Add the user info button
      initializeAccordion(); // Initialize the accordion functionality
      handleJoin()
    })

    // Initialize accordion functionality
    function initializeAccordion() {
      const questionItems = document.querySelectorAll('.question-item');

      questionItems.forEach(item => {
        const toggleButton = item.querySelector('.question-toggle');
        const answerContainer = item.querySelector('.answer-container');

        toggleButton.addEventListener('click', (e) => {
          e.stopPropagation(); // Prevent the click from bubbling to the question item
          toggleButton.classList.toggle('active');
          answerContainer.classList.toggle('active');
        });

        // Allow clicking on the question header to toggle too
        const questionHeader = item.querySelector('.question-header');
        questionHeader.addEventListener('click', () => {
          toggleButton.classList.toggle('active');
          answerContainer.classList.toggle('active');
        });
      });
    }

    // User Form Submit Event
    const handleJoin = async () => {
      // stop the page from reloading
      event.preventDefault()
      // Get the channel name from the form input and remove any extra spaces
      // const channelName = getById('form-channel-name').value.trim()
      const channelName = "{{ $channel }}";

      console.log(`Joining channel: ${channelName}`)
      // Check if the channel name is empty
      if (!channelName || channelName === '') {
        // TODO: Add error message
        return
      }
      await initDevices()                                       // Initialize the devices and create Tracks

      // Join the channel and publish out streams
      const token = "{{ $token }}";                          // Token security is not enabled
      console.log(token);                                   // Token security is not enabled
      const uid = 0                                        // Pass null to have Agora set UID dynamically
      await client.join(appid, channelName, token)
      await client.publish([localTracks.camera.audio, localTracks.camera.video])
      // track audio state locally
      localTrackActive.audio = true
      localTrackActive.video = true
      // getById('controls-container').style.display = 'flex'   // show media controls (mic, video. screen-share, etc)
      document.querySelector('.controls-container').style.display = 'flex'
    }

    async function initDevices() {
      if (!localTracks.camera.audio || !localTracks.camera.video) {
        [localTracks.camera.audio, localTracks.camera.video] = await AgoraRTC.createMicrophoneAndCameraTracks({ audioConfig: audioConfigPreset, videoConfig: cameraVideoPreset })
      }

      const localVideoDiv = getById('local-video')

      const videoFromStream = document.createElement('video')
      videoFromStream.id = 'local-video-stream'
      videoFromStream.setAttribute('webkit-playsinline', 'webkit-playsinline');
      videoFromStream.setAttribute('playsinline', 'playsinline');

      videoFromStream.srcObject = new MediaStream([localTracks.camera.video.getMediaStreamTrack()])
      videoFromStream.controls = false
      videoFromStream.height = 300
      videoFromStream.width = 500
      localVideoDiv.appendChild(videoFromStream);

      videoFromStream.onloadedmetadata = () => {
        // ready to play video
        videoFromStream.play();
      }
    }

    // Add client Event Listeners -- on page load
    const addAgoraEventListeners = () => {
      // Add listeners for Agora Client Events
      client.on('user-joined', handleRemotUserJoined)
      client.on('user-left', handleRemotUserLeft)
      client.on('user-published', handleRemotUserPublished)
      client.on('user-unpublished', handleRemotUserUnpublished)
    }

    // New remote users joins the channel
    const handleRemotUserJoined = async (user) => {
      const uid = user.uid
      remoteUsers[uid] = user         // add the user to the remote users
      console.log(`User ${uid} joined the channel`)
    }

    // Remote user leaves the channel
    const handleRemotUserLeft = async (user, reason) => {
      const uid = user.uid
      delete remoteUsers[uid]
      console.log(`User ${uid} left the channel with reason:${reason}`)
    }

    // Remote user publishes a track (audio or video)
    const handleRemotUserPublished = async (user, mediaType) => {
      const uid = user.uid
      await client.subscribe(user, mediaType)
      remoteUsers[uid] = user                                  // update remote user reference
      if (mediaType === 'video') {
        // Check if the full screen view is empty
        if (mainIsEmpty()) {
          mainStreamUid = uid
          user.videoTrack.play('full-screen-video')           // play video on main user div
        } else {
          await createRemoteUserDiv(uid)                    // create remote user div
          user.videoTrack.play(`remote-user-${uid}-video`)   // play video on remote user div
        }
      } else if (mediaType === 'audio') {
        user.audioTrack.play()
      }
    }

    // Remote user unpublishes a track (audio or video)
    const handleRemotUserUnpublished = async (user, mediaType) => {
      const uid = user.uid
      console.log(`User ${uid} unpublished their ${mediaType}`)
      if (mediaType === 'video') {
        // Check if its the full screen user
        if (uid === mainStreamUid) {
          console.log(`User ${uid} is the main uid`)
          const newMainUid = getNewUidForMainUser()
          await setNewMainVideo(newMainUid)
        } else {
          await removeRemoteUserDiv(uid)
        }
      }
    }

    // Add button listeners
    const addLocalMediaControlListeners = () => {
      console.log('add-local-media-control-listeners')
      const micToggleBtn = getById('mic-toggle')
      const videoToggleBtn = getById('video-toggle')
      const leaveChannelBtn = getById('leave-channel')

      micToggleBtn.addEventListener('click', handleMicToggle)
      videoToggleBtn.addEventListener('click', handleVideoToggle)
      leaveChannelBtn.addEventListener('click', handleLeaveChannel)
    }

    const handleMicToggle = async (event) => {
      const button = event.currentTarget; // Use currentTarget instead of target to ensure we get the button
      const isTrackActive = localTrackActive.audio;

      try {
          await muteTrack(localTracks.camera.audio, isTrackActive, button);
          button.classList.toggle('media-active');
          localTrackActive.audio = !isTrackActive;
      } catch (error) {
          console.error('Error toggling microphone:', error);
      }
  }

  const handleVideoToggle = async (event) => {
      const button = event.currentTarget; // Use currentTarget instead of target
      const isTrackActive = localTrackActive.video;

      try {
          await muteTrack(localTracks.camera.video, isTrackActive, button);
          button.classList.toggle('media-active');
          localTrackActive.video = !isTrackActive;
      } catch (error) {
          console.error('Error toggling video:', error);
      }
  }

  const muteTrack = async (track, isActive, btn) => {
      if (!track) return;

      try {
          await track.setMuted(isActive);
      } catch (error) {
          console.error('Error muting track:', error);
          // Revert the button state if there's an error
          btn.classList.toggle('media-active');
      }
  }

    const handleLeaveChannel = async () => {
      try {
        // Show loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'loading-overlay';
        loadingDiv.innerHTML = '<div class="loading-spinner">Leaving meeting...</div>';
        document.body.appendChild(loadingDiv);

        // Stop recording if active
        if (isRecording) {
          await stopRecording();
        }

        // Close tracks and leave channel
        for (let trackName in localTracks.camera) {
          const track = localTracks.camera[trackName]
          if (track) {
            track.stop()
            track.close()
            localTracks.camera[trackName] = undefined
          }
        }

        await client.leave()
        console.log("Client left channel successfully")

        remoteUsers = {}
        for (const flag in localTrackActive) {
          localTrackActive[flag] = false
        }

        // Save ratings
        await saveRatingAndMarkDone();

        // Redirect to dashboard using absolute path
        window.location.href = "/dashboard";

      } catch (error) {
        console.error('Error leaving channel:', error);
        alert('An error occurred while leaving the meeting. Please try again.');
      }
    }

    // create the remote user container and video player div
    const createRemoteUserDiv = async (uid) => {
      const containerDivId = getById(`remote-user-${uid}-container`)
      if (containerDivId) return
      console.log(`add remote user div for uid: ${uid}`)
      // create a container for the remote video stream
      const containerDiv = document.createElement('div')
      containerDiv.id = `remote-user-${uid}-container`
      containerDiv.className = 'remote-user-container'
      // create a div to display the video track
      const remoteUserDiv = document.createElement('div')
      remoteUserDiv.id = `remote-user-${uid}-video`
      remoteUserDiv.classList.add('remote-video')
      containerDiv.appendChild(remoteUserDiv)
      // Add remote user to remote video container
      getById('remote-video-container').appendChild(containerDiv)

      // Listen for double click to swap container with main div
      containerDiv.addEventListener('dblclick', async (e) => {
        await swapMainVideo(uid)
      })
    }

    // Remove the div when users leave the channel
    const removeRemoteUserDiv = async (uid) => {
      const containerDiv = getById(`remote-user-${uid}-container`)
      if (containerDiv) {
        containerDiv.parentNode.removeChild(containerDiv)
      }
    }

    // check if the main-screen is empty
    const mainIsEmpty = () => {
      return getById('full-screen-video').childNodes.length === 0
    }

    const setNewMainVideo = async (newMainUid) => {
      if (!newMainUid) return                                        // Exit early if newMainUid is undefined
      await removeRemoteUserDiv(newMainUid)                          // remove the div from the remote user's container
      remoteUsers[newMainUid].videoTrack.play('full-screen-video')   // play the remote video in the full screen div
      mainStreamUid = newMainUid                                     // Set the new uid as the mainUid
      console.log(`newMainUid: ${newMainUid}`)
    }

    const swapMainVideo = async (newMainUid) => {
      const mainStreamUser = remoteUsers[mainStreamUid]
      if (mainStreamUser) {
        await createRemoteUserDiv(mainStreamUid)
        const videoTrack = remoteUsers[mainStreamUid].videoTrack
        // check if the video track is active
        if (videoTrack) {
          videoTrack.play(`remote-user-${mainStreamUid}-video`)
        }
      }
      await setNewMainVideo(newMainUid)
    }

    const getNewUidForMainUser = () => {
      const allUids = Object.keys(remoteUsers)
      if (allUids.length === 0) return undefined   // handle error-case
      // return a random uid
      const getRandomUid = () => {
        const randUid = allUids[Math.floor(Math.random() * allUids.length)]
        console.log(`randomUid: ${randUid}`)
        return randUid
      }
      // make sure the random Uid is not the main uid
      let newUid = getRandomUid()
      while (allUids.length > 1 && newUid == mainStreamUid) {
        newUid = getRandomUid()
      }

      return newUid
    }

    // Rating functionality from first UI
    document.addEventListener('DOMContentLoaded', function () {
      const questionItems = document.querySelectorAll('.question-item');

      questionItems.forEach(item => {
        const slider = item.querySelector('.rating-slider');
        const starsInner = item.querySelector('.stars-inner');
        const ratingValue = item.querySelector('.rating-value');
        const questionId = item.dataset.id;

        if (slider) {
          // Set initial state based on current value
          const initialValue = slider.value;
          updateRatingVisuals(starsInner, ratingValue, slider, initialValue);

          // Update visuals when slider changes
          slider.addEventListener('input', function () {
            const value = this.value;
            updateRatingVisuals(starsInner, ratingValue, slider, value);
          });
        }
      });
    });

    // Function to update the visual elements based on rating value
    function updateRatingVisuals(starsInner, ratingValue, slider, value) {
      const percentage = (value / 10) * 100;

      // Update rating bar width
      starsInner.style.width = `${percentage}%`;

      // Update rating text
      ratingValue.textContent = `${value}/10`;
      ratingValue.classList.add('rated');

      if (value > 4) {
        starsInner.classList.remove('poor');
        starsInner.classList.add('good');
        ratingValue.classList.add('good');
        slider.classList.add('good');
      } else {
        starsInner.classList.remove('good');
        starsInner.classList.add('poor');
        ratingValue.classList.remove('good');
        slider.classList.remove('good');
      }
    }

    // Placeholder for saving ratings
    async function saveRatingAndMarkDone() {
      //calculate average rating
      const questionItems = document.querySelectorAll('.question-item');
      const noShowCheckbox = document.getElementById('no-show-checkbox');
      const noShow = noShowCheckbox.checked;
      const showedUpValue = noShow ? 0 : 1;
      let totalRating = 0;
      let totalQuestions = 0;
      questionItems.forEach(item => {
        const slider = item.querySelector('.rating-slider');
        const questionId = item.dataset.id;
        const value = slider.value;
        totalRating += parseInt(value);
        totalQuestions++;
      });
      const averageRating = totalRating / totalQuestions;

      const res = await fetch("/api/save-rating", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          rating: averageRating,
          assessment_id: "{{ $assessment_request_id }}",
          showed_up: showedUpValue,
        })
      })

      if (res.ok) {
        console.log('Ratings saved successfully');
      } else {
        console.error('Error saving ratings');
      }

    }

    //// audio-video recording

    // Recording variables
    let mediaRecorder;
    let recordedChunks = [];
    let isRecording = false;

    // Function to toggle recording state
    async function toggleRecording() {
      const recordButton = getById('record-toggle');

      if (!isRecording) {
        // Start recording
        try {
          await startRecording();
          isRecording = true;
          recordButton.classList.add('recording');
          recordButton.classList.add('media-active');
          recordButton.title = 'Stop Recording';

          // Change icon to stop icon for better visual cue
          const icon = recordButton.querySelector('i');
          icon.setAttribute('data-lucide', 'square');
          lucide.createIcons();

        } catch (error) {
          console.error('Error starting recording:', error);
        }
      } else {
        // Stop recording
        try {
          await stopRecording();
          isRecording = false;
          recordButton.classList.remove('recording');
          recordButton.classList.remove('media-active');
          recordButton.title = 'Start Recording';

          // Change icon back to recording icon
          const icon = recordButton.querySelector('i');
          icon.setAttribute('data-lucide', 'video');
          lucide.createIcons();

        } catch (error) {
          console.error('Error stopping recording:', error);
        }
      }
    }

    // Function to start recording
    async function startRecording() {
      recordedChunks = [];

      // Create a composite stream of all participants
      const streams = [];

      if (localTracks.camera.audio && !localTracks.camera.audio.muted) {
        streams.push(localTracks.camera.audio.getMediaStreamTrack());
      }

      // Add remote users' video and audio tracks
      for (const uid in remoteUsers) {
        const user = remoteUsers[uid];

        if (user.videoTrack) {
          streams.push(user.videoTrack.getMediaStreamTrack());
        }

        if (user.audioTrack) {
          streams.push(user.audioTrack.getMediaStreamTrack());
        }
      }

      if (streams.length === 0) {
        console.error('No remote streams available to record');
        return Promise.reject('No remote streams available');
      }

      // Create a MediaStream from all tracks
      const compositeStream = new MediaStream(streams);

      // Create and initialize the MediaRecorder
      mediaRecorder = new MediaRecorder(compositeStream, {
        mimeType: 'video/webm;codecs=vp9,opus',
        videoBitsPerSecond: 2500000, // 2.5 Mbps
        audioBitsPerSecond: 128000   // 128 kbps
      });

      // Event handler for when data is available
      mediaRecorder.ondataavailable = (event) => {
        if (event.data.size > 0) {
          recordedChunks.push(event.data);
        }
      };

      // Event handler for when recording stops
      mediaRecorder.onstop = () => {
        // Create a blob from the recorded chunks
        const blob = new Blob(recordedChunks, {
          type: 'video/webm'
        });

        // Create a download link for the recording
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        document.body.appendChild(a);
        a.style = 'display: none';
        a.href = url;
        a.download = `interview-recording-${new Date().toISOString()}.webm`;

        // Trigger download
        a.click();

        // Clean up
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
      };

      // Start recording
      mediaRecorder.start();

      // Show recording indicator
      showRecordingIndicator(true);

      console.log('Recording started');
    }

    // Function to stop recording
    async function stopRecording() {
      if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
        showRecordingIndicator(false);
        console.log('Recording stopped');
      }
    }

    // Function to show/hide recording indicator
    function showRecordingIndicator(show) {
      // Check if indicator already exists
      let indicator = getById('recording-indicator');

      if (show) {
        if (!indicator) {
          // Create recording indicator
          indicator = document.createElement('div');
          indicator.id = 'recording-indicator';
          indicator.className = 'recording-indicator';
          indicator.innerHTML = `
        <div class="recording-dot"></div>
        <span>REC</span>
      `;

          // Add to video container
          getById('full-screen-video').appendChild(indicator);
        }
      } else if (indicator) {
        // Remove indicator
        indicator.remove();
      }
    }
  </script>
</body>

</html>
