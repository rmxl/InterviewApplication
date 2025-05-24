<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/agora-box-logo.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <title>Video Interview</title>
    <script src="https://cdn.jsdelivr.net/npm/agora-rtc-sdk-ng@4.23.2/AgoraRTC_N-production.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/usermeet.css')}}" />
  </head>
  <body>
    <div class="main-container">
      <!-- Video section -->
      <div class="video-section">
        <div id="full-screen-video"></div>
        <div id="local-video-container">
          <div id="local-video"></div>
        </div>
      </div>
      
      <!-- Controls container -->
      <div class="controls-container">
        <div class="video-controls">
          <button class="control-button media-active" id="mic-toggle" title="Toggle Microphone">
            <i data-lucide="mic"></i>
          </button>
          <button class="control-button media-active" id="video-toggle" title="Toggle Camera">
            <i data-lucide="video"></i>
          </button>
          <button class="control-button leave" id="leave-channel" title="Leave Meeting">
            <i data-lucide="phone-off"></i>
          </button>
        </div>
      </div>
    </div>

    <script>
      // Agora config
      const appid = "cd7e91e937a734615890a26f5c6a4277f";

      const cameraVideoPreset = '360p_7'          // 480 x 360p - 15fps @ 320 Kps
      const audioConfigPreset = 'music_standard'  // 48kHz mono @ 40 Kbps

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

      // Listen for page loaded event
      document.addEventListener('DOMContentLoaded', () => {
        console.log('page-loaded')
        lucide.createIcons();
        addAgoraEventListeners()                          // Add the Agora Event Listeners
        addLocalMediaControlListeners()                   // Add listeners to local media buttons
        handleJoin()
      })

      // User Form Submit Event
      const handleJoin = async () => {
        // Get the channel name from the form input and remove any extra spaces
        const channelName = "{{ $channel }}";
        
        console.log(`Joining channel: ${channelName}`)
        // Check if the channel name is empty  
        if (!channelName || channelName === '') {
          // TODO: Add error message
          return
        }
        await initDevices()                                       // Initialize the devices and create Tracks

        // Join the channel and publish out streams
        const token = "{{ $token }}"; 
        console.log(token);                                   // Token security is not enabled
        const uid = 1                                         // Pass null to have Agora set UID dynamically
        await client.join(appid, channelName, token)
        await client.publish([localTracks.camera.audio, localTracks.camera.video])
        // track audio state locally
        localTrackActive.audio = true
        localTrackActive.video = true
      }

      async function initDevices() {
        if (!localTracks.camera.audio || !localTracks.camera.video) {
          [ localTracks.camera.audio, localTracks.camera.video ] = await AgoraRTC.createMicrophoneAndCameraTracks({ audioConfig: audioConfigPreset, videoConfig: cameraVideoPreset })
        }
        
        const localVideoDiv = getById('local-video')
        
        const videoFromStream = document.createElement('video')
        videoFromStream.id = 'local-video-stream'
        videoFromStream.setAttribute('webkit-playsinline', 'webkit-playsinline');
        videoFromStream.setAttribute('playsinline', 'playsinline');
        
        videoFromStream.srcObject = new MediaStream([localTracks.camera.video.getMediaStreamTrack()])
        videoFromStream.controls = false
        videoFromStream.style.width = '100%'
        videoFromStream.style.height = '100%'
        videoFromStream.style.objectFit = 'cover'
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
        
        // If this user was displayed in main view, show "waiting" message
        if (uid === mainStreamUid) {
          mainStreamUid = null;
        }
      }

      // Remote user publishes a track (audio or video)
      const handleRemotUserPublished = async (user, mediaType) => {
        const uid = user.uid
        await client.subscribe(user, mediaType)
        remoteUsers[uid] = user                                  // update remote user reference
        if (mediaType === 'video') { 
          // Always display in main view for mobile
          mainStreamUid = uid;
          user.videoTrack.play('full-screen-video');
        } else if (mediaType === 'audio') {
          user.audioTrack.play()
        }
      }

      // Remote user unpublishes a track (audio or video)
      const handleRemotUserUnpublished = async (user, mediaType) => {
        const uid = user.uid
        console.log(`User ${uid} unpublished their ${mediaType}`)
        if (mediaType === 'video' && uid === mainStreamUid) {
          // Reset the main view
          mainStreamUid = null;
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
        const isTrackActive = localTrackActive.audio;
        const button = event.target.closest('button');
        button.classList.toggle('muted');
        button.classList.toggle('media-active');
        await muteTrack(localTracks.camera.audio, isTrackActive);
        localTrackActive.audio = !isTrackActive;
      }

      const handleVideoToggle = async (event) => {
        const isTrackActive = localTrackActive.video;
        const button = event.target.closest('button');
        button.classList.toggle('muted');
        button.classList.toggle('media-active');
        await muteTrack(localTracks.camera.video, isTrackActive);
        localTrackActive.video = !isTrackActive;
      }

      const muteTrack = async (track, isActive) => {
        if (!track) return;
        
        // Mute the track if it's currently active
        await track.setMuted(isActive);
      }

      const handleLeaveChannel = async () => {
        // loop through and stop the local tracks
        for (let trackName in localTracks.camera) {
          const track = localTracks.camera[trackName]
          if (track) {
            track.stop()
            track.close()
            localTracks.camera[trackName] = undefined
          }
        }
        // Leave the channel
        await client.leave()
        console.log("client left channel successfully")
        // Reset remote users 
        remoteUsers = {} 
        // reset the active flags
        for (const flag in localTrackActive){
          localTrackActive[flag] = false
        }

        window.close()
        // // window.location.href = "https://frenvitest.in/assessment-result";
        // const link = document.createElement('a');
        // link.href = "https://frenvitest.in/assessment-result";  
        // // link.style.display = 'none';   
        // //make link very visible
        // // const controlsContainer = getById('controls-container');
        // // link.style.position = 'absolute';
        // // link.style.color = 'red';   
        // // link.style.backgroundColor = 'yellow';
        // // link.style.fontSize = '30px';
        // // link.style.zIndex = '1000';
        // // link.style.top = '50%';
        // // link.style.left = '50%';
        // // controlsContainer.appendChild(link);
        // // link.innerHTML = "Click here to continue";

        // // link.click();                     
        // // document.body.removeChild(link);  
      }
    </script>
  </body>
</html>